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

require_once dirname(__FILE__) . '/../api.php';

$url = CognitoAPI::$formsBase;
if ($_GET['page'] == 'CognitoCreateForm') {
	$url = $url . 'forms/new';
} elseif ($_GET['page'] == "CognitoTemplates") {
	$url = $url . 'forms/templates';
}
?>

<iframe id="cognito-frame" src="<?php print $url; ?>" style="width:100%; overflow-x: hidden;"></iframe>

<style>
	body { overflow: hidden; }
</style>

<script language="javascript">
	var element = document.getElementById('cognito-frame');
	for (; element; element = element.previousSibling) {
		if (element.nodeType === 1 && element.id !== 'cognito-frame') {
			element.style.display = 'none';
		}
	}

	var adminheight = document.getElementById('wpadminbar').clientHeight;
	document.getElementById('cognito-frame').height = (document.body.clientHeight - adminheight) + "px";
	window.addEventListener("message", messageListener);
	window.addEventListener('resize', resizeListener);
	
	// Handler to watch for window resize to correctly update iframe height
	function resizeListener(event) {
		var adminheight = document.getElementById('wpadminbar').clientHeight;

		document.getElementById('cognito-frame').height = (document.body.clientHeight) + "px";
	}
	
	// Handler to listen for session tokens being broadcast from the Cognito iframe
	function messageListener(event) {
		var data = event.data;

		var found = data.indexOf("token:");
		if (found == 0) {
			var token = data.substring("token:".length);
			
			// If a session token was received, and no api key is present, update
			fetchApiKey(token);
		}
	}
	
	// Posts token to a hidden iframe so that a separate script can fetch necessary keys
	function fetchApiKey(token) {
		if (!token) return;
		var data = {
			action: "fetch_api_keys",
			token: token
		};
		
		jQuery.post(ajaxurl, data, function(response) { });
	}	
</script>
