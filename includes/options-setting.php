<?php
class wpjm_related_jobs_options_page {

    function __construct() {
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
    }

    function admin_menu() {
        add_options_page(
            'WP Job Manager Related Job',
            'Related Jobs',
            'manage_options',
            'wpjm-realted-jobs-setting',
            array(
                $this,
                'settings_page'
            )
        );
    }

    public function settings_page() {
        if ( ! current_user_can( 'manage_options' ) )  {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }
        
        if ( ! empty( $_POST ) && check_admin_referer( 'wpjmrelatedjobs', 'save_wpjmrelatedjobs' ) ) {
            //add or update page transition options
            $wpjm_related_jobs = array(
                'is_job_detail' => $_POST['is_job_detail'],
                'position' => $_POST['position'],
                'length' => $_POST['length'],
                'limit' => $_POST['limit'],
                'columns' => $_POST['columns']
                );
            update_option( 'wpjm_related_jobs', $wpjm_related_jobs );

            wp_redirect( admin_url( 'options-general.php?page=wpjm-realted-jobs-setting&updated=1' ) );
        }

        $options=get_option('wpjm_related_jobs');
        ?>
        <div class="wrap">
            <h2><?php _e( 'WP Job Manager Related Job Setting', 'wpjmrelated' );?></h2>

            <div id="poststuff">
                <div id="post-body" class="metabox-holder columns-2">
                    <div id="post-body-content">
            <form method="post" action="<?php echo esc_url( admin_url( 'options-general.php?page=wpjm-realted-jobs-setting&noheader=true' ) ); ?>">
                <?php wp_nonce_field( 'wpjmrelatedjobs', 'save_wpjmrelatedjobs' ); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="is_job_detail"><?php _e( 'Show in job detail page?', 'wpjmrelated' );?></label></th>
                        <td><select name="is_job_detail" id="is_job_detail" onchange="if(jQuery(this).val()==1){jQuery('.showhidetr').show('slow');}else{jQuery('.showhidetr').hide('slow');}">
                            <option value="1"<?php esc_attr_e( 1 == $options['is_job_detail'] ? ' selected=selected' : '' ); ?>><?php _e( 'Yes', 'wpjmrelated' );?></option>
                            <option value="0"<?php esc_attr_e( 0 == $options['is_job_detail'] ? ' selected=selected' : '' ); ?>><?php _e( 'No', 'wpjmrelated' );?></option>
                        </select></td>
                    </tr>
                    <?php $style = ''; if($options['is_job_detail']==0){$style = 'style="display:none"';}?>
                    <tr class="showhidetr" <?php echo $style; ?>>
                        <th scope="row"><label for="position"><?php _e( 'Position', 'wpjmrelated' );?></th>
                        <td><select name="position" id="position">
                            <option value="0"<?php esc_attr_e( 0 == $options['position'] ? ' selected=selected' : '' ); ?>><?php _e( 'After Content', 'wpjmrelated' );?></option>
                            <option value="1"<?php esc_attr_e( 1 == $options['position'] ? ' selected=selected' : '' ); ?>><?php _e( 'Before Content', 'wpjmrelated' );?></option>
                        </select></td>
                    </tr>
                    <tr class="showhidetr" <?php echo $style; ?>>
                        <th scope="row"><label for="length"><?php _e( 'Description Character Limit', 'wpjmrelated' );?></label></th>
                        <td><input type="number" id="length" name="length" style="width:70px;" value="<?php echo $options['length']; ?>"  /></td>
                    </tr>
                    <tr class="showhidetr" <?php echo $style; ?>>
                        <th scope="row"><label for="limit"><?php _e( 'Number of Jobs', 'wpjmrelated' );?></label></th>
                        <td><input type="number" id="limit" name="limit" style="width:70px;" min="1" value="<?php echo $options['limit']; ?>"  /></td>
                    </tr>
                    <tr class="showhidetr" <?php echo $style; ?>>
                        <th scope="row"><label for="columns"><?php _e( 'Number of Columns', 'wpjmrelated' );?></label></th>
                        <td><input type="number" id="columns" name="columns" style="width:70px;" min="1" max="6" value="<?php echo $options['columns']; ?>"  /></td>
                    </tr>
                </table>
                <p class="submit">
                    <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes' ) ?>" />
                </p>
            </form>
            <p><code>[WPJM_RELATED_JOBS limit="3" columns="3" length="250"]</code></p>
            <p>
                <a href="http://wordpress.org/plugins/wpjm-related-jobs/" target="_blank"><img src="<?php echo WPJM_RELATED_PLUGIN_URI; ?>/assets/screenshot-2.png" width="100%" /></a>
            </p>
        </div>
            <div id="postbox-container-1" class="postbox-container">
                <div class="postbox">
                    <h3 class="hndle ui-sortable-handle"><span><?php _e( 'Donate', 'wpjmrelated' );?></span></h3>
                    <div class="inside">
                        <p>Enjoy this plugin? Please <a href="http://amplebrain.com/donate/" target="_blank">donate to support development</a></p>
                    </div>
                </div>
                <div class="postbox">
                    <h3 class="hndle ui-sortable-handle"><span><?php _e( 'Rate Us', 'wpjmrelated' );?></span></h3>
                    <div class="inside">
                        <p><a href="http://wordpress.org/plugins/wpjm-related-jobs/" class="add-new-h2" target="_blank"><span class="dashicons dashicons-star-filled"></span> <?php _e( 'Rate Us On Wordpress', 'wpjmrelated' );?></a></p>
                    </div>
                </div>
                <div class="postbox">
                    <h3 class="hndle ui-sortable-handle"><span><?php _e( 'Customize You Job Portal', 'wpjmrelated' );?></span></h3>
                    <div class="inside">
                        <p>
                            <div class="wri_admin_left_sidebar">
                                
                                <a target="_blank" class="wli_pro" href="http://amplebrain.com/request-a-quote/">
                                    <img alt="Customize You Job Portal" src="<?php echo WPJM_RELATED_PLUGIN_URI; ?>/assets/wpjmrelatedjobs.jpg">
                                </a>
                    
                                <?php _e( 'Customize features', 'wpjmrelated' );?>:
                                <ol>
                                    <li><?php _e( 'Addon configuration and customization', 'wpjmrelated' );?></li>
                                    <li><?php _e( 'Customize job page layout', 'wpjmrelated' );?></li>
                                    <li><?php _e( 'Search Customization', 'wpjmrelated' );?></li>
                                    <li><?php _e( 'Customize job fields', 'wpjmrelated' );?></li>
                                    <li><?php _e( 'More..', 'wpjmrelated' );?></li>
                                </ul>
                            </div>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
new wpjm_related_jobs_options_page;
?>