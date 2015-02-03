<?php
    
    // TinyMCE hook
    function themes_tinymce_button() {
        global $typenow;
        
        // Check permissions
        if ( !current_user_can('edit_posts') && !current_user_can('edit_pages') ) {
            return;
        }
        // Verify Post type
        if( !in_array( $typenow, array( 'post', 'page' ) ) )
            return;
        
        // Check if Richtext Editing is enabled
        if ( get_user_option('rich_editing') == 'true') {
            add_filter('mce_external_plugins', 'themes_add_tinymce_plugin');
            add_filter('mce_buttons', 'themes_register_tinymce_button');
        }
    }
    add_action('admin_head', 'themes_tinymce_button');

    function themes_add_tinymce_plugin($plugin_array) {
        $plugin_array['themes_tinymce_button'] = plugins_url( '/js/tinymce-plugin.js', dirname(__FILE__) );
        return $plugin_array;
    }

    function themes_register_tinymce_button($buttons) {
       array_push($buttons, "themes_tinymce_button");
       return $buttons;
    }
    
    // Future: Custom Styling of Shortcodes Output...
    add_action('print_media_templates', function() {
        if ( ! isset( get_current_screen()->id ) || get_current_screen()->base != 'post' )
            return;
        ?>
?>
        <script type="text/html" id="tmpl-custom-shortcodes">
            
        </script>
<?php
    });

?>