<?php

/**
 * @wordpress-plugin
 * Plugin Name:       Simple Googlebot Visit
 * Description:       Plugin to view the last visit of googlebot to your pages and entries.
 * Version:           1.2.3
 * Author:            Codents
 * Author URI:        https://codents.net
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       simple-googlebot-visit
 * Domain Path:       /languages
 */

if (!defined('WPINC')) {
	die;
}

define('SGBV_NAME', 'Simple Googlebot Visit');
define('SGBV_SLUG', 'simple-googlebot-visit');
define('SGBV_VERSION', '1.2.3');
define('SGBV_DB_TERM', 'sgbv');
define('SGBV_GOOGLEBOT_AGENTS', array('googlebot'));

function activate_simple_googlebot_visit() {
	require_once plugin_dir_path(__FILE__) . 'includes/class-simple-googlebot-visit-activator.php';
	$activator = new Simple_Googlebot_Visit_Activator(SGBV_NAME, SGBV_SLUG, SGBV_VERSION, SGBV_DB_TERM);
	$activator->activate();
}

function deactivate_simple_googlebot_visit() {
	require_once plugin_dir_path(__FILE__) . 'includes/class-simple-googlebot-visit-activator.php';
	$activator = new Simple_Googlebot_Visit_Activator(SGBV_NAME, SGBV_SLUG, SGBV_VERSION, SGBV_DB_TERM);
	$activator->deactivate();
}

register_activation_hook(__FILE__, 'activate_simple_googlebot_visit');
register_deactivation_hook(__FILE__, 'deactivate_simple_googlebot_visit');

require plugin_dir_path(__FILE__) . 'includes/class-simple-googlebot-visit.php';

function run_simple_googlebot_visit() {
	$plugin = new Simple_Googlebot_Visit(SGBV_NAME, SGBV_SLUG, SGBV_VERSION, SGBV_DB_TERM, SGBV_GOOGLEBOT_AGENTS);
	$plugin->run();
}

run_simple_googlebot_visit();
