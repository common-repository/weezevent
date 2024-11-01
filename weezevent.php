<?php
/**
 * Plugin Name: Weezevent
 * Plugin URI:        https://wordpress.org/plugins/weezevent/
 * Description:       Easily add your Weezevent Ticketing widget to sell tickets on your Wordpress website.
 * Version:           1.0.2
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Weezevent
 * Author URI:        https://weezevent.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       weezevent
 */
 
function wzvt_admin_css() {
    $admin_handle = 'admin_css';
    $admin_stylesheet = plugin_dir_url(__FILE__) . '/css/admin.css';

    wp_enqueue_style($admin_handle, $admin_stylesheet);
}
add_action('admin_print_styles', 'wzvt_admin_css');
 
// Hook for adding admin menus
add_action('admin_menu', 'wzvt_add_pages');

// action function for above hook
function wzvt_add_pages() {
    // Add a new top-level menu
    add_menu_page(
        __('Weezevent','weezevent'), //page_title
        __('Weezevent','weezevent'), //menu_title
        'manage_options', //capability
        'wz-top-level', //menu_slug
        'wzvt_settings_page', //function callback for rendering
        'dashicons-tickets-alt' // icon to display
    ); 

}

function wzvt_settings_page() {

    //must check that the user has the required capability 
    if (!current_user_can('manage_options'))
    {
      wp_die( __('You do not have sufficient permissions to access this page.', 'weezevent') );
    }

    // variables for the field and option names 
    $option_name_code = 'wzvt_code';
    $data_field_code = 'wzvt_code';
    $option_name_multi = 'wzvt_multi';
    $data_field_multi = 'wzvt_multi';
    
    $hidden_field_name = 'wzvt_submit_hidden';
    

    // Read in existing option value from database
    $option_val_code = get_option( $option_name_code );
    $option_val_multi = get_option( $option_name_multi );

    // See if the user has posted us some information
    // If they did, this hidden field will be set to 'Y'
    if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {
        if (
            ! isset( $_POST['wzvt_nonce_field'] )
            || ! wp_verify_nonce( $_POST['wzvt_nonce_field'], 'submit_new_code' )
        ) {
           wp_nonce_ays( '' );
        }
        
        // Read their posted value
        $option_val_code = wp_kses($_POST[ $data_field_code ], array('a' => array(
            'title' => array(),
            'href' => array(),
            'class' => array(),
            'target' => array(),
            'data-src' => array(),
            'data-width' => array(),
            'data-height' => array(),
            'data-id' => array(),
            'data-resize' => array(),
            'data-width_auto' => array(),
            'data-noscroll' => array(),
            'data-nopb' => array(),
            'data-type' => array()
            )));
        $option_val_multi = sanitize_text_field($_POST[ $data_field_multi ]);

        // Save the posted value in the database
        //update_option( $option_name_code, htmlentities($option_val_code) );
        update_option( $option_name_code, stripslashes(wp_filter_post_kses(addslashes($option_val_code))));
        update_option( $option_name_multi, $option_val_multi === "true" ? true : false );

        // Put a "settings saved" message on the screen

?>
<div class="updated"><p><strong><?php _e('Data saved.', 'weezevent' ); ?></strong></p></div>
<?php
    } //end of if
?>
    <?php

    // Now display the settings editing screen

    echo '<div class="wrap">';

    // header

    echo "<h2>" . __( 'Weezevent', 'weezevent' ) . "</h2>";

    // settings form
    
    ?>
    <div class="wz-card">
        <?php _e('This page allows you to set a default value for your ticketing module.', 'weezevent' ); ?><br/><br/>
        <?php _e('By adding a code on this page, it will be used as the default value on all the pages where you integrate the Weezevent module.You can however modify the code when creating / editing your pages.', 'weezevent' ); ?>
    </div>
    <div class="wz-card">
        <?php _e('Useful links:', 'weezevent' ); ?>
        <ul>
            <li><?php _e('Creation of an event module:', 'weezevent' ); ?> <a href="<?php _e('https://aide.weezevent.com/article/64-integrer-une-billetterie-sur-mon-site-web', 'weezevent' ); ?>" target="_blank"><?php _e('link', 'weezevent' ); ?></a></li>
            <li><?php _e('Creation of a multi-event module:', 'weezevent' ); ?> <a href="<?php _e('https://aide.weezevent.com/article/94-integrer-un-widget-multi-evenements-sur-mon-site-web', 'weezevent' ); ?>" target="_blank"><?php _e('link', 'weezevent' ); ?></a></li>
        </ul>
        
    </div>
    <div class="wz-card">
        <form name="wz-form" method="post" action="">
            <input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">
            <div class="form-field">
                <label for="<?php echo $data_field_code; ?>"><?php _e("Module code", 'weezevent' ); ?></label>
                <div class="helper">
                    <?php _e('Copy / paste the module code here. Don\'t panic if the "script" tag is missing, we\'ll add it on our side.', 'weezevent' ); ?>
                </div>
                
                <textarea name="<?php echo $data_field_code; ?>" id="<?php echo $data_field_code; ?>" rows="6"><?php echo esc_html(stripslashes($option_val_code)); ?></textarea>
                
                <script type="application/javascript">
                    const textarea = document.getElementById('<?php echo $data_field_code; ?>');
                    textarea.addEventListener("change", function(event) {
                        const data = event.target.value;
                        console.log('data', data);
                        const doc = new DOMParser().parseFromString(data, "text/html");
                        const links = doc.querySelectorAll("a");
            
                        if (links && links.length > 0){
                            textarea.value = links[0].outerHTML;
                            console.log('links[0].outerHTML', links[0].outerHTML);
                        } else {
                            textarea.value = "";
                        }
                        
                    });
                </script>
            </div>
            <div class="form-field">
            <div class="checkbox-block"><input type="checkbox" name="<?php echo $data_field_multi; ?>" id="<?php echo $data_field_multi; ?>" <?php echo ($option_val_multi === "true" ? 'checked' : '');?> value="true" /> <label for="<?php echo $data_field_multi; ?>"><?php _e("multi-event module", 'weezevent' ); ?></label></div>
            </div>
            <p>
                <?php wp_nonce_field( 'submit_new_code', 'wzvt_nonce_field' ); ?>
                <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save', 'weezevent') ?>" />
            </p>
        
        </form>
    </div>
</div>

<?php
 
}

// function that runs when shortcode is called
function wzvt_render_server($attributes) { 
    //$id_event = get_option('weez_event_id');
    $code = get_option('wzvt_code');
    $multi = get_option('wzvt_multi');
    
    if(isset($attributes) && isset($attributes['code'])){
        if(!empty($attributes['code'])){
            $code = $attributes['code'];
        }
        //$code = empty($attributes['code']) ? $attributes['code'] : $code;
        $multi = $attributes['multi'] || false;
    }
    
    $wzvt_script = $multi ? 'https://www.weezevent.com/js/widget/min/widget.min.js' : 'https://widget.weezevent.com/weez.js';
    
    wp_enqueue_script('wzvt_widget_script', $wzvt_script);

    // Output needs to be return
    return $code;
} 

// register shortcode
add_shortcode('weezevent', 'wzvt_render_server'); 

function wzvt_load_block() {
  wp_enqueue_script(
    'weezevent/weezevent-js',
    plugin_dir_url(__FILE__) . 'weezevent.js',
    array('wp-blocks','wp-editor'),
    true
  );
}
   
add_action('enqueue_block_editor_assets', 'wzvt_load_block');

register_block_type('weezevent/weezevent-block', array(
        'render_callback' => 'wzvt_render_server',
        'attributes' => array(
            'code' => array(
                'type' => 'string'
            ),
            'multi' => array(
                'type' => 'boolean'
            )
        )
    )
);

?>
