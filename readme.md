## them.es<sup>+</sup>

**them.es<sup>+</sup>** is a WordPress Plugin that brings useful **Modules** and **Features** to WordPress *the WordPress way*. It has been developed for them.es Themes, but also works on other Themes built with Bootstrap.

Pull requests are always welcome!

For more information, check out [them.es](http://them.es/).

![Alt text](/screenshot.png?raw=true)


## What's included?
* Shortcodes
* Page Builder / TinyMCE Plugin
* Bootstrap components: Grid, Galleries, Carousels
* WordPress hooks: Transform Standard WordPress Galleries into slideshows and even more
* Portfolio Filter
* Google Maps
* Simple Contact form


## What's NOT included?
* Bootstrap framework (needs to be included in your Theme)


## Shortcodes
All Shortcodes are documented in the Theme demos and can be included via a Page builder button in TinyMCE.

<table>
<thead>
<tr>
    <th>Type</th>
    <th>Shortcode</th>
</tr>
</thead>
<tbody>
<tr>
    <td></td>
    <td></td>
</tr>
<tr>
    <td><strong>Recent Posts</strong></td>
    <td>
        <pre><code>[recentposts]</code></pre>
        <pre><code>[recentposts posts="10"]</code></pre>
    </td>
</tr>
<tr>
    <td></td>
    <td></td>
</tr>
<tr>
    <td><strong>Count up Stats</strong></td>
    <td>
        <pre>
        <code>
[stat]256[/stat]
        </code>
        </pre>
Example with 4 stats in a grid:
        <pre>
        <code>
[row]
  [col][stat]657[/stat] Projects delivered[/col]
  [col][stat]46[/stat] Awards[/col]
  [col][stat]231[/stat] Fans[/col]
  [col][stat]3756[/stat] Cups of coffee[/col]
[/row]
        </code>
        </pre>
    </td>
</tr>
<tr>
    <td></td>
    <td></td>
</tr>
<tr>
    <td><strong>Content Carousel</strong></td>
    <td>
        <pre>
        <code>
[carousel]
  [carouselslide]Slide 1. Lorem ipsum dolor sit amet...[/carouselslide]
  [carouselslide]Slide 2. Lorem ipsum dolor sit amet...[/carouselslide]
  [carouselslide]Slide 3. Lorem ipsum dolor sit amet...[/carouselslide]
  etc.
[/carousel]
        </code>
        </pre>
    </td>
</tr>
<tr>
    <td></td>
    <td></td>
</tr>
<tr>
    <td><strong>Default Gallery</strong></td>
    <td><pre><code>[gallery ids="###,###,###"]</code></pre></td>
</tr>
<tr>
    <td></td>
    <td></td>
</tr>
<tr>
    <td><strong>Gallery Grid</strong></td>
    <td><pre><code>[gallery ids="###,###,###" type="grid"]</code></pre></td>
</tr>
<tr>
    <td></td>
    <td></td>
</tr>
<tr>
    <td><strong>Gallery Grid** “stacked”<br><small>(9 items)</small></td>
    <td><pre><code>[gallery ids="###,###,###" type="grid-stacked"]</code></pre></td>
</tr>
<tr>
    <td></td>
    <td></td>
</tr>
<tr>
    <td><strong>Image Carousel</strong></td>
    <td><pre><code>[gallery ids="###,###,###" type="carousel"]</code></pre></td>
</tr>
<tr>
    <td></td>
    <td></td>
</tr>
<tr>
    <td><strong>Image Carousel</strong> with Panzoom<br><small>aka “Ken Burns” effect</small></td>
    <td><pre><code>[gallery ids="###,###,###" type="panzoom"]</code></pre></td>
</tr>
<tr>
    <td></td>
    <td></td>
</tr>
<tr>
    <td><strong>Portfolio Filter</strong></td>
    <td><pre><code>[gallery ids="###,###,###" type="portfolio"]</code></pre></td>
</tr>
<tr>
    <td></td>
    <td></td>
</tr>
<tr>
    <td><strong>Map</strong></td>
    <td><pre><code>[map latlng="###.####,###.####" zoom="##" class="..." style="..."]</code></pre></td>
</tr>
<tr>
    <td></td>
    <td></td>
</tr>
<tr>
    <td><strong>Contact form</strong></td>
    <td><pre><code>[contactform]</code></pre></td>
</tr>
<tr>
    <td></td>
    <td></td>
</tr>
<tr>
    <td><strong>Video</strong></td>
    <td>
        <pre><code>http://www.youtube.com/watch?v=###########</code></pre>
        <pre><code>http://vimeo.com/########</code></pre>
        <pre><code>[video src="*.mp4"]</code></pre>
    </td>
</tr>
<tr>
    <td></td>
    <td></td>
</tr>
<tr>
    <td><strong>Audio</strong></td>
    <td><pre><code>[audio src="*.mp3"]</code></pre></td>
</tr>
<tr>
    <td></td>
    <td></td>
</tr>
<tr>
    <td><strong>Column-Grid</strong><br><small>(max. 6 cols)</small></td>
    <td>
        <pre>
        <code>
[row]
  [col]1. Lorem ipsum dolor sit amet...[/col]
  [col]2. Lorem ipsum dolor sit amet...[/col]
  etc.
[/row]
        </code>
        </pre>
    </td>
</tr>
</tbody>
</table>


## Technology

* [Shortcake Plugin](https://github.com/fusioneng/Shortcake), [GPLv2+](https://github.com/fusioneng/Shortcake/blob/master/LICENSE)
* [Bootstrap](https://github.com/twbs/bootstrap), [MIT licence](https://github.com/twbs/bootstrap/blob/master/LICENSE)
* [jquery-countTo](https://github.com/mhuggins/jquery-countTo), [MIT licence](https://github.com/mhuggins/jquery-countTo/blob/master/LICENSE.txt)
* [Shuffle.js](https://github.com/Vestride/Shuffle), [MIT licence](https://github.com/Vestride/Shuffle/blob/master/LICENSE)
* [Salvattore](https://github.com/rnmp/salvattore), [MIT licence](https://github.com/rnmp/salvattore/blob/master/LICENSE)
* [Google Maps API](https://developers.google.com/maps), [Proprietary licence](https://developers.google.com/maps/licensing)


## Copyright & License

Code and Documentation &copy; [them.es](http://them.es)

Code released under [GPLv2+](http://www.gnu.org/licenses/gpl-2.0.html)