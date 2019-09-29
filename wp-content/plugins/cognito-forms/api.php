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

// Cognito API access
class CognitoAPI {
	public static $servicesBase = 'https://services.cognitoforms.com/';
	public static $formsBase = 'https://www.cognitoforms.com/';
	
	// Fetches all forms for an organization
	// $api_key - API Key for the organization
	public static function get_forms($api_key) {
		$response = wp_remote_fopen(self::$servicesBase . 'forms/api/' . $api_key . '/forms');

		return $response;
	}
	
	// Fetches organization information for a given member
	// $session_token - Valid session token
	public static function get_organization($session_token) {
		$response = wp_remote_fopen(self::$servicesBase . 'member/admin/organization?token=' . urlencode($session_token));	
		$organization = json_decode($response);

		return $organization;
	}
	
	// Builds form embed script
	public static function get_form_embed_script($public_key, $formId) {
		$base = self::$servicesBase;
		return <<< EOF
			<div class="cognito">
				<script src="{$base}session/script/{$public_key}"></script>
				<script>Cognito.load("forms", { id: "{$formId}" });</script>
			</div>
EOF;
	}

	// Builds Cognito module embed script
	public static function get_embed_script($key, $module) {
		$base = self::$servicesBase;
		return <<< EOF
			<div class="cognito">
				<script src="{$base}session/script/{$key}"></script>
				<script>Cognito.load("{$module}");</script>
			</div>
EOF;
	}
}

?>