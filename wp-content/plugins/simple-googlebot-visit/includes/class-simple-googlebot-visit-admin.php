<?php

	class Simple_Googlebot_Visit_Admin {

		private $plugin_name;
		private $plugin_slug;
		private $version;
		private $db_term;
		private $active_types;

		public function __construct($plugin_name, $plugin_slug, $version, $db_term, $active_types) {
			$this->plugin_name = $plugin_name;
			$this->plugin_slug = $plugin_slug;
			$this->version = $version;
			$this->db_term = $db_term;
			$this->active_types = $active_types;
		}

		public function enqueue_scripts() {
			wp_register_script($this->plugin_name, plugin_dir_url(__FILE__) . '../assets/scripts/simple-googlebot-visit-admin.min.js', array('jquery'), $this->version, false);
			wp_localize_script($this->plugin_name, $this->db_term, array(
				'ajaxurl' => site_url() . '/wp-admin/admin-ajax.php',
				'nonce' => wp_create_nonce('wp_rest')
			));
			wp_enqueue_script($this->plugin_name);
		}

		public function enqueue_styles() {
			wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . '../assets/css/simple-googlebot-visit-admin.min.css', array(), $this->version, 'all');
		}

		public function add_button_to_settings() {
			add_submenu_page(
				'options-general.php',
				esc_html__($this->plugin_name, $this->plugin_slug),
				esc_html__($this->plugin_name, $this->plugin_slug),
				'administrator',
				$this->plugin_slug,
				array($this, 'admin_page')
			);
		}

		public function admin_page() {
			$template = new Simple_Googlebot_Visit_Template(plugin_dir_path(dirname(__FILE__)) . 'templates');
			foreach ($this->get_last_visits() as $key => $value) {
				$template->add($value, 'LAST_VISITS');
			}
			$template->add(array(
				'PLUGIN_NAME' => $this->plugin_name,
				'PLUGIN_LOGO' => plugin_dir_url(__FILE__) . '../assets/images/logo.png',
				'TITLE_SETTINGS' => __('Settings', $this->plugin_slug),
				'TITLE_LAST_VISITS' => __('Last visits', $this->plugin_slug),
				'TITLE_WHY' => __('Why Simple Googlebot Visit?', $this->plugin_slug),
				'TITLE_ABOUT' => __('About us', $this->plugin_slug),
				'FORM_BOOLEAN_OPTIONS' => array(
					array(
						'TEXT' => 'true',
						'VALUE' => 'true'
					),
					array(
						'TEXT' => 'false',
						'VALUE' => 'false'
					)
				),
				'FORM_ACTIVE_PAGES_VALUE' => get_option($this->db_term . '_active_pages') == 'true' ? 'true' : 'false',
				'FORM_ACTIVE_PAGES_LABEL' => __('<strong>Activate Simple Googlebot Visit for Wordpress pages:</strong> Check this box to collect information about googlebot visits and show this data in the Simple Googlebot Visit column of the pages management table.', $this->plugin_slug),
				'FORM_ACTIVE_ENTRIES_VALUE' => get_option($this->db_term . '_active_entries') == 'true' ? 'true' : 'false',
				'FORM_ACTIVE_ENTRIES_LABEL' => __('<strong>Activate Simple Googlebot Visit for Wordpress entries:</strong> Check this box to collect information about googlebot visits and show this data in the Simple Googlebot Visit column of the entries management table.', $this->plugin_slug),
				'FORM_ACTIVE_PRODUCTS_VALUE' => get_option($this->db_term . '_active_products') == 'true' ? 'true' : 'false',
				'FORM_ACTIVE_PRODUCTS_LABEL' => __('<strong>Activate Simple Googlebot Visit for Woocommerce products:</strong> Check this box to collect information about googlebot visits and show this data in the Simple Googlebot Visit column of the products management table of Woocommerce.', $this->plugin_slug),
				'FORM_ACTIVE_CUSTOM_TYPES_OBJECT' => array_map(function($value) {
					return array(
						'VALUE' => $value === 'post' || $value === 'product' || $value === 'page' ? '' : $value
					);
				}, $this->active_types),
				'FORM_ACTIVE_CUSTOM_TYPES_LABEL' => __('<strong>Activate Simple Googlebot Visit for custom post types:</strong> If, in addition to your Wordpress pages and entries or Woocommerce products, you need to track other types of posts, you can enter your custom post types (any type different to post, page or product) here.', $this->plugin_slug),
				'FORM_ACTIVE_CUSTOM_TYPES_INPUT_PLACEHOLDER' => __('Enter new post type...', $this->plugin_slug),
				'FORM_ACTIVE_CUSTOM_TYPES_REMOVE_BUTTON' => __('Remove', $this->plugin_slug),
				'BODY_WHY' => __('<p><strong>Simple Googlebot Visit</strong> is a plugin that lets you know at all times the last time that the googlebot visited any of your content. This is important because it is in charge of indexing all your pages and showing them in the search results of Google. Thanks to <strong>Simple Googlebot Visit</strong> you have this valuable information integrated into your Wordpress administration panel, without having to integrate tools such as Search Console.</p>', $this->plugin_slug),
				'BODY_ABOUT' => __('<p>We are <a href="https://codents.net" target="_blank">Codents</a>, a spanish company made up of young people who love programming and new technologies. We have been working with Wordpress and plugins for years, but it has not been until now that we have decided to upload tools as we think they can be useful for the community. If you like <strong>Googlebot Simple Visit</strong> you can rate it with <a href="https://wordpress.org/support/plugin/simple-googlebot-visit/reviews/?filter=5#new-post" target="_blank" rel="noopener noreferrer">★★★★★</a> at <a href="https://wordpress.org/support/plugin/simple-googlebot-visit/reviews/?filter=5#new-post" target="_blank" rel="noopener">WordPress.org</a>, it will help us to continue creating free plugins like this one.</p>', $this->plugin_slug),
				'FOOTER_TEXT' => __('Rate <strong>Simple Googlebot Visit</strong> with <a href="https://wordpress.org/support/plugin/simple-googlebot-visit/reviews/?filter=5#new-post" target="_blank" rel="noopener noreferrer">★★★★★</a> at <a href="https://wordpress.org/support/plugin/simple-googlebot-visit/reviews/?filter=5#new-post" target="_blank" rel="noopener">WordPress.org</a> and help us to continue creating free plugins. Thank you!', $this->plugin_slug)
			));
			$template->render('simple_googlebot_visit_admin.tpl');
		}

		public function add_column($columns) {
			$columns[$this->plugin_slug] = __('Googlebot', $this->plugin_slug);
			return $columns;
		}

		public function add_column_value_by_post($column_name, $post_id) {
			if ($column_name === $this->plugin_slug) {
				echo $this->add_column_value('post', $post_id);
			}
		}

		public function add_column_value_by_term($column_name, $term_id) {
			if ($column_name === $this->plugin_slug) {
				echo $this->add_column_value('post', $term_id);
			}
		}

		public function add_column_value($type, $search_id) {
			global $wpdb;
			$table_name = $wpdb->prefix . $this->db_term;
			$table_column = ($type === 'post') ? 'post_id' : 'term_id';
			$db_result = $wpdb->get_results("SELECT * FROM {$table_name} 
				WHERE {$table_column} = {$search_id} ORDER BY date_updated DESC LIMIT 1");
			if (!$db_result) {
				echo __('No registered visit', $this->plugin_slug);
			} else {
				$datetime = $this->get_datetime_with_format($db_result[0]->date_updated);
				$value = '<span>' . __('Last visit', $this->plugin_slug) . '</span>';
				$value .= '<span title="' . $datetime . '">' . $datetime . '</span>';
				echo $value;
			}
		}

		public function save_settings() {
			if (is_admin()) {
				require_once(ABSPATH . 'wp-includes/option.php');
				update_option($this->db_term . '_active_pages', $_POST['active_pages'] == 'true' ? 'true' : 'false');
				update_option($this->db_term . '_active_entries', $_POST['active_entries'] == 'true' ? 'true' : 'false');
				update_option($this->db_term . '_active_products', $_POST['active_products'] == 'true' ? 'true' : 'false');
				update_option($this->db_term . '_active_custom_types', $_POST['active_custom_types']);
				wp_send_json_success();
			} else {
				wp_send_json_error();
			}
		}

		public function get_datetime_with_format($datetime) {
			$datetimeFormat = get_option('date_format') . ' ' . get_option('time_format');
			return date_i18n($datetimeFormat, strtotime($datetime));
		}

		public function get_last_visits() {
			global $wpdb;
			$last_visits = array();
			$table_name = $wpdb->prefix . $this->db_term;
			$db_result = $wpdb->get_results("SELECT * FROM {$table_name} ORDER BY date_updated DESC LIMIT 20");
			foreach ($db_result as $key => $value) {
				array_push($last_visits, array(
					'MOBILE' => $value->device === 'mobile' ? true : false,
					'URL' => get_permalink($value->post_id),
					'DATE' => $this->get_datetime_with_format($value->date_updated)
				));
			}
			return $last_visits;
		}

	}
