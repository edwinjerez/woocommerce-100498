<?php
/**
 * Cognito Forms WordPress Plugin.
 *
 * The Cognito Forms WordPress Plugin is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * The Cognito Forms WordPress Plugin is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
 ?>
<!DOCTYPE html>
<html>
	<head>
		<style>
			body { overflow: hidden; }
			button{margin-top:20px;color: #fff !important;text-shadow: 0 1px 1px rgba(0,0,0,.3);outline: none;cursor: pointer;text-align: center;text-decoration: none;font: 14px/100% 'Open Sans Condensed', sans-serif !important;font-weight: 700 !important;padding: 5px 15px 6px;border: solid 1px #95ba14;background: #aed136;}
			.clearlooks2 .mceTop .mceLeft, .clearlooks2 .mceTop .mceRight{background:#00B4AC;padding:20px;font-family: 'Open Sans Condensed', Arial, Helvetica, sans-serif;font-size: 18px;font-weight: 700;color: #fff;}
			body{padding: 10px 20px!important;font-family: 'Open Sans', Arial, Helvetica, sans-serif;font-size: 14px;font-weight: 400;}
			h3 {margin-bottom:20px!important;color:#444!important;}
		</style>
		<script src="../../../../wp-includes/js/jquery/jquery.js"></script>
		<script src="../../../../wp-includes/js/tinymce/tiny_mce_popup.js"></script>
		<script src="../../../../wp-includes/js/tinymce/utils/mctabs.js"></script>
		<script src="../../../../wp-includes/js/tinymce/utils/form_utils.js"></script>
		<title>Cognito Forms</title>
		<script>
			var titleElements = tinyMCEPopup.getWin().document.querySelectorAll('.mceTop .mceLeft, .mceTop .mceRight, .mce-title');
			for (var i = 0; i < titleElements.length; i++) {
				titleElements[i].style.background = '#00B4AC';
				titleElements[i].style.color = '#fff';
			}

			var closeElement = tinyMCEPopup.getWin().document.querySelector('.mceClose, .mce-close');
			if (closeElement) closeElement.style.color = '#fff';
			
			function cognito_submit() {
				var formSelect = document.getElementById('formSelect');
				var shortcode = '[CognitoForms id="' + formSelect.value + '"]';
				
				if (window.tinyMCE) {
					if (window.tinyMCE.execInstanceCommand) {
						window.tinyMCE.execInstanceCommand('content', 'mceInsertContent', false, shortcode);
						tinyMCEPopup.editor.execCommand('mceRepaint');
					}
					else if (window.tinyMCE.focusedEditor) {
						window.tinyMCE.focusedEditor.insertContent(shortcode);
					}
					
					tinyMCEPopup.close();
				}
				
				return false;
			}
			
			jQuery(function() {
				var data = {
					action: "get_forms"
				};
				jQuery.post(tinyMCEPopup.params.ajax_url, data, function(response) { 
					if (response) {
						var forms = JSON.parse(response);
						
						var formSelect = jQuery("#formSelect");
						jQuery.each(forms, function() {
							formSelect.append(jQuery("<option></option>")
								.attr("value", this.Id)
								.text(this.Name));
						});
						
						jQuery("#form-list").show();
					} else {
						jQuery("#no-forms").show();
					}
				});
			});			
		</script>
	</head>
	
	<body>
		<div id="no-forms" style="display:none;">
			<h3>No key present</h3>
			<p>Please click on the "Cognito Forms" link in the menu on the left and log in to register this plug-in with your account.</p>
		</div>
		
		<div id="form-list" style="display:none;">
			<h3>Embed a Form</h3>
			<form method="post" action="">
				<label for="formSelect">Choose a form</label>
				<select id="formSelect">
				</select><br/>
				<button id="cognito-insert-form" type="button" onclick="cognito_submit();">Insert Form</button>
			</form>
		</div>
	</body>
</html>