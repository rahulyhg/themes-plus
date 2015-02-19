<?php
/**
 * Plugin Name: them.es Plus
 * Plugin URI: https://wordpress.org/plugins/themes-plus
 * Description: "Short-code" your Bootstrap powered Theme and activate useful modules and features.
 * Version: 1.1.2
 * Author: them.es
 * Author URI: http://them.es
 * Text Domain: themes-plus
 * License: GPL version 2 or later - http://www.gnu.org/licenses/gpl-2.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// Initialize Addon
if ( !class_exists("themesPlus") ) {
	class themesPlus {
		function themesPlus() {
            
            // Init function
            function themesPlus_init() {
                
                wp_register_style( 'pluginstylesheet', plugins_url('style.css', __FILE__) );
                wp_enqueue_style( 'pluginstylesheet' ); // Load CSS
                
                add_editor_style( plugins_url('style-editor.css', __FILE__) ); // Style transformed Shortcodes in TinyMCE Editor
                
                wp_enqueue_style( 'dashicons' ); // Activate wp-internal dashicons webfont
                
            }
            add_action('init', 'themesPlus_init');
            
            
            /**
             * Only load Javascript if shortcode exists
             * Thanks https://pippinsplugins.com/load-scripts-if-post-has-short-code
             */
            function themesPlus_check_posts_for_shortcode($posts) {
                if ( empty($posts) ) {
                    return $posts;
                }
                
                $found = false;
                
                foreach ($posts as $post) {
                    if ( shortcode_exists( 'countup' ) && stripos($post->post_content, '[countup') ) {
                        $found = true;
                        break;
                    }
                }

                if ($found) {
                    wp_register_script( 'counttoinit', plugins_url( '/js/countto.min.js', __FILE__ ), array('jquery'), '1.0', false );
                    wp_enqueue_script( 'counttoinit' );
                }
                return $posts;
            }
            add_action('the_posts', 'themesPlus_check_posts_for_shortcode');
            
            
            function themesPlus_load_textdomain() {
                
                load_plugin_textdomain( 'themes-plus', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
                
            }
            add_action( 'plugins_loaded', 'themesPlus_load_textdomain' );
            
            
            /**
             * Init Shortcake Plugin: https://github.com/fusioneng/Shortcake
             * Plugin source has been added in /shortcake
             */
            // Include file
            $initshortcake = plugin_dir_path( __FILE__ ) . '/shortcake-plugin/shortcode-ui.php';
            
            if ( is_readable($initshortcake) ) {
                require_once($initshortcake);
            }
            
            
            /**
             * Add Dropdown Button to TinyMCE Editor
             */
            // Include file
            $tinymce_mod = plugin_dir_path( __FILE__ ) . '/inc/tinymce_mod.php';
            
            if ( is_readable($tinymce_mod) ) {
                require_once($tinymce_mod);
            }
            
            
            /**
             * Transform Standard Image Galleries
             */
            // Include file
            $gallery_mod = plugin_dir_path( __FILE__ ) . '/inc/gallery_mod.php';
            
            if ( is_readable($gallery_mod) ) {
                require_once($gallery_mod);
            }

            /**
             * Add a custom field "External Weblink" to Media files
             * Thanks: http://code.tutsplus.com/tutorials/creating-custom-fields-for-attachments-in-wordpress--net-13076
             */
            
            // 1. Media-Metabox function
            function themes_image_attachment_fields($form_fields, $post) {
                
                $form_fields["weblink"]["label"] = __( 'Weblink', 'themes-plus' );
                $form_fields["weblink"]["input"] = "text";
                $form_fields["weblink"]["value"] = get_post_meta($post->ID, "_weblink", true);

                return $form_fields;
                
            }
            add_filter('attachment_fields_to_edit', 'themes_image_attachment_fields', null, 2);

            // 2. Save function
            function themes_image_attachment_fields_to_save($post, $attachment) {
                
                if( isset($attachment['weblink']) ){
                    // update_post_meta(postID, meta_key, meta_value);
                    update_post_meta($post['ID'], '_weblink', $attachment['weblink']);
                }
                
                return $post;
                
            }
            add_filter('attachment_fields_to_save', 'themes_image_attachment_fields_to_save', null , 2);
            
            
            /**
             * Fix Shortcode markup
             * Thanks: https://gist.github.com/maxxscho/2058547
             */
            function themes_shortcode_fix_empty_paragraphs($content) {
                
                $array = array (
                    '<p>[' => '[',
                    ']</p>' => ']',
                    ']<br>' => ']',
                    ']<br/>' => ']',
                    ']<br />' => ']'
                );
                $content = strtr($content, $array);
                return $content;
                
            }
            add_filter('the_content', 'themes_shortcode_fix_empty_paragraphs');
	

        /**
         * Google Maps
         * 
         * Shortcodes:
         * [map] (Only working if latlng got defined in Customizer -> e.g. them.es Themes)
         * [map latlng="##.####,##.####" zoom="##" class="..." style="..."]
         */
            function themes_map_shortcode( $atts = array() ) {
                
                // Include file
                $file = plugin_dir_path( __FILE__ ) . "/inc/map.php";
                
                // Return Content from file
                if ( $file != 'NULL' && file_exists($file) ) {
                    
                    ob_start();
                    include( $file );
                    $content = ob_get_clean();
                    
                    return $content;
                    
                } else {
                    
                    return __( 'Error', 'themes-plus' );
                    
                }
            }
            add_shortcode( 'map', 'themes_map_shortcode' );
            
        /**
         * Register a TinyMCE UI for the Shortcode
         * External Plugin "Shortcode UI" required: https://github.com/fusioneng/Shortcake
         */
            if (function_exists('shortcode_ui_register_for_shortcode')) {
                shortcode_ui_register_for_shortcode(
                    'map',
                    array(
                        'label' => 'map',
                        'listItemImage' => 'dashicons-location',
                        'attrs' => array(
                            array(
                                'label'       => 'Lat,Lng',
                                'attr'        => 'latlng',
                                'type'        => 'text',
                                'placeholder' => '0.0000,0.0000',
                            ),
                            array(
                                'label'       => 'Zoom',
                                'attr'        => 'zoom',
                                'type'        => 'text',
                                'placeholder' => '',
                            ),
                            array(
                                'label'       => 'Marker (PNG/GIF/JPG, 128x128)',
                                'attr'        => 'marker',
                                'type'        => 'text',
                                'placeholder' => 'http://',
                            ),
                            array(
                                'label'       => 'Class',
                                'attr'        => 'class',
                                'type'        => 'text',
                                'placeholder' => '',
                            ),
                            array(
                                'label'       => 'CSS',
                                'attr'        => 'style',
                                'type'        => 'text',
                                'placeholder' => '',
                            ),
                        ),
                    )
                );
            }


        /**
         * Contact form
         * 
         * Shortcode:
         * [contactform]
         */
            function themes_contactform_shortcode( $atts = array() ) {
                
                // Include file
                $file = plugin_dir_path( __FILE__ ) . "/inc/contactform.php";
                
                // Return Content from file
                if ( $file != 'NULL' && file_exists($file) ) {
                    
                    ob_start();
                    include( $file );
                    $content = ob_get_clean();
                    
                    return $content;
                    
                } else {
                    
                    return __( 'Error', 'themes-plus' );
                    
                }
                
            }
            add_shortcode( 'contactform', 'themes_contactform_shortcode' );
            
         /**
         * Register a TinyMCE UI for the Shortcode
         * External Plugin "Shortcode UI" required: https://github.com/fusioneng/Shortcake
         */
            if (function_exists('shortcode_ui_register_for_shortcode')) {
                shortcode_ui_register_for_shortcode(
                    'contactform',
                    array(
                        'label' => 'contactform',
                        'listItemImage' => 'dashicons-email-alt',
                        'attrs' => array(
                            array(
                                'label'       => 'Class',
                                'attr'        => 'class',
                                'type'        => 'text',
                                'placeholder' => '',
                            ),
                            array(
                                'label'       => 'CSS',
                                'attr'        => 'style',
                                'type'        => 'text',
                                'placeholder' => '',
                            ),
                        ),
                    )
                );
            }


        /**
         * Recent posts
         * 
         * Shortcode:
         * [recentposts] or [recentposts posts="10"]
         */
            function themes_recentposts_shortcode( $atts = array() ) {
                
                // Get Attributes
                extract(shortcode_atts(array(
                    'posts' => '5'
                ), $atts));
                
                // Return Content
                $content = '<ul class="recentposts">';
                $content .= '<li><h3>' . __('Recent Posts', 'themes-plus') . '</h3></li>';
                    $recentposts_query = new WP_Query( "posts_per_page=$posts" );// $posts = number of posts (default = 5)
                    $month_check = null;
                    if ( $recentposts_query->have_posts() ) : while ( $recentposts_query->have_posts() ) : $recentposts_query->the_post();
                        $content .= '<li>';
                            // Show monthly archive and link to months
                            $month = get_the_date('F, Y');
                            if ($month !== $month_check) : $content .= '<p><a href="' . get_month_link( get_the_date('Y'), get_the_date('m') ) . '" title="' . get_the_date('F, Y') . '">' . $month . '</a></p>'; endif;
                            $month_check = $month;
                        $content .= '<h4><a href="' . get_the_permalink() . '" title="' . sprintf( __('Permalink to %s', 'themes-plus'), the_title_attribute('echo=0') ) . '" rel="bookmark">' . get_the_title() . '</a></h4>';
                        $content .= '</li>';
                    endwhile; endif; wp_reset_postdata(); // end of the loop.
                $content .= '</ul>';
                //$content = ob_get_clean();
                
                return $content;
                
            }
            add_shortcode( 'recentposts', 'themes_recentposts_shortcode' );
            
        /**
         * Register a TinyMCE UI for the Shortcode
         * External Plugin "Shortcode UI" required: https://github.com/fusioneng/Shortcake
         */
            if (function_exists('shortcode_ui_register_for_shortcode')) {
                shortcode_ui_register_for_shortcode(
                    'recentposts',
                    array(
                        'label' => 'recentposts',
                        //'listItemImage' => 'dashicons-editor-quote',
                        'attrs' => array(
                            array(
                                'label'       => 'Number of Posts',
                                'attr'        => 'number',
                                'type'        => 'text',
                                'placeholder' => '5',
                            ),
                        ),
                    )
                );
            }
            
            
        /**
         * Stats Counter: JQuery Plugin + Code initialized in "themesPlus_init"
         * 
         * Shortcode:
         * [countup]###[/countup]
         */

            // Number: [countup]###[/countup]
            function themes_countup_shortcode( $atts = array(), $content = null ) {
                
                return '<h3 class="countup h1" data-to="' . do_shortcode( shortcode_unautop( $content ) ) .'" data-speed="2500">' . do_shortcode( shortcode_unautop( $content ) ) .'</h3>'; // If $content contains a shortcode, that code will get processed
                
            }
            add_shortcode( 'countup', 'themes_countup_shortcode' );
            
        /**
         * Register a TinyMCE UI for the Shortcode
         * External Plugin "Shortcode UI" required: https://github.com/fusioneng/Shortcake
         */
            if (function_exists('shortcode_ui_register_for_shortcode')) {
                shortcode_ui_register_for_shortcode(
                    'countup',
                    array(
                        'label' => 'Number',
                        //'listItemImage' => 'dashicons-editor-quote',
                        'attrs' => array(
                            array(
                                'label'       => 'Content',
                                'attr'        => 'content',
                                'type'        => 'text',
                                'placeholder' => '100',
                            ),
                        ),
                    )
                );
            }
            
            
        /**
         * Content Carousel
         * 
         * Shortcode:
         * [carousel]
         *   [item]Lorem ipsum...[/item]
         *   [item]Lorem ipsum...[/item]
         * [/carousel]
         */
            function themes_carousel_shortcode( $atts = array(), $content = null ) {
                
                // Get Attributes
                extract(shortcode_atts(array(
                    'class' => '',
                    'style' => ''
                ), $atts));
                
                $GLOBALS['carouselitem_count'] = 0;
                
                return '<div id="slider" class="carousel slide ' . ( $class ? ' ' . $class : '' ) . '"' . ( $style ? ' style="' . $style . '"' : '' ) . ' data-interval="false"><div class="carousel-inner" role="listbox">' . do_shortcode($content) . '</div> <a class="left carousel-control" href="#slider" role="button" data-slide="prev"><span class="dashicons dashicons-arrow-left-alt2"></span></a> <a class="right carousel-control" href="#slider" role="button" data-slide="next"><span class="dashicons dashicons-arrow-right-alt2"></span></a></div>'; // If $content contains a shortcode, that code will get processed. Dashicons need to be activated via wp_enqueue_style( 'dashicons' );
                
            }
            add_shortcode( 'carousel', 'themes_carousel_shortcode' );
            
            // Carousel-items: [item]...[/item]
            function themes_carouselitem_shortcode( $atts = array(), $content = null ) {
                
                // Get Attributes
                extract(shortcode_atts(array(
                    'class' => '',
                    'style' => ''
                ), $atts));
                
                if( isset($GLOBALS['carouselitem_count']) ) {
                    
                    if( $GLOBALS['carouselitem_count'] == 0 ) {
                        $class .= " active"; // first item
                    }
                    
                    $GLOBALS['carouselitem_count']++;
                    
                }
                
                return '<div class="item' . ( $class ? ' ' . $class : '' ) . '"' . ( $style ? ' style="' . $style . '"' : '' ) . '>' . do_shortcode( shortcode_unautop( $content ) ) . '</div>';
                
            }
            add_shortcode( 'item', 'themes_carouselitem_shortcode' );
            
        /**
         * Register a TinyMCE UI for the Shortcode
         * External Plugin "Shortcode UI" required: https://github.com/fusioneng/Shortcake
         */
            if (function_exists('shortcode_ui_register_for_shortcode')) {
                shortcode_ui_register_for_shortcode(
                    'item',
                    array(
                        'label' => 'item',
                        //'listItemImage' => 'dashicons-editor-quote',
                        'attrs' => array(
                            array(
                                'label'       => 'Content',
                                'attr'        => 'content',
                                'type'        => 'textarea',
                                'placeholder' => 'Lorem ipsum dolor sit amet...',
                            ),
                            array(
                                'label'       => 'Class',
                                'attr'        => 'class',
                                'type'        => 'text',
                                'placeholder' => '',
                            ),
                            array(
                                'label'       => 'CSS',
                                'attr'        => 'style',
                                'type'        => 'text',
                                'placeholder' => '',
                            ),
                        ),
                    )
                );
            }
            

        /**
         * Grid
         * 
         * Shortcode:
         * [row]
         *   [col]Lorem ipsum...[/col]
         *   [col]Lorem ipsum...[/col]
         * [/row]
         */
            function themes_row_shortcode( $atts = array(), $content = null ) {
                
                // Get Attributes
                extract(shortcode_atts(array(
                    'class' => '',
                    'style' => ''
                ), $atts));
                
                $GLOBALS['colitem_count'] = substr_count( $content, '[/col]' ); // Count number of closing cols
                
                return '<div class="content-grid row' . ( $class ? ' ' . $class : '' ) . '"' . ( $style ? ' style="' . $style . '"' : '' ) . '>' . do_shortcode( shortcode_unautop( $content ) ) . '</div>'; // If $content contains a shortcode, that code will get processed
                
            }
            add_shortcode( 'row', 'themes_row_shortcode' );

            // Col Item(s): [col]...[/col]
            function themes_col_shortcode( $atts = array(), $content = null ) {
                
                // Get Attributes
                extract(shortcode_atts(array(
                    'class' => '',
                    'style' => ''
                ), $atts));
                
                if ( isset( $GLOBALS['colitem_count'] ) ) {
                    $colcounter = $GLOBALS['colitem_count'];
                    
                    if ( $colcounter == 2 ) {
                        $bootstrap = 'col-lg-6 col-md-6 col-sm-12 col-xs-12'; // 2 cols
                    } else if ( $colcounter == 3 ) {
                        $bootstrap = 'col-lg-4 col-md-4 col-sm-6 col-xs-12'; // 3 cols
                    } else if ( $colcounter == 4 ) {
                        $bootstrap = 'col-lg-3 col-md-3 col-sm-6 col-xs-12'; // 4 cols
                    } else if ( $colcounter == 6 ) {
                        $bootstrap = 'col-lg-2 col-md-2 col-sm-6 col-xs-12'; // 6 cols
                    }
                    
                } else {
                    
                    $bootstrap = 'col-lg-12'; // 1 col
                    
                }
                
                return '<div class="' . $bootstrap . ( $class ? ' ' . $class : '' ) . '"' . ( $style ? ' style="' . $style . '"' : '' ) . '>' . do_shortcode( shortcode_unautop( $content ) ) . '</div>'; // If $content contains a shortcode, that code will get processed
                
            }
            add_shortcode( 'col', 'themes_col_shortcode' );
            
        /**
         * Register a TinyMCE UI for the Shortcode
         * External Plugin "Shortcode UI" required: https://github.com/fusioneng/Shortcake
         */
            if (function_exists('shortcode_ui_register_for_shortcode')) {
                shortcode_ui_register_for_shortcode(
                    'col',
                    array(
                        'label' => 'col',
                        //'listItemImage' => 'dashicons-editor-quote',
                        'attrs' => array(
                            array(
                                'label'       => 'Content',
                                'attr'        => 'content',
                                'type'        => 'textarea',
                                'placeholder' => 'Lorem ipsum dolor sit amet...',
                            ),
                            array(
                                'label'       => 'Class',
                                'attr'        => 'class',
                                'type'        => 'text',
                                'placeholder' => '',
                            ),
                            array(
                                'label'       => 'CSS',
                                'attr'        => 'style',
                                'type'        => 'text',
                                'placeholder' => '',
                            ),
                        ),
                    )
                );
            }

            
		}

	}
}

if ( class_exists("themesPlus") ) {
	$themesPlus = new themesPlus();
}

if ( !function_exists('_log') ){
  function _log( $message ) {
    if ( WP_DEBUG === true ) {
      if ( is_array( $message ) || is_object( $message ) ){
        error_log( print_r( $message, true ) );
      } else {
        error_log( $message );
      }
    }
  }
}

?>