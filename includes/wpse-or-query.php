<?php 
 /**
 * Class WPSE_OR_Query 
 * 
 * Add a support for (tax_query OR meta_query) 
 *
 * @version 0.2
 * @link http://stackoverflow.com/a/22633399/2078474
 *
 */
if(!class_exists('WPSE_OR_Query'))
{
    class WPSE_OR_Query extends WP_Query 
    {       
        protected $meta_or_tax  = FALSE;
        protected $tax_args     = NULL;
        protected $meta_args    = NULL;

        public function __construct( $args = array() )
        {
            add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ), 999 );
            add_filter( 'posts_where',   array( $this, 'posts_where' ),   999 );
            parent::__construct( $args );
        }
        public function pre_get_posts( $qry )
        {       
            remove_action( current_filter(), array( $this, __FUNCTION__ ) );            
            // Get query vars
            $this->meta_or_tax = ( isset( $qry->query_vars['meta_or_tax'] ) ) ? $qry->query_vars['meta_or_tax'] : FALSE;
            if( $this->meta_or_tax )
            { 
                $this->tax_args  = ( isset( $qry->query_vars['tax_query'] ) )  ? $qry->query_vars['tax_query']  : NULL;
                $this->meta_args = ( isset( $qry->query_vars['meta_query'] ) ) ? $qry->query_vars['meta_query'] : NULL;
            }
        }
        public function posts_where( $where )
        {       
            global $wpdb;       
            $field = 'ID';
            remove_filter( current_filter(), array( $this, __FUNCTION__ ) );    

            // Construct the "tax OR meta" query
            if( $this->meta_or_tax && is_array( $this->tax_args ) &&  is_array( $this->meta_args )  )
            {
                // Tax query
                $sql_tax = get_tax_sql( $this->tax_args, $wpdb->posts, $field );            

                // Meta query
                $sql_meta = get_meta_sql( $this->meta_args, 'post', $wpdb->posts, $field );

                // Modify the 'where' part          
                if( isset( $sql_meta['where'] ) && isset( $sql_tax['where'] ) )
                {
                    $where  = str_replace( array( $sql_meta['where'], $sql_tax['where'] ) , '', $where );
                    $where .= sprintf( ' AND ( %s OR  %s ) ', substr( trim( $sql_meta['where'] ), 4 ), substr( trim( $sql_tax['where'] ), 4 ) );              
                }
            }   
            return $where;
        }
    }
}
?>