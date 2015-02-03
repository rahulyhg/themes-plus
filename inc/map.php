<?php
    $map_styles = trim( get_theme_mod('map_styles') );

    if ( isset($map_styles) && $map_styles != "" ):
        echo '<script>var styles = ' . $map_styles . '</script>';
    endif;
    
    // [map latlng='###.####,###.####' zoom='##']
    // 1. Check Shortcode attributes (first choice!): $latlng, $zoom
    extract(shortcode_atts(array(
        'latlng' => '',
        'zoom' => '',
        'class' => '',
        'style' => ''
    ), $atts));
    
    if ( empty($latlng) ):
        // [map]
        // 2. Use global settings (Theme Customization API)
        $latlng = trim( get_theme_mod('map_latlng') );
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
		<div id="map" data-latlng="<?php echo $latlng; ?>"<?php if ( isset($zoom) && $zoom != "" ): echo ' data-zoom="' . $zoom . '"'; endif; ?>><?php _e('Map', 'themes-plus'); ?></div><!-- /#map -->
	<?php 
        else:
    ?>
		<p class="alert alert-danger"><?php _e('Lat/Lng undefined. Please add the coordinates in the Page Settings!', 'themes-plus'); ?></p>
	<?php
        endif;
    ?>
</div>