=== them.es Plus ===
Contributors: them.es
Tags: bootstrap, slideshow, slider, gallery, portfolio filter, google maps, contact form, grid
Requires at least: 4.0
Tested up to: 4.1
Stable tag: 1.1.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Useful WordPress Add-ons for Themes built with Bootstrap

== Description ==

= Target Group? =

* WordPress Themes built with the [Bootstrap](http://getbootstrap.com/ "Bootstrap") framework
* Installing this Plugin is strongly recommended for [them.es](http://them.es/ "them.es") customers who want to implement the featured Modules
    
= What you get? =

* `[Shortcodes]`
* Google Maps, Contact form, Recent Posts, Count up Stats, Carousel, Grid, etc.
* WordPress Standard Image Gallery hooks, Slider, Portfolio Filter
* TinyMCE Button
* Page Builder
* Theme Customization API (on them.es Themes!)

= Compatibility? =

* Some Styles and Javascripts depend on Bootstrap - __Please note that the required Bootstrap components are not included in the Plugin!__
* __In order to work properly the Plugin needs to be installed on systems with Bootstrap powered Themes__

= Feedback/Help/Contribution? =

* The Development of this Plugin can be followed via a GitHub repository <3 and are happy to receive feedback, questions, feature suggestions and pull requests: [https://github.com/them-es](https://github.com/them-es "GitHub")

== Screenshots ==

1. TinyMCE Button
2. Shortcodes WYSIWYG
3. Edit a Post Element
4. WordPress Image Gallery hooks

== Installation ==

1. Upload the Plugin to the `/wp-content/plugins/` directory
2. Activate it through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= Which modules are included? How can I add them to a page? =
* __Custom Shortcodes:__ Google Maps, Contact form, Recent Posts, Count up Stats, Content Carousels, Bootstrap Column Grid
  * _Use the them.es+ button in the TinyMCE Editor to add a module on the page_
* __WordPress Standard Image Gallery hooks:__ Gallery Grids, Image Carousels, Panzoom (Ken Burns) Slider, Portfolio Filter
  * _Gallery hooks can be activated by creating a new Gallery, adding images and selecting the desired "Type" in the settings_
* __Portfolio Filter:__
  * _Upload all images which should appear in the Portfolio and name/tag them accordingly. Create a new WordPress Gallery and select Type "Portfolio Filter"_

= Does this Plugin work with my Theme? =
* The Plugin has been primarily developed to bring additional features into [them.es](http://them.es/ "them.es") Themes
* If your WordPress Theme has been built with Bootstrap and if all Bootstrap components are included in your Theme the Plugin should work out-of-the-box

= Where can I get help? =
* You can ask questions in the Support forums
  * [https://github.com/them-es](https://github.com/them-es "GitHub")
  * [http://them.es/support](http://them.es/support "them.es Support")
* WordPress developers are welcome to help us improve the Plugin via [GitHub](https://github.com/them-es "GitHub")
  
== Upgrade Notice ==

= 1.1.2 =
* Important change: For consistency reasons the inner Content Carousel shortcode [item] got replaced with [carouselslide]

= 1.1.1 =
* Support for custom Marker images in Maps

= 1.1 =
* Public Release in the WordPress Plugin directory
* Updated documentation

== Changelog ==

= 1.1.2 =
* New feature: Count up Stats shortcode `[countup]123456[/countup]`
* Changes: Content Carousel shortcode pattern `[carousel][carouselslide]...[/carouselslide][carouselslide]...[/carouselslide][/carousel]`
* Updates: Shortcake Plugin, Minor CSS changes in Gallery Grids (fixed glitch w/ Twentyfourteen Theme)

= 1.1.1 =
* New feature: Maps now support a Marker image URL attribute, Contact form and Map shortcodes now allow custom class/style attributes
* Updates: Shortcake Plugin, JS changes
* Minor bugfixes

= 1.1 =
* New feature: Integrated the Shortcake Plugin
* Minor bugfixes

= 1.1a =
* New feature: Portfolio Filter
* Updates: Minor CSS changes, Translations
* Docs: Added Dependencies and Licence information

= 1.0 =
* Initial Release
* GitHub repository