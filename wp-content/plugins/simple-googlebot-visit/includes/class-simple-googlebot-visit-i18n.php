<?php

	class Simple_Googlebot_Visit_i18n {

		public function load_plugin_textdomain($plugin_slug) {
			load_plugin_textdomain(
				$plugin_slug,
				false,
				dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
			);
		}

	}
