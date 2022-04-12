<?php

	class Simple_Googlebot_Visit_Activator {

		private $plugin_name;
		private $plugin_slug;
		private $version;
		private $db_term;

		public function __construct($plugin_name, $plugin_slug, $version, $db_term) {
			$this->plugin_name = $plugin_name;
			$this->plugin_slug = $plugin_slug;
			$this->version = $version;
			$this->db_term = $db_term;
		}

		public function activate() {
			//$this->drop_table_db();
			$this->create_table_db();
			$this->create_options();
		}

		public function deactivate() {
			//$this->drop_table_db();
			//$this->remove_options();
		}

		private function create_options() {
			update_option($this->db_term . '_version', $this->version);
			if (!get_option($this->db_term . '_active_pages')) {
				update_option($this->db_term . '_active_pages', 'true');
			}
			if (!get_option($this->db_term . '_active_entries')) {
				update_option($this->db_term . '_active_entries', 'true');
			}
			if (!get_option($this->db_term . '_active_products')) {
				update_option($this->db_term . '_active_products', 'true');
			}
			if (!get_option($this->db_term . '_active_custom_types')) {
				update_option($this->db_term . '_active_custom_types', '');
			}
		}

		private function remove_options() {
			global $wpdb;
			$table_name = $wpdb->prefix . 'options';
			$wpdb->query("DELETE FROM {$table_name} WHERE
				option_name LIKE '{$db_term}_%'");
		}

		private function create_table_db() {
			global $wpdb;
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			$charset_collate = $wpdb->get_charset_collate();
			$table_name = $wpdb->prefix . $this->db_term;
			$sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				date_created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
				date_updated timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
				post_id int DEFAULT NULL,
				term_id int DEFAULT NULL,
				device varchar(10) NOT NULL,
				agent varchar(350) NOT NULL,
				UNIQUE KEY id (id)
			) {$charset_collate}";
			dbDelta($sql);
		}

		private function drop_table_db() {
			global $wpdb;
			$table_name = $wpdb->prefix . $this->db_term;
			$wpdb->query("DROP TABLE IF EXISTS {$table_name}");
		}

	}
