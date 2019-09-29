(function(){
    tinymce.create('tinymce.plugins.cognito', {
        init : function(ed, url) {
            ed.addCommand('cognito_embed_window', function() {
                ed.windowManager.open({
                    file   : url + '/dialog.php',
                    width  : 400,
                    height : 160,
                    inline: 1
                }, { plugin_url : url, ajax_url: ajaxurl });
            });
            
            ed.addButton('cognito', {
                title : 'Cognito Forms',
				cmd : 'cognito_embed_window',
				image : url + '/cogicon.ico'
            });
        }
    });

    tinymce.PluginManager.add('cognito', tinymce.plugins.cognito);

})()

