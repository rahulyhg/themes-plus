(function() {
    
    jQuery('#tinymce p:contains("[panelgroup]")').css('background-color', 'red');
    
    tinymce.PluginManager.add('themes_tinymce_button', function( editor, url ) {
        editor.addButton( 'themes_tinymce_button', {
            title: 'them.es+ Shortcodes',
            image: url + '/tinymce-button.png',
            type: 'menubutton',
            menu: [
                {
                    text: 'Grid',
                    value: '',
                    onclick: function() {
                        editor.insertContent(this.value());
                    },
                    menu: [
                        {
                            text: '2 |-|-|',
                            value: '2',
                            onclick: function( e ) {
                                e.stopPropagation();
                                var items = "";
                                for (i = 0; i < this.value(); i++) {
                                    items += '[col]' + parseInt(i+1) + '. Lorem ipsum dolor sit amet...[/col]';
                                }
                                editor.insertContent( '[row]' + items + '[/row]');
                            }
                        },
                        {
                            text: '3 |-|-|-|',
                            value: '3',
                            onclick: function( e ) {
                                e.stopPropagation();
                                var items = "";
                                for (i = 0; i < this.value(); i++) {
                                    items += '[col]' + parseInt(i+1) + '. Lorem ipsum dolor sit amet...[/col]';
                                }
                                editor.insertContent( '[row]' + items + '[/row]');
                            }
                        },
                        {
                            text: '4 |-|-|-|-|',
                            value: '4',
                            onclick: function( e ) {
                                e.stopPropagation();
                                var items = "";
                                for (i = 0; i < this.value(); i++) {
                                    items += '[col]' + parseInt(i+1) + '. Lorem ipsum dolor sit amet...[/col]';
                                }
                                editor.insertContent( '[row]' + items + '[/row]');
                            }
                        },
                        {
                            text: '6 |-|-|-|-|-|-|',
                            value: '6',
                            onclick: function( e ) {
                                e.stopPropagation();
                                var items = "";
                                for (i = 0; i < this.value(); i++) {
                                    items += '[col]' + parseInt(i+1) + '. Lorem ipsum dolor sit amet...[/col]';
                                }
                                editor.insertContent( '[row]' + items + '[/row]');
                            }
                        }
                    ]
                },
                {
                    text: 'Recent Posts',
                    value: '[recentposts]',
                    onclick: function() {
                        editor.windowManager.open( {
                            title: 'Recent Posts',
                            body: [{
                                type: 'textbox',
                                name: 'number',
                                label: 'Number of Posts'
                            }],
                                onsubmit: function( e ) {
                                    if( e.data.number === '' || isNaN(e.data.number) ) {
                                        editor.insertContent( '[recentposts]');
                                    } else {
                                        editor.insertContent( '[recentposts number="' + e.data.number + '"]');
                                    }
                                }
                            });
                    }
                },
                {
                    text: 'Carousel',
                    value: '[carousel][item]Content 1[/item][item]Content 2[/item][item]Content 3[/item][/carousel]',
                    onclick: function() {
                        editor.windowManager.open( {
                            title: 'Map data',
                            body: [{
                                type: 'textbox',
                                name: 'number',
                                label: 'Number of Slides'
                            }],
                                onsubmit: function( e ) {
                                    if( e.data.number === '' || isNaN(e.data.number) ) {
                                        editor.insertContent( '[carousel][item]Lorem ipsum dolor sit amet...[/item][item]Lorem ipsum dolor sit amet...[/item][item]Lorem ipsum dolor sit amet...[/item][/carousel]');
                                    } else {
                                        var items = "";
                                        for (i = 0; i < e.data.number; i++) {
                                            items += '[item]' + parseInt(i+1) + '. Lorem ipsum dolor sit amet...[/item]';
                                        }
                                        editor.insertContent( '[carousel]' + items + '[/carousel]');
                                    }
                                }
                            });
                    }
                },
                {
                    text: 'Map',
                    value: '[map latlng="0.0000,0.0000" zoom="18"]',
                    onclick: function() {
                        editor.windowManager.open( {
                            title: 'Map data',
                            body: [{
                                type: 'textbox',
                                name: 'lat',
                                label: 'Latitude (0.0000)'
                            },
                            {
                                type: 'textbox',
                                name: 'lng',
                                label: 'Longitude (0.0000)'
                            },
                            {
                                type: 'textbox',
                                name: 'zoom',
                                label: 'Zoomfactor (0-19)'
                            }],
                            onsubmit: function( e ) {
                                if( e.data.lat === '' || e.data.lng === '' || isNaN(e.data.lat) || isNaN(e.data.lng) || isNaN(e.data.zoom) ) {
                                    editor.insertContent( '[map]');
                                }
                                else if( e.data.lat !== '' && e.data.lng !== '' && e.data.zoom !== '' && !isNaN(e.data.lat) && !isNaN(e.data.lng) && !isNaN(e.data.zoom) ) {
                                    editor.insertContent( '[map latlng="' + e.data.lat + ',' + e.data.lng + '" zoom="' + e.data.zoom + '"]');
                                }
                                else if( e.data.lat !== '' && e.data.lng !== '' && !isNaN(e.data.lat) && !isNaN(e.data.lng) || e.data.zoom === '' || isNaN(e.data.zoom) ) {
                                    editor.insertContent( '[map latlng="' + e.data.lat + ',' + e.data.lng + '"]');
                                }
                                else {
                                    editor.insertContent( '[map]');
                                }
                            }
                        });
                    }
                },
                {
                    text: 'Contact form',
                    value: '[contactform]',
                    onclick: function() {
                        editor.insertContent(this.value());
                    }
                },
                {
                    text: 'Media (Image Gallery, Carousel, Portfolio)',
                    value: '',
                    onclick: function() {
                        jQuery('#insert-media-button').click();
                    }
                },
           ]
        });
    });
})();