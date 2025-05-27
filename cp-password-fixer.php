<?php
/**
 * Plugin Name:  ClassicPress password fixer
 * Description:  Fix password when migrated from WP 6.8 or above
 * Version:      1.0.0
 * License:      GPL2
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 * Author:       Simone Fioravanti
 * Requires CP:  2.0
 * Requires PHP: 7.4
 */

if (!defined('ABSPATH')) {
	die('-1');
}

global $cp_version;

if (!isset($cp_version)) {
	// Running on WordPress? Stop.
	return;
}

if (version_compare($cp_version, '2.4.1', 'gt')) {
	// ClassicPress version already patched?
	cppf_no_more_useful();
	return;
}

require __DIR__.'/pluggable.php';

// Support functions.

function cppf_no_more_useful() {
	add_action('after_plugin_row', 'cppf_no_more_useful_nag', 10, 2);
}

function cppf_no_more_useful_nag($plugin_file, $plugin_data) {
	if ($plugin_file !== 'cp-password-fixer/cp-password-fixer.php') {
		return;
	}
	?>
	<tr class="plugin-update-tr active">
		<td colspan="4"  class="plugin-update colspanchange" style="box-shadow: none;">
	<?php
	wp_admin_notice(
		'This plugin is no more necessary as you are using an updated version of ClassicPress.',
		[
			'type'               => 'warning',
			'additional_classes' => ['inline'],
		]
	);
	?>
		</td>
	</tr>
	<script>
		document.querySelector('tr[data-plugin="<?php echo esc_html($plugin_file); ?>"').classList.add('update');
	</script>
	<?php
}
