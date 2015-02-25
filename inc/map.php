<?php
    $map_styles = trim( get_theme_mod('map_styles') );

    if ( isset($map_styles) && $map_styles != "" ):
        echo '<script>var styles = ' . $map_styles . '</script>';
    endif;
    
    // [map latlng='###.####,###.####' zoom='##' marker='http://']
    // 1. Check Shortcode attributes (first choice!): $latlng, $zoom
    extract(shortcode_atts(array(
        'latlng' => '',
        'zoom' => '',
        'marker' => '',
        'class' => '',
        'style' => ''
    ), $atts));
    
    if ( empty($latlng) ):
        // Use global settings (Theme Customization API)
        $latlng = trim( get_theme_mod('map_latlng') );
    endif;
	
    if ( empty($zoom) ):
        // Use global settings (Theme Customization API)
        $zoom = trim( get_theme_mod('map_zoom', '13') );
    endif;
	
    if ( empty($marker) ):
        // Use global settings (Theme Customization API)
        $marker = trim( get_theme_mod('map_markerimage') );
    endif;
?>

<div id="map-container"<?php if ( isset($class) && $class != "" ): echo ' class="' . $class . '"'; endif; if ( isset($style) && $style != "" ): echo ' style="' . $style . '"'; endif; ?>>
	<?php
        if ( isset($latlng) && $latlng != "" ):

            // Load Google Maps API
            wp_register_script( 'gmapsapi', 'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false', false, '1.0', false );
            wp_enqueue_script( 'gmapsapi' );

            // Initialize Google Maps
            wp_register_script( 'gmapsinit', plugins_url( '/js/map.min.js', dirname(__FILE__) ), array('gmapsapi'), '1.0', false );
            wp_enqueue_script( 'gmapsinit' );
    ?>
		<div id="map-canvas" data-latlng="<?php echo $latlng; ?>"<?php if ( isset($zoom) && $zoom != "" ): echo ' data-zoom="' . $zoom . '"'; endif; if ( isset($marker) && $marker != "" ): echo ' data-marker="' . $marker . '"'; endif; ?>><?php _e('Map', 'themes-plus'); ?></div><!-- /#map -->
	<?php 
        else:
    ?>
		<p class="alert alert-danger"><?php _e('Lat/Lng undefined. Please add the coordinates in the Page Settings!', 'themes-plus'); ?></p>
	<?php
        endif;
    ?>
</div>