<?php

	class Simple_Googlebot_Visit_Public {

		private $plugin_name;
		private $version;
		private $db_term;
		private $googlebot_agents;
		private $active_types;

		public function __construct($plugin_name, $version, $db_term, $googlebot_agents, $active_types) {
			$this->plugin_name = $plugin_name;
			$this->version = $version;
			$this->db_term = $db_term;
			$this->googlebot_agents = $googlebot_agents;
			$this->active_types = $active_types;
		}

		public function add_visit_to_db($content) {
			global $wpdb, $post;
			if ($post && in_array(get_post_type(), $this->active_types, true)) {
				$table_name = $wpdb->prefix . $this->db_term;
				$googlebot_agent = $_SERVER['HTTP_USER_AGENT'];
				$googlebot_device = (strpos(strtolower($googlebot_agent), 'mobile') !== false) ? 'mobile' : 'desktop';
				foreach ($this->googlebot_agents as $key => $value) {
					if (strpos(strtolower($googlebot_agent), $value) !== false) {
						$post_id = get_the_ID();
						$db_results = $wpdb->get_results("SELECT * FROM ${table_name}
							WHERE post_id = {$post_id} AND device = '{$googlebot_device}'");
						if (count($db_results) > 0) {
							$wpdb->update($table_name, array(
								'date_updated' => current_time('mysql')
							), array(
								'post_id' => $post_id,
								'device' => $googlebot_device
							));
						} else {
							$wpdb->insert($table_name, array(
								'post_id' => $post_id,
								'device' => $googlebot_device,
								'agent' => $googlebot_agent
							));
						}
						break;
					}
				}
			}
			return $content;
		}

	}
