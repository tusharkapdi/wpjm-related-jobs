<?php
/**
 * WP Job Manager Related Jobs
 *
 */

function wpjm_related_jobs_display($options=array()) {
    
    if(!count($options))
       $options=get_option('wpjm_related_jobs'); 
    
    $limit=$options['limit'];
    $columns=$options['columns'];
    $length=$options['length']; 

    include_once( WPJM_RELATED_PLUGIN_DIR . '/includes/wpse-or-query.php' );
    global $post;

    $jobmetas=get_post_meta( $post->ID);
    $contactname=(isset($jobmetas['_company_name'][0])!='')?$jobmetas['_company_name'][0]:'';
    
    $types = wp_get_post_terms( $post->ID, 'job_listing_type', array("fields" => "ids") );
    $args = array(
        'post_type' => 'job_listing',
        'post_status' => 'publish',
        'posts_per_page' => $limit,
        'orderby' => 'rand',
        'meta_or_tax' => true,
        'tax_query' => array(
            array(
                'taxonomy' => 'job_listing_type',
                'field' => 'id',
                'terms' => $types
            )
        ),
        'meta_query' => array(
            array(
                'key'     => '_company_name',
                'value'   => $contactname,
                'compare' => 'LIKE',
            ),
        ),
        'post__not_in' => array ($post->ID),
    );

    if($contactname=='')
        unset($args['meta_query']);

    $result='';
    // query
    $myposts = new WPSE_OR_Query( $args );
    $num_of_jobs = $myposts->post_count;
    if($num_of_jobs>0)
    {
        // container
        $result='<div class="relatedjobs"><div class="heading">'.__('Related Jobs', 'wpjmrelated').'</div><ul>';

        $count = 1;
        // begin loop
        while($myposts->have_posts()) : $myposts->the_post(); 

            $metas = get_post_meta( get_the_ID() );
            $types = wp_get_post_terms( get_the_ID(), 'job_listing_type', array("fields" => "names") );
            $company=(isset($metas['_company_name'][0])!='')?$metas['_company_name'][0]:'';
            $location=(isset($metas['_job_location'][0])!='')?$metas['_job_location'][0]:'';
            $content = strip_shortcodes(get_the_content());

            $result .= '<li class="col_'.$columns.'">
                <div class="title"><a href="'.get_the_permalink().'">'.get_the_title().'</a></div>
                <div class="comp">'.$company.'</div>
                <div class="jt"><span>'.current( $types ).'</span></div>
                <div class="loc"><span class="dashicons dashicons-location"></span>'.$location.'</div>
                <hr />
                <div class="cont">'.substr(strip_tags($content),0,$length);
                if(strlen($content)>$length){$result .= '<div class="fade-out"></div>';}
            $result .= '</div>
            </li>';
            
            // rinse and repeat
            $count++;

        endwhile;
        wp_reset_postdata();

        // container close
        $result.='</ul><div class="clear"></div></div>';
    }
    return $result;
}