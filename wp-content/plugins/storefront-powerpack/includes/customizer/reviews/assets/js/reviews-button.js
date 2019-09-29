/* global tinymce */
(function() {
    tinymce.PluginManager.add( 'sr_tinymce_button', function( editor ) {
        editor.addButton( 'sr_tinymce_button', {
            title: 'Insert Reviews',
            icon: 'icon sr-icon',
            type: 'button',
            onclick: function() {
			    editor.windowManager.open( {
			        title: 'Insert Reviews',
			        body: [
			        {
			            type: 'textbox',
			            name: 'title',
			            label: 'Title'
			        },

			        {
			            type: 'listbox',
			            name: 'columns',
			            label: 'Columns',
			            'values': [
			                {text: '1', value: '1'},
			                {text: '2', value: '2'},
			                {text: '3', value: '3'}
			            ]
			        },

			        {
			            type: 'textbox',
			            name: 'number',
			            label: 'Number of reviews'
			        },

			        {
			            type: 'listbox',
			            name: 'scope',
			            label: 'Scope',
			            'values': [
			                {text: 'Recent reviews', value: 'recent'},
			                {text: 'Reviews of a Specific product', value: 'specific-product'},
			                {text: 'Specific reviews', value: 'specific-reviews'}
			            ]
			        },

			        {
			            type: 'textbox',
			            name: 'product_id',
			            label: 'Product ID'
			        },

			        {
			            type: 'textbox',
			            name: 'review_ids',
			            label: 'Review IDs'
			        },

			        {
			            type: 'listbox',
			            name: 'layout',
			            label: 'Style',
			            'values': [
			                {text: 'Plain', value: 'style-1'},
			                {text: 'Bubble', value: 'style-2'},
			                {text: 'Graphic', value: 'style-3'}
			            ]
			        },

			        {
			            type: 'checkbox',
			            name: 'carousel',
			            label: 'Display reviews in a carousel'
			        },

			        {
			            type: 'checkbox',
			            name: 'gravatar',
			            label: 'Display the reviewers Gravatar'
			        }
			        ],
			        onsubmit: function( e ) {
			            editor.insertContent( '[storefront_reviews title="' + e.data.title + '" columns="' + e.data.columns + '" number="' + e.data.number + '" scope="' + e.data.scope + '" product_id="' + e.data.product_id + '" review_ids="' + e.data.review_ids + '" layout="' + e.data.layout + '" carousel="' + e.data.carousel + '" gravatar="' + e.data.gravatar + '"]');
			        }
			    });
			}

        });
    });
})();