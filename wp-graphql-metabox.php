<?php

/**
 * Plugin Name: WPGraphQL Meta Box Integration
 * Plugin URI: https://github.com/hsimah/wp-graphql-metabox
 * Description: WP GraphQL provider for Meta Box
 * Author: hsimah
 * Author URI: http://www.hsimah.com
 * Version: 0.0.1
 * Text Domain: wpgraphql-metabox
 * License: GPL-3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package  WPGraphQL_MetaBox
 * @author   hsimah
 * @version  0.0.1
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

if (!class_exists('WPGraphQL_MetaBox')) {

	add_action('admin_init', function () {
		$wp_graphql_required_min_version = '0.3.2';

		if (!class_exists('RWMB_Loader') || !class_exists('WPGraphQL') || (defined('WPGRAPHQL_VERSION') && version_compare(WPGRAPHQL_VERSION, $wp_graphql_required_min_version, 'lt'))) {

			/**
			 * For users with lower capabilities, don't show the notice
			 */
			if (!current_user_can('manage_options')) {
				return false;
			}

			add_action(
				'admin_notices',
				function () use ($wp_graphql_required_min_version) {
					?>
				<div class="error notice">
					<p>
						<?php _e(sprintf('Both WPGraphQL (v%s+) and Meta Box (v5.2.3) must be active for "wp-graphql-metabox" to work', $wp_graphql_required_min_version), 'wpgraphql-metabox'); ?>
					</p>
				</div>
<?php
			}
		);

		return false;
	}
});

if (class_exists('RWMB_Loader') && class_exists('WPGraphQL'))
	require_once __DIR__ . '/class-metabox.php';
}
