var sui = require('sui-utils/sui'),
	Shortcodes = require('sui-collections/shortcodes'),
	shortcodeViewConstructor = require('sui-utils/shortcode-view-constructor'),
	mediaFrame = require('sui-views/media-frame'),
	wp = require('wp'),
	$ = require('jquery');

$(document).ready(function(){

	// Create collection of shortcode models from data.
	sui.shortcodes.add( shortcodeUIData.shortcodes );

	wp.media.view.MediaFrame.Post = mediaFrame;

	// Register a view for each shortcode.
	sui.shortcodes.each( function( shortcode ) {
		if ( wp.mce.views ) {
			wp.mce.views.register(
				shortcode.get('shortcode_tag'),
				shortcodeViewConstructor
			);
		}
	} );

	$(document.body).on( 'click', '.shortcake-add-post-element', function( event ) {

		var $el     = $( event.currentTarget ),
			editor  = $el.data('editor'),
			frame   = wp.media.editor.get( editor ),
			options = {
				frame: 'post',
				state: 'shortcode-ui',
				title: shortcodeUIData.strings.media_frame_title
			};

		event.preventDefault();

		// Remove focus from the `.shortcake-add-post-element` button.
		// Prevents Opera from showing the outline of the button above the modal.
		// See: https://core.trac.wordpress.org/ticket/22445
		$el.blur();

		if ( frame ) {
			frame.mediaController.setActionSelect();
			frame.open();
		} else {
			frame = wp.media.editor.open( editor, options );
		}

		// Make sure to reset state when closed.
		frame.once( 'close submit', function() {
			frame.state().props.set('currentShortcode', false);
			var menuItem = frame.menu.get().get('shortcode-ui');
			menuItem.options.text = shortcodeUIData.strings.media_frame_title;
			menuItem.render();
			frame.setState( 'insert' );
		} );

	} );

});
