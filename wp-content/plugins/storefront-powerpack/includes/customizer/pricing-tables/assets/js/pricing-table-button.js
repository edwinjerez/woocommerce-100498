/* global tinymce */
(function() {
    tinymce.PluginManager.add( 'spt_tinymce_button', function( editor ) {
        editor.addButton( 'spt_tinymce_button', {
            title: 'Insert Pricing Table',
            icon: 'icon spt-icon',
            type: 'menubutton',
            menu: [
                {
                    text: 'Pricing Table Wrapper',
                    onclick: function() {
					    editor.windowManager.open( {
					        title: 'Insert Pricing Table Wrapper',
					        body: [{
					            type: 'listbox',
					            name: 'columns',
					            label: 'Columns',
					            'values': [
					                {text: '1', value: '1'},
					                {text: '2', value: '2'},
					                {text: '3', value: '3'},
					                {text: '4', value: '4'},
					                {text: '5', value: '5'},
					                {text: '6', value: '6'}
					            ]
					        },
					        {
					            type: 'listbox',
					            name: 'alignment',
					            label: 'Text alignment',
					            'values': [
					                {text: 'left', value: 'left'},
					                {text: 'center', value: 'center'},
					                {text: 'right', value: 'right'}
					            ]
					        }],
					        onsubmit: function( e ) {
					            editor.insertContent( '[pricing_table columns="' + e.data.columns + '" alignment="' + e.data.alignment + '"][/pricing_table]');
					        }
					    });
					}
                },
                {
                    text: 'Pricing Table Column',
                    onclick: function() {
					    editor.windowManager.open( {
					        title: 'Insert Pricing Table Column',
					        body: [{
					            type: 'textbox',
					            name: 'product_id',
					            label: 'Product ID'
					        },
					        {
					            type: 'textbox',
					            name: 'title',
					            label: 'Column title'
					        },
					        {
					            type: 'textbox',
					            name: 'features',
					            label: 'Features (pipe (|) separated)',
					            size: 20
					        },
					        {
					            type: 'checkbox',
					            name: 'image',
					            label: 'Display product image'
					        },
					        {
					            type: 'checkbox',
					            name: 'highlight',
					            label: 'Highlight this column'
					        }],
					        onsubmit: function( e ) {
					            editor.insertContent( '[pricing_column id="' + e.data.product_id + '" title="' + e.data.title + '" features="' + e.data.features + '" image="' + e.data.image + '" highlight="' + e.data.highlight + '"]');
					        }
					    });
					}
                }
           ]
        });
    });
})();