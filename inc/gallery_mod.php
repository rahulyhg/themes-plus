<?php
    add_filter( 'use_default_gallery_style', '__return_false' );
    // Extend Gallery settings: Add Dropdown "Type" (https://wordpress.org/support/topic/how-to-add-fields-to-gallery-settings#post-5000775)
    add_action('print_media_templates', function() {
?>
    <script type="text/html" id="tmpl-themesplus-custom-gallery-setting">
        <label class="setting">
            <span><?php _e( 'Type', 'themes-plus'); ?></span>
            <select data-setting="type">
                <option value=""><?php _e( 'Default', 'themes-plus' ); ?></option>
                <option value="grid"><?php _e( 'Grid', 'themes-plus' ); ?></option>
                <option value="grid-stacked"><?php _e( 'Grid Stacked (9 items)', 'themes-plus' ); ?></option>
                <option value="carousel"><?php _e( 'Carousel', 'themes-plus' ); ?></option>
                <option value="panzoom"><?php _e( 'PanZoom', 'themes-plus' ); ?></option>
                <option value="portfolio"><?php _e( 'Portfolio Filter', 'themes-plus' ); ?></option>
            </select>
        </label>
    </script>

    <script>
        jQuery(document).ready(function() {
            // add your shortcode attribute and its default value to the
            // gallery settings list; $.extend should work as well...
            _.extend(wp.media.gallery.defaults, {
                type: ''
            });

            // merge default gallery settings template with yours
            wp.media.view.Settings.Gallery = wp.media.view.Settings.Gallery.extend({
                template: function (view) {
                    return wp.media.template('gallery-settings')(view) + wp.media.template('themesplus-custom-gallery-setting')(view);
                }
            });
        });
    </script>
<?php
    });

    
    // Enable Tags in Media (needed for Portfolio Filter)
	function themes_register_taxonomies() {
		$taxonomies = array( 'post_tag' );
		foreach ( $taxonomies as $taxonomy ) {
			register_taxonomy_for_object_type( $taxonomy, 'attachment' ); // Media
		}
	}
	add_action( 'init', 'themes_register_taxonomies' );


    // Custom Wordpress Gallery shortcode
	function themes_post_gallery( $output, $atts = array(), $id = 0 ) {
		if (isset($attr['orderby'])) {
			$attr['orderby'] = sanitize_sql_orderby($attr['orderby']);
			if (!$attr['orderby'])
				unset($attr['orderby']);
		}
		extract(shortcode_atts(array(
			'order' => 'ASC',
			'orderby' => 'menu_order ID',
			'itemtag' => 'dl',
			'icontag' => 'dt',
			'captiontag' => 'dd',
			'include' => '',
			'exclude' => '',
            'link' => '', // $link
			'type' => '' // optional: $type (type="grid|carousel|panzoom|etc.")
		), $atts));
	
		$id = intval($id);
		if ('RAND' == $order) $orderby = 'none';
	
		if ( !empty($include) ) {
			$include = preg_replace('/[^0-9,]+/', '', $include);
			$_attachments = get_posts(array(
				'include' => $include, 
				'post_status' => 'inherit', 
				'post_type' => 'attachment', 
				'post_mime_type' => 'image', 
				'order' => $order, 
				'orderby' => $orderby
			));
			$attachments = array();
			foreach ($_attachments as $key => $val) {
				$attachments[$val->ID] = $_attachments[$key];
			}
		}
        
        // Attribute style=
        extract(shortcode_atts(array(
            'style' => ''
        ), $atts));
	
		if ( empty($attachments) ) return '';
        
        
        // 1. Grid: [gallery ids="###,###,###" type="grid"]
        if ( !empty($include) && $type == "grid" ) :
        
            // Load Masonry Plugin
            wp_register_script( 'salvattoreplugin', plugins_url( '/js/salvattore.min.js', dirname(__FILE__) ), array('jquery'), '1.0', false );
            wp_enqueue_script( 'salvattoreplugin' );

			$output = '<div id="grid" class="gallery grid" data-columns>' . PHP_EOL;
        
			$i = 0;
        
			// Attachments
			foreach ($attachments as $id => $attachment) {
                $image = wp_get_attachment_image($id, 'large', false, false);
                
                $weblink = get_post_meta( $id, '_weblink', true ); // Test if custom metadata "weblink" has been defined?
                
                if ( filter_var($weblink, FILTER_VALIDATE_URL) ) :
                    $url = $weblink; // "external" link
                elseif ( isset($weblink) && !empty($weblink) && $weblink[0] === "/" ) :
                    $url = get_site_url() . $weblink; // relative "internal" link
                endif;
                
                if ( isset($url) ) :
                    $image = '<a href="' . $url . '">' . $image . '</a>';
                else:
                    $image = isset($link) && 'none' == $link ? '<span>' . $image . '</span>' : '<a href="' . wp_get_attachment_url( $id ) . '">' . $image . '</a>';
                
                endif;
                
				$output .= $image . PHP_EOL;
                
                unset($url); // Unset $url from foreach
			
				$i++;
			}
            
			$output .= "</div><!-- /.grid -->" . PHP_EOL;
		
		endif;
        
        
        // 2. Grid rectangular: [gallery ids="###,###,###" type="grid-stacked"]
        if ( !empty($include) && $type == "grid-stacked" ) :
        
            if ( !function_exists('get_image_stacked') ) {
                
                function get_image_stacked( $id, $link) {
                    $imageurl = wp_get_attachment_url( $id, false );
                    
                    $weblink = get_post_meta( $id, '_weblink', true ); // Test if custom metadata "weblink" has been defined?
                    
                    if ( filter_var($weblink, FILTER_VALIDATE_URL) ) :
                        $url = $weblink; // "external" link
                    elseif ( isset($weblink) && !empty($weblink) && $weblink[0] === "/" ) :
                        $url = get_site_url() . $weblink; // relative "internal" link
                    endif;

                    if ( !empty($id) ) :
                        if ( isset($url) && !empty($url) ) :
                            $image = '<a href="' . $url . '" style="background-image: url(' . $imageurl . ');"></a>';
                        else:
                            $image = isset($link) && 'none' == $link ? '<span style="background-image: url(' . $imageurl . ');"></span>' : '<a href="' . wp_get_attachment_url( $id ) . '" style="background-image: url(' . $imageurl . ');"></a>';
                        
                        endif;
                    
                        unset($url); // Unset $url

                    else:

                        $image = '';

                    endif;

                    return $image;
                }
                
            }
            
            foreach ($attachments as $id => $attachment) {
                $data[] = $attachment->ID;
            }
            
            $output = '<div id="grid-stacked" class="gallery grid-stacked">' . PHP_EOL;
            $output .= '<div class="row">' . PHP_EOL;
                $output .= '<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">' . PHP_EOL;
                    $output .= '<div class="row">' . PHP_EOL;
                        $output .= '<div class="col-lg-12 col-md-12 col-sm-12">' . get_image_stacked( isset($data[0]) ? $data[0] : null, $link ) . '</div>' . PHP_EOL;
                        $output .= '<div class="col-lg-12 col-md-12 col-sm-12">' . get_image_stacked( isset($data[1]) ? $data[1] : null, $link ) . '</div>' . PHP_EOL;
                        $output .= '<div class="col-lg-12 col-md-12 col-sm-12">' . get_image_stacked( isset($data[2]) ? $data[2] : null, $link ) . '</div>' . PHP_EOL;
                    $output .= '</div><!-- /.row -->' . PHP_EOL;
                $output .= '</div><!-- /.col -->' . PHP_EOL;
                $output .= '<div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">' . get_image_stacked( isset($data[3]) ? $data[3] : null, $link ) . '</div>' . PHP_EOL;
            $output .= '</div><!-- /.row -->' . PHP_EOL;
            $output .= '<div class="row hidden-xs">' . PHP_EOL;
                $output .= '<div class="col-lg-6 col-md-6 col-sm-6">' . get_image_stacked( isset($data[4]) ? $data[4] : null, $link ) . '</div>' . PHP_EOL;
                $output .= '<div class="col-lg-3 col-md-3 col-sm-3">' . PHP_EOL;
                    $output .= '<div class="row">' . PHP_EOL;
                        $output .= '<div class="col-lg-12 col-md-12 col-sm-12">' . get_image_stacked( isset($data[5]) ? $data[5] : null, $link ) . '</div>' . PHP_EOL;
                        $output .= '<div class="col-lg-12 col-md-12 col-sm-12">' . get_image_stacked( isset($data[6]) ? $data[6] : null, $link ) . '</div>' . PHP_EOL;
                    $output .= '</div><!-- /.row -->' . PHP_EOL;
                $output .= '</div><!-- /.col -->' . PHP_EOL;
                $output .= '<div class="col-lg-3 col-md-3 col-sm-3">' . PHP_EOL;
                    $output .= '<div class="row">' . PHP_EOL;
                        $output .= '<div class="col-lg-12 col-md-12 col-sm-12">' . get_image_stacked( isset($data[7]) ? $data[7] : null, $link ) . '</div>' . PHP_EOL;
                        $output .= '<div class="col-lg-12 col-md-12 col-sm-12">' . get_image_stacked( isset($data[8]) ? $data[8] : null, $link ) . '</div>' . PHP_EOL;
                    $output .= '</div><!-- /.row -->' . PHP_EOL;
                $output .= '</div><!-- /.col -->' . PHP_EOL;
            $output .= '</div><!-- /.row -->' . PHP_EOL;
            $output .= '</div><!-- /.grid-stacked -->' . PHP_EOL;

		endif;
        
        
        // 3. Carousel: [gallery ids="###,###,###" type="carousel"]
        if ( !empty($include) && $type == "carousel" ) :
		
			$output = '<div id="carousel" class="carousel carousel-fade slide" data-ride="carousel" data-interval="10000"' . ( $style ? ' style="' . $style . '"' : '' ) . '>' . PHP_EOL;
			$output .= '<div class="carousel-inner">' . PHP_EOL;
        
			$i = 0;
        
			// Attachments
			foreach ($attachments as $id => $attachment) {
				$img = wp_get_attachment_image_src($id, 'large');
                
				if ( $i == 0 ) {
					$class = "active";
				} else {
					$class = "";
				}
                
				$output .= '<div class="item slide' . $i . ' ' . $class . '" style="background-image:url(' . $img[0] . ')">' . PHP_EOL;
					$output .= '<div class="carousel-caption">' . PHP_EOL;
						$output .= '<h3>' . esc_attr( $attachment->post_excerpt ) . '</h3>' . PHP_EOL;
						$output .= '<p>' . $attachment->post_content . '</p>' . PHP_EOL;
					$output .= '</div>' . PHP_EOL;
				$output .= '</div>' . PHP_EOL;
			
				$i++;
			}
			$output .= "</div><!-- /.carousel-inner -->" . PHP_EOL;
	
			$j = 0;
            
			// Indicators
			$output .= '<ol class="carousel-indicators">' . PHP_EOL;
			foreach ($attachments as $id => $attachment) {
				if ( $j == 0 ) {
					$class = "active";
				} else {
					$class = "";
				}
                
				$output .= '<li data-target="#carousel" data-slide-to="' . $j . '" class="' . $class . '"></li> ';
			
				$j++;
			}
			$output .= '</ol>' . PHP_EOL;
		
			// Controls
			$output .= '<a class="left carousel-control" href="#carousel" role="button" data-slide="prev"><span class="dashicons dashicons-arrow-left-alt2"></span></a> <a class="right carousel-control" href="#carousel" role="button" data-slide="next"><span class="dashicons dashicons-arrow-right-alt2"></span></a>';
		
			$output .= "</div><!-- /.carousel -->" . PHP_EOL;
		
		endif;
        
        
        // 4. Ken Burns effect: [gallery ids="###,###,###" type="panzoom"]
        if ( !empty($include) && $type == "panzoom" ) :
        
            // Init Javascripts
            wp_register_script( 'panzoominit', plugins_url( '/js/panzoominit.min.js', dirname(__FILE__) ), array('jquery'), '1.0', false );
            wp_enqueue_script( 'panzoominit' );
		
			$output = '<div id="panzoom" class="panzoom carousel carousel-fade slide" data-ride="carousel" data-pause="false" data-interval="10000"' . ( $style ? ' style="' . $style . '"' : '' ) . '>' . PHP_EOL;
			$output .= '<div class="carousel-inner">' . PHP_EOL;
        
			$i = 0;
        
			// Attachments
			foreach ($attachments as $id => $attachment) {
				$img = wp_get_attachment_image_src($id, 'large');
				if ( $i == 0 ) {
					$class = "active";
				} else {
					$class = "";
				}
				$output .= '<div class="item slide' . $i . ' ' . $class . '" style="background-image:url(' . $img[0] . ');">' . PHP_EOL;
					$output .= '<!--div class="carousel-caption">' . PHP_EOL;
						$output .= '<h3>' . esc_attr( $attachment->post_excerpt ) . '</h3>' . PHP_EOL;
						$output .= '<p>' . $attachment->post_content . '</p>' . PHP_EOL;
                        $output .= isset($link) && 'none' == $link ? '' : '<a href="' . wp_get_attachment_url( $id ) . '">' . __('more', 'themes-plus') . '</a>';
					$output .= '</div-->' . PHP_EOL;
				$output .= '</div>' . PHP_EOL;
			
				$i++;
			}
			$output .= "</div><!-- /.carousel-inner -->" . PHP_EOL;
        
			$output .= "</div><!-- /.carousel -->" . PHP_EOL;
		
		endif;
        
        
        // 5. Portfolio Filter: [gallery ids="###,###,###" type="portfolio"]
        if ( !empty($include) && $type == "portfolio" ) :
        
            // Init Javascripts
            wp_register_script( 'shuffleinit', plugins_url( '/js/shuffle.min.js', dirname(__FILE__) ), array('jquery'), '1.0', false );
            wp_enqueue_script( 'shuffleinit' );
        
?>
            <script>
                jQuery(document).ready(function() {
                    var portfolio = jQuery('#portfolio-list'), filterOptions = jQuery('.filter-options');

                    portfolio.shuffle({
                        itemSelector: '[class*="col-"]',
                        sizer: null,
                        speed: 250, // Transition/animation speed (milliseconds).
                        easing: 'ease-out', // CSS easing function to use.
                        buffer: 0, // Useful for percentage based heights when they might not always be exactly the same (in pixels).
                        initialSort: null, // Shuffle can be initialized with a sort object. It is the same object given to the sort method.
                        throttleTime: 300, // How often shuffle can be called on resize (in milliseconds).
                        sequentialFadeDelay: 150, // Delay between each item that fades in when adding items.
                    });

                    var btns = filterOptions.find('.btn');
                    btns.on('click', function() {
                        var btn = jQuery(this), isActive = btn.hasClass( 'active' ),
                        group = isActive ? 'all' : btn.data('group');

                        // Already active?
                        if ( isActive ) {
                            return false;
                        }

                        filterOptions.find('.btn').removeClass("active");
                        btn.addClass("active");

                        // Filter elements
                        portfolio.shuffle( 'shuffle', group );
                    });

                    btns = null;

                });
            </script>
<?php
        
            $output = '<ul id="portfolio-filter" class="filter-options navbar nav nav-pills">';
                $output .= '<li><button data-group="all" class="btn btn-default active">' . __('All', 'themes-plus') . '</button></li>';
                
                foreach ($attachments as $id => $attachment) {
                    $terms = get_the_terms( $id, 'post_tag' ); // Get terms "post_tag" from Attachments
                    //print_r($terms);
                    
                    if ( $terms ) :
                        foreach ($terms as $term) {
                            $links[] = $term->name; // $links array: Get only names
                        }
                    endif;
                }
                
                $unique_links = array_unique($links); // Unique array
                $unique_links = array_map('strtolower', $unique_links);
                asort($unique_links); // Sort array
                //print_r($unique_links);
                
                foreach ($unique_links as $link) {
                    $output .= '<li><button data-group="' . $link . '" class="btn btn-default" title="' . esc_attr( $link ) . '">' . ucwords($link) . '</button></li>';
                }
                
            $output .= "</ul>";
            
			$output .= '<div id="portfolio-wrapper"' . ( $style ? ' style="' . $style . '"' : '' ) . '>' . PHP_EOL;
			$output .= '<div id="portfolio-list" class="row">' . PHP_EOL;
            
			// Attachments
			foreach ($attachments as $id => $attachment) {
                $img = wp_get_attachment_image_src($id, 'large');
                
                $terms = get_the_terms( $id, 'post_tag' ); // Get terms "post_tag" from Attachments
                //print_r($terms);
                
                if ( $terms ) :
                    $links = array();
                    foreach ( $terms as $term ) {
                        $links[] = $term->name;
                    }
                    $groups = join(',', array_map(function($a) { return '"' . $a . '"'; }, $links)); // wrap ("...") and join (,) group items
                    $groups = strtolower($groups); // Lower case
                else : 
                    $groups = ''; 
                endif;
                
                $weblink = get_post_meta( $id, '_weblink', true ); // Test if custom metadata "weblink" has been defined
                
                if ( filter_var($weblink, FILTER_VALIDATE_URL) ) :
                    $url = $weblink; // "external" link
                elseif ( isset($weblink) && !empty($weblink) && $weblink[0] === "/" ) :
                    $url = get_site_url() . $weblink; // relative "internal" link
                endif;
				
				$output .= '<div data-groups=\'[' . $groups . ']\' title="' . esc_attr( $attachment->post_title ) . '" class="col-lg-3 col-md-3 col-sm-4 col-xs-6">' . PHP_EOL;
                    $output .= isset($url) && !empty($url) ? '<a href="' . $url . '">' : ''; // open link-tag
					$output .= '<div class="thumbnail" style="background-image:url(' . $img[0] . ');"></div><!-- /.thumbnail -->' . PHP_EOL;
                    $output .= '<h4>' . esc_attr( $attachment->post_title ) . '</h4>' . PHP_EOL;
                    $output .= isset($url) && !empty($url) ? '</a>' : ''; // close link-tag
				$output .= '</div><!-- /.col -->' . PHP_EOL;
                
                unset($url); // Unset $url from foreach
			}
            
            $output .= '<div class="clearfix"></div>' . PHP_EOL;
			$output .= '</div><!-- /.row -->' . PHP_EOL;
        
			$output .= '</div><!-- /#portfolio-wrapper -->' . PHP_EOL;
		
		endif;
        
        
        
        
        // Return Code
		return $output;
        
	}
	add_filter('post_gallery', 'themes_post_gallery', 10, 2);

?>