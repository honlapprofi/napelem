<?php

	class Simple_Googlebot_Visit {

		protected $loader;
		protected $plugin_name;
		protected $plugin_slug;
		protected $version;
		protected $db_term;
		protected $googlebot_agents;

		public function __construct($plugin_name, $plugin_slug, $version, $db_term, $googlebot_agents) {
			$this->plugin_name = $plugin_name;
			$this->plugin_slug = $plugin_slug;
			$this->version = $version;
			$this->db_term = $db_term;
			$this->googlebot_agents = $googlebot_agents;
			$this->check_table_changes();
			$this->load_dependencies();
			$this->set_locale();
			$this->define_admin_hooks();
			$this->define_public_hooks();
		}

		private function check_table_changes() {
			global $wpdb;
			$table_name = $wpdb->prefix . $this->db_term;
			$db_result = $wpdb->query("SHOW COLUMNS FROM `{$table_name}` LIKE 'term_id'");
			if (is_numeric($db_result) && $db_result <= 0) {
				require_once plugin_dir_path(__FILE__) . 'class-simple-googlebot-visit-activator.php';
				$activator = new Simple_Googlebot_Visit_Activator($this->plugin_name, $this->plugin_slug, $this->version, $this->db_term);
				$activator->activate();
			}
		}

		private function load_dependencies() {
			require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-simple-googlebot-visit-loader.php';
			require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-simple-googlebot-visit-i18n.php';
			require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-simple-googlebot-visit-admin.php';
			require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-simple-googlebot-visit-public.php';
			require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-simple-googlebot-visit-template.php';
			$this->loader = new Simple_Googlebot_Visit_Loader();
		}

		private function set_locale() {
			$plugin_i18n = new Simple_Googlebot_Visit_i18n($this->plugin_slug);
			$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
		}

		private function define_admin_hooks() {
			$plugin_admin = new Simple_Googlebot_Visit_Admin($this->get_plugin_name(), $this->plugin_slug, $this->get_version(), $this->db_term,
			$this->get_active_post_types());
			foreach ($this->get_active_post_types() as &$value) {
				$this->loader->add_filter("manage_{$value}_posts_columns", $plugin_admin, 'add_column');
				$this->loader->add_action("manage_{$value}_posts_custom_column", $plugin_admin, 'add_column_value_by_post', 10, 2);
			}
			$this->loader->add_action('wp_ajax_' . $this->db_term . '_save', $plugin_admin, 'save_settings');
			$this->loader->add_action('wp_ajax_nopriv_' . $this->db_term . '_save', $plugin_admin, 'save_settings');
			$this->loader->add_action('admin_menu', $plugin_admin, 'add_button_to_settings', 9);
			$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
			$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
		}

		private function define_public_hooks() {
			$plugin_public = new Simple_Googlebot_Visit_Public(
				$this->get_plugin_name(),
				$this->get_version(),
				$this->db_term,
				$this->googlebot_agents,
				$this->get_active_post_types()
			);
			$this->loader->add_filter('the_content', $plugin_public, 'add_visit_to_db');
		}

		private function get_active_post_types() {
			$types = array();
			if (get_option($this->db_term . '_active_entries') == 'true') {
				array_push($types, 'post');
			}
			if (get_option($this->db_term . '_active_pages') == 'true') {
				array_push($types, 'page');
			}
			if (get_option($this->db_term . '_active_products') == 'true') {
				array_push($types, 'product');
			}
			foreach (explode(',', get_option($this->db_term . '_active_custom_types')) as &$value) {
				$value = trim($value);
				if ($value && !in_array($value, $types, true)) {
					array_push($types, $value);
				}
			}
			return $types;
		}

		public function run() {
			$this->loader->run();
		}

		public function get_plugin_name() {
			return $this->plugin_name;
		}

		public function get_loader() {
			return $this->loader;
		}

		public function get_version() {
			return $this->version;
		}

	}
