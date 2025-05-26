<?php
/**
 * Plugin Name:  ClassicPress password fixer
 * Description:  Fix password when migrated from WP 6.8 or above
 * Version:      0.9.0
 * License:      GPL2
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 * Author:       Gieffe edizioni srl
 * Requires CP:  2.0
 * Requires PHP: 7.4
 */

if (!defined('ABSPATH')){
	die('-1');
};


function render_page (){
	echo "<h1>Welcome to the sandbox</h1>";
	echo '<pre>';
	echo classicpress_version();
	echo "\n";
	echo version_compare('2.4.1+dev', '2.4.1', 'gt') ? 'exit' : 'run'; // -1
	echo'</pre>';
}

add_action('admin_menu', 'create_menu', 100);

function create_menu() {
	if (current_user_can('edit_posts')) {
		$page = add_menu_page(
			'xxx',
			'xxx',
			'edit_posts',
			'xxx',
			'render_page'
		);
	}
}



