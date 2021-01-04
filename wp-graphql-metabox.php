<?php

/**
 * Plugin Name: WPGraphQL Meta Box Integration
 * Plugin URI: https://github.com/hsimah-services/wp-graphql-metabox
 * Description: WP GraphQL provider for Meta Box
 * Author: hsimah
 * Author URI: http://www.hsimah.com
 * Version: 0.3.1
 * Text Domain: wpgraphql-metabox
 * License: GPL-3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package  WPGraphQL_MetaBox
 * @author   hsimah
 * @version  0.3.1
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

if (!class_exists('WPGraphQL_MetaBox')) {

	add_action('admin_init', function () {
		$versions = [
			'wp-graphql' => '1.0.4',
			'metabox' => '5.3.3'
		];

		if (
			!class_exists('RWMB_Loader') ||
			!class_exists('WPGraphQL') ||
			(defined('WPGRAPHQL_VERSION') && version_compare(WPGRAPHQL_VERSION, $versions['wp-graphql'], 'lt')) ||
			(defined('RWMB_VER') && version_compare(RWMB_VER, $versions['metabox'], 'lt'))
		) {

			/**
			 * For users with lower capabilities, don't show the notice
			 */
			if (!current_user_can('manage_options')) {
				return false;
			}

			add_action(
				'admin_notices',
				function () use ($versions) {
?>
				<div class="error notice">
					<p>
						<?php _e(vsprintf('Both WPGraphQL (v%s+) and Meta Box (v%s+) must be active for "wp-graphql-metabox" to work', $versions), 'wpgraphql-metabox'); ?>
					</p>
				</div>
<?php
				}
			);

			return false;
		}
	});

	require_once __DIR__ . '/class-metabox.php';
}
