<?php

/*
Plugin Name: Cognito Forms
Plugin URI: http://wordpress.org/plugins/cognito-forms/
Description: Cognito Forms is a free online form builder that integrates seemlessly with WordPress. Create contact forms, registrations forms, surveys, and more!
Version: 1.1.7
Author: Cognito Apps
Author URI: https://www.cognitoforms.com
*/

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

require_once dirname(__FILE__) . '/api.php';

// The Cognito Plugin!
class Cognito_Plugin {
	// Initialization actions
	private static $actions = array(
		'admin_init',
		'admin_menu',
		'wp_ajax_fetch_api_keys',
		'wp_ajax_get_forms',
		'wp_enqueue_scripts'
	);
	
	// Supported shortcodes
	private static $shortcodes = array(
		'Cognito' => 'renderCognitoShortcode',
		'cognito' => 'renderCognitoShortcode',
		'CognitoForms' => 'renderCognitoFormsShortcode',
		'cognitoforms' => 'renderCognitoFormsShortcode'
	);
	
	// Entrypoint
	public function __construct() {
		$this->addActions(self::$actions);
		$this->addShortcodes(self::$shortcodes);
	}
	
	// Initialize plug-in
	public function admin_init() {
		if(!current_user_can('edit_posts') && !current_user_can('edit_pages')) return;		
		
		register_setting('cognito_plugin', 'cognito_api_key');
		register_setting('cognito_plugin', 'cognito_admin_key');
		register_setting('cognito_plugin', 'cognito_public_key');
		register_setting('cognito_plugin', 'cognito_organization');
		
		// If the flag to delete options was passed-in, delete them
		if (isset($_GET['cog_clear']) && $_GET['cog_clear'] == '1') {
			delete_option('cognito_api_key');
			delete_option('cognito_admin_key');
			delete_option('cognito_public_key');
			delete_option('cognito_organization');
		}
		
		// Add tinyMCE plug-in
        if(get_user_option('rich_editing') == 'true') {
			$this->addfilters(array( 
				'mce_buttons',
				'mce_external_plugins'                
            ));
		}
	}

	// Register required scripts
	public function wp_enqueue_scripts() {
		wp_enqueue_script('jquery');
	}
	
	// Ajax callback to allow fetching of API keys based on session token
	public function wp_ajax_fetch_api_keys() {
		$organization = CognitoAPI::get_organization($_POST['token']);

		if (!is_null($organization)) {
			delete_option('cognito_api_key');
			delete_option('cognito_admin_key');
			delete_option('cognito_public_key');
			delete_option('cognito_organization');
		
			update_option('cognito_api_key', $organization->apiKey);
			update_option('cognito_admin_key', $organization->adminKey);
			update_option('cognito_public_key', $organization->publicKey);
			update_option('cognito_organization', $organization->code);
		}
		
		die;
	}
	
	// Ajax callback to allow fetching of forms for a given organization
	public function wp_ajax_get_forms() {
		$api_key = get_option('cognito_api_key');
		
		if ($api_key) {
			$forms = CognitoAPI::get_forms($api_key);

			echo $forms;		
		}		
		
		die;
	}
	
	// Initialize administration menu (left-bar)
	public function admin_menu() {
		add_menu_page('Cognito Forms', 'Cognito Forms', 'manage_options', 'Cognito', array($this, 'main_page'), '../wp-content/plugins/cognito-forms/cogicon.ico');
		add_submenu_page('Cognito', 'Cognito Forms', 'View Forms', 'manage_options', 'Cognito', array($this, 'main_page'));
		add_submenu_page('Cognito', 'Create Form', 'New Form', 'manage_options', 'CognitoCreateForm', array($this, 'main_page'));
		add_submenu_page('Cognito', 'Templates', 'Templates', 'manage_options', 'CognitoTemplates', array($this, 'main_page'));
		
		add_options_page('Cognito Options', 'Cognito Forms', 'manage_options', 'CognitoOptions', array($this, 'options_page'));
    }

	// Called when a 'Cognito' shortcode is encountered, renders embed script
	public function renderCognitoShortcode($atts, $content = null, $code = '') {
		// Default to key setting, unless overridden in shortcode (allows for modules from multiple orgs)
		$key = empty($atts['key']) ? get_option('cognito_public_key') : $atts['key'];
		if (empty($atts['module']) || empty($atts['key'])) return '';
		
		return CognitoAPI::get_embed_script($key, $atts['module']);
	}
	
	// Called when a 'CognitoForms' shortcode is encountered, renders form embed script
	public function renderCognitoFormsShortcode($atts, $content = null, $code = '') {
		// Default to key setting, unless overridden in shortcode (allows for modules from multiple orgs)
		$key = empty($atts['key']) ? get_option('cognito_public_key') : $atts['key'];
		if (empty($atts['id']) || empty($key)) return '';
		
		return CognitoAPI::get_form_embed_script($key, $atts['id']);
	}

	// Entrypoint for Cognito Forms access
	public function main_page() {		
		include 'tmpl/main.php';
	}
	
	public function options_page() {
		include 'tmpl/options.php';
	}
	
	// Set up tinyMCE buttons
	public function mce_buttons($buttons) {
		array_push($buttons, '|', 'cognito');
		return $buttons;
	}

	// Initialize tinyMCE plug-in
	public function mce_external_plugins($plugins) {
		$plugins['cognito'] = plugin_dir_url( __FILE__ ) . 'tinymce/plugin.js';
		return $plugins;
	}

	// Registers plug-in actions
    private function addActions($actions) {
        foreach($actions as $action)
            add_action($action, array($this, $action));
    }

	// Registers shortcodes
    private function addShortcodes($shortcodes) {
        foreach($shortcodes as $tag => $func)
            add_shortcode($tag, array($this, $func));
    }
	
	// Registers tinyMCE filters
	private function addFilters($filters) {
		foreach($filters as $filter)
			add_filter($filter, array($this, $filter));
	}
}

new Cognito_Plugin;
?>