<?php

	if (!defined('WP_UNINSTALL_PLUGIN')) {
		exit;
	}

	global $wpdb;
	$db_term = 'sgbv';

	// delete database table
	$table_name = $wpdb->prefix . $db_term;
	$wpdb->query("DROP TABLE IF EXISTS {$table_name}");

	// delete plugin options
	$table_name = $wpdb->prefix . 'options';
	$wpdb->query("DELETE FROM {$table_name}
		WHERE option_name LIKE '{$db_term}_%'");
