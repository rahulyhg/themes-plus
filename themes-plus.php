<?php
/**
 * Plugin Name: them.es Plus
 * Plugin URI: https://wordpress.org/plugins/themes-plus
 * Description: "Short-code" your Bootstrap powered Theme and activate useful modules and features.
 * Version: 1.1.7
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
            
            
            function themesPlus_load_textdomain() {
                
                load_plugin_textdomain( 'themes-plus', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
                
            }
            add_action( 'plugins_loaded', 'themesPlus_load_textdomain' );
            
            
            /**
             * Init Shortcake Plugin: https://github.com/fusioneng/Shortcake
             * Plugin source has been added in /shortcake
             */
			
            $initshortcake = plugin_dir_path( __FILE__ ) . '/inc/shortcake/shortcode-ui.php';
			
            // Include Shortcake if not loaded (by other Plugin) already
            if ( !class_exists( 'Shortcode_UI' ) && is_readable($initshortcake) ) {
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
             * Customizer API: Google Analytics, Map Styles, ...
             */
            // Include file
            $plugin_customizer = plugin_dir_path( __FILE__ ) . '/inc/customizer.php';
            
            if ( is_readable($plugin_customizer) ) {
                require_once($plugin_customizer);
            }
            
			
			/**
			 * Google Analytics
			 */
			global $ga_trackingcode;
			$ga_trackingcode = trim( get_theme_mod('themes_plus_google_analytics') ); // see customizer.php
			function themes_plus_add_googleanalytics() {
				global $ga_trackingcode;
				echo "
				<script>
				(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
					(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
					m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
					})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
					ga('create', '" . $ga_trackingcode . "', 'auto');
					ga('send', 'pageview');
				</script>
				";
			}
			if( isset($ga_trackingcode) && $ga_trackingcode != "" ) {
				add_action('wp_footer', 'themes_plus_add_googleanalytics', 100);
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
                                'type'        => 'number',
                                'placeholder' => '14',
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
                                'label'       => 'Email',
                                'attr'        => 'email',
                                'type'        => 'text',
                                'placeholder' => 'mail@domain.tld',
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
                    if ( $recentposts_query->have_posts() ) : 
						while ( $recentposts_query->have_posts() ) : $recentposts_query->the_post();
							$content .= '<li>';
								// Show monthly archive and link to months
								$month = get_the_date('F, Y');
								if ($month !== $month_check) : $content .= '<p><a href="' . get_month_link( get_the_date('Y'), get_the_date('m') ) . '" title="' . get_the_date('F, Y') . '">' . $month . '</a></p>'; endif;
								$month_check = $month;
							$content .= '<h4><a href="' . get_the_permalink() . '" title="' . sprintf( __('Permalink to %s', 'themes-plus'), the_title_attribute('echo=0') ) . '" rel="bookmark">' . get_the_title() . '</a></h4>';
							$content .= '</li>';
						endwhile;
					else:
						$content .= __('No Posts found!', 'themes-plus');
					endif; wp_reset_postdata(); // end of the loop.
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
                                'type'        => 'number',
                                'placeholder' => '5',
                            ),
                        ),
                    )
                );
            }
            
            
        /**
         * Countdown Timer: Count down to date
         * 
         * Shortcode:
         * [timer]January 25, 2020 12:00:00[/timer]
         */

            // Datetime: [timer]January 25, 2020 12:00:00[/timer]
            function themes_timer_shortcode( $atts = array(), $content = null ) {
                
                wp_register_script( 'timerinit', plugins_url( '/js/countdown.min.js', __FILE__ ), array('jquery'), '1.0', false );
                wp_enqueue_script( 'timerinit' );
                
				// Get Attributes
                extract(shortcode_atts(array(
                    'class' => '',
                    'style' => ''
                ), $atts));
				
                $datetime = do_shortcode( shortcode_unautop( $content ) ); // If $content contains a shortcode, that code will get processed
                
                return '<h3 id="timer" class="h1 timer' . ( $class ? ' ' . $class : '' ) . '"' . ( $style ? ' style="' . $style . '"' : '' ) . ' data-to="' . $datetime .'" data-offset="' . get_option('gmt_offset') . '" data-rtl="' . ( is_rtl() ? 'true' : 'false' ) . '">' . $datetime . ', UTC ' . get_option('gmt_offset') . '</h3>';
                
            }
            add_shortcode( 'timer', 'themes_timer_shortcode' );
            
        /**
         * Register a TinyMCE UI for the Shortcode
         * External Plugin "Shortcode UI" required: https://github.com/fusioneng/Shortcake
         */
            if (function_exists('shortcode_ui_register_for_shortcode')) {
                shortcode_ui_register_for_shortcode(
                    'timer',
                    array(
                        'label' => 'Countdown Timer: Timestamp',
                        //'listItemImage' => 'dashicons-editor-quote',
                        'attrs' => array(
                            array(
                                'label'       => 'Timestamp: January 25, 2020 12:00:00',
                                'description' => 'Timezone: UTC ' . get_option('gmt_offset') . ' ' . get_option('timezone_string'),
                                'attr'        => 'content',
                                'type'        => 'text',
                                'placeholder' => '',
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
         * Stats Counter
         * 
         * Shortcode:
         * [countup]###[/countup]
         */

            // Number: [countup]###[/countup]
            function themes_countup_shortcode( $atts = array(), $content = null ) {
                
                wp_register_script( 'counttoinit', plugins_url( '/js/countto.min.js', __FILE__ ), array('jquery'), '1.0', false );
                wp_enqueue_script( 'counttoinit' );
				
				// Get Attributes
                extract(shortcode_atts(array(
                    'class' => '',
                    'style' => ''
                ), $atts));
                
                $timer = do_shortcode( shortcode_unautop( $content ) ); // If $content contains a shortcode, that code will get processed
                
                return '<h3 class="countup h1' . ( $class ? ' ' . $class : '' ) . '"' . ( $style ? ' style="' . $style . '"' : '' ) . ' data-to="' . $timer .'" data-speed="2500">' . $timer .'</h3>';
                
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
                                'label'       => 'Count to',
                                'attr'        => 'content',
                                'type'        => 'number',
                                'placeholder' => '100',
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
         * Progress bar
         * 
         * Shortcode:
         * [progressbar color="blue" duration="2" label="true"]40[/progressbar]
		 * Colors: blue/green/lightblue/yellow/red
         */
            
            function themes_progressbar_shortcode( $atts = array(), $content = null ) {
                
                // Get Attributes
                extract(shortcode_atts(array(
					'type' => '',
					'title' => '',
                    'color' => '',
                    'duration' => '',
                    'label' => '',
                    'class' => '',
                    'style' => ''
                ), $atts));
                
                $value = do_shortcode( shortcode_unautop( $content ) ); // If $content contains a shortcode, that code will get processed
				
				if ( isset($type) && $type == "chart" ) {
					// Circular Progress indicator
					
					wp_register_script( 'progresschartinit', plugins_url( '/js/easypiechart.min.js', __FILE__ ), array('jquery'), '1.0', false );
					wp_enqueue_script( 'progresschartinit' );
					
					if ( isset($color) && $color != "" ) {

						if ( $color == "blue" ) {
							$colorcode = "337AB7";
						} else if ( $color == "green" ) {
							$colorcode = "5CB85C";
						} else if ( $color == "lightblue" ) {
							$colorcode = "5BC0DE";
						} else if ( $color == "yellow" ) {
							$colorcode = "F0AD4E";
						} else if ( $color == "red" ) {
							$colorcode = "D9534F";
						}

					}
					
					$progressbar = '<div class="chart">';
					$progressbar .= '<div class="easyPieChart" data-animate="on" data-percent="0" data-value="' . $value . '" data-duration="' . ( isset($duration) && $duration != "" ? $duration*1000 : '2000' ) . '"' . ( isset($colorcode) && $colorcode != "" ? ' data-bar-color="#' . $colorcode . '"' : '' ) . '>' . ( isset($label) && $label != "" || isset($label) && $label == "1" ? '<span class="percent">' . $value . '</span>' : '' ) . '</div>';
					if ( isset($title) && $title != "" ) { $progressbar .= '<h3>' . $title . '</h3>'; }
					$progressbar .= '</div>';
					
				} else {
					// Bootstrap Progress bar
					
					wp_register_script( 'progressbarinit', plugins_url( '/js/progressbarinit.min.js', __FILE__ ), array('jquery'), '1.0', false );
					wp_enqueue_script( 'progressbarinit' );
					
					if ( isset($color) && $color != "" ) {

						if ( $color == "blue" ) {
							$class .= " progress-bar-primary";
						} else if ( $color == "green" ) {
							$class .= " progress-bar-success";
						} else if ( $color == "lightblue" ) {
							$class .= " progress-bar-info";
						} else if ( $color == "yellow" ) {
							$class .= " progress-bar-warning";
						} else if ( $color == "red" ) {
							$class .= " progress-bar-danger";
						}

					}
					
					//$style .= "width: " . $value . "%;"; // no CSS3 animation!

					if ( isset($duration) && $duration != "" ) {
						$style .= "-webkit-transition-duration: " . $duration . "s; transition-duration: " . $duration . "s;";
					} else {
						$style .= "-webkit-transition-duration: 2s; transition-duration: 2s;"; // Default
					}

					if ( isset($label) && $label != "" ) {
						$label = $value . "%";
					}

					$progressbar = '<div class="progress">';
					if ( isset($title) && $title != "" ) { $progressbar .= '<h3>' . $title . '</h3>'; }
					$progressbar .= '<div class="progress-bar' . ( $class ? ' ' . $class : '' ) . '"' . ( $style ? ' style="' . $style . '"' : '' ) . ' role="progressbar" aria-valuenow="' . $value . '" aria-valuemin="0" aria-valuemax="100">';
					$progressbar .= ( isset($label) && $label != "" || isset($label) && $label == "1" ? $value . '%' : '<span class="sr-only">' . $value . '%</span>' );
					$progressbar .= '</div>';
					$progressbar .= '</div>';
					
				}
                
                return $progressbar;
                
            }
            add_shortcode( 'progressbar', 'themes_progressbar_shortcode' );
            
        /**
         * Register a TinyMCE UI for the Shortcode
         * External Plugin "Shortcode UI" required: https://github.com/fusioneng/Shortcake
         */
            if (function_exists('shortcode_ui_register_for_shortcode')) {
                shortcode_ui_register_for_shortcode(
                    'progressbar',
                    array(
                        'label' => 'Number',
                        //'listItemImage' => 'dashicons-editor-quote',
                        'attrs' => array(
                            array(
                                'label'       => 'Percentage',
                                'attr'        => 'content',
                                'type'        => 'number',
                                'placeholder' => '40',
                            ),
							array(
                                'label'       => 'Type',
                                'attr'        => 'type',
                                'type'        => 'radio',
								'value'       => 'bar', // default value 
                                'options'     => array(
                                                    'bar' => 'Bar',
                                                    'chart' => 'Chart'
                                                )
                            ),
                            array(
                                'label'       => 'Color',
                                'attr'        => 'color',
                                'type'        => 'select',
                                'options'     => array(
                                                    'blue' => 'Blue',
                                                    'green' => 'Green',
                                                    'lightblue' => 'Lightblue',
                                                    'yellow' => 'Yellow',
                                                    'red' => 'Red'
                                                )
                            ),
                            array(
                                'label'       => 'Duration (seconds)',
                                'attr'        => 'duration',
                                'type'        => 'number',
                                'placeholder' => '0.6',
                            ),
                            array(
                                'label'       => 'Show Label',
                                'attr'        => 'label',
                                'type'        => 'checkbox',
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
         * Content Carousel
         * 
         * Shortcode:
         * [carousel]
         *   [carouselslide]Lorem ipsum...[/carouselslide]
         *   [carouselslide]Lorem ipsum...[/carouselslide]
         * [/carousel]
         */
            function themes_carousel_shortcode( $atts = array(), $content = null ) {
				
				wp_register_script( 'carouselinit', plugins_url( '/js/carouselinit.min.js', __FILE__ ), array('jquery'), '1.0', false );
                wp_enqueue_script( 'carouselinit' );
                
                // Get Attributes
                extract(shortcode_atts(array(
                    'class' => '',
                    'style' => ''
                ), $atts));
                
                $GLOBALS['carouselslide_count'] = 0;
                
                return '<div id="slider" class="carousel slide ' . ( $class ? ' ' . $class : '' ) . '"' . ( $style ? ' style="' . $style . '"' : '' ) . ' data-interval="false"><div class="carousel-inner" role="listbox">' . do_shortcode($content) . '</div> <a class="left carousel-control" href="#slider" role="button" data-slide="prev"><span class="dashicons dashicons-arrow-left-alt2"></span></a> <a class="right carousel-control" href="#slider" role="button" data-slide="next"><span class="dashicons dashicons-arrow-right-alt2"></span></a></div>'; // If $content contains a shortcode, that code will get processed. Dashicons need to be activated via wp_enqueue_style( 'dashicons' );
                
            }
            add_shortcode( 'carousel', 'themes_carousel_shortcode' );
            
            // Carousel-slides: [carouselslide]...[/carouselslide]
            function themes_carouselslide_shortcode( $atts = array(), $content = null ) {
                
                // Get Attributes
                extract(shortcode_atts(array(
                    'class' => '',
                    'style' => ''
                ), $atts));
                
                if( isset($GLOBALS['carouselslide_count']) ) {
                    
                    if( $GLOBALS['carouselslide_count'] == 0 ) {
                        $class .= " active"; // first slide
                    }
                    
                    $GLOBALS['carouselslide_count']++;
                    
                }
                
                return '<div class="item' . ( $class ? ' ' . $class : '' ) . '"' . ( $style ? ' style="' . $style . '"' : '' ) . '>' . do_shortcode( shortcode_unautop( $content ) ) . '</div>';
                
            }
            add_shortcode( 'carouselslide', 'themes_carouselslide_shortcode' );
            
        /**
         * Register a TinyMCE UI for the Shortcode
         * External Plugin "Shortcode UI" required: https://github.com/fusioneng/Shortcake
         */
            if (function_exists('shortcode_ui_register_for_shortcode')) {
                shortcode_ui_register_for_shortcode(
                    'carouselslide',
                    array(
                        'label' => 'carouselslide',
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