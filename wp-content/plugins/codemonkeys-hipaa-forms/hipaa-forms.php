<?php
/**
 * Created by Code Monkeys LLC
 * http://www.codemonkeysllc.com
 * User: Spencer
 * Date: 11/27/2017
 * Time: 4:33 PM
 *
 * Plugin Name: HIPAA Forms
 * Plugin URI: https://www.hipaaforms.online
 * Description: HIPAA Compliant Forms
 * Version: 2.2.5
 * Author: Code Monkeys LLC
 * Author URI: https://www.codemonkeysllc.com
 * License: GPL2
 */

//* REQUIRE ENQUEUE FILE
require(plugin_dir_path(__FILE__) . 'enqueue.php');

//* REQUIRE HIPAA FORMS OPTIONS
require(plugin_dir_path(__FILE__) . 'includes/options.php');

//* REQUIRE CLASS FILE
require(plugin_dir_path(__FILE__) . 'includes/class-cm-hipaa.php');

//* REQUIRE AJAX FUNCTIONS
require(plugin_dir_path(__FILE__) . 'ajax-functions.php');

//* REQUIRE USER ROLE
require(plugin_dir_path(__FILE__) . 'user-role.php');

//* REQUIRE ADMIN PAGE
require(plugin_dir_path(__FILE__) . 'admin-page.php');