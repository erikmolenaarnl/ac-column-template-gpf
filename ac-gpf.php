<?php
/*
Plugin Name:        Admin Columns - Template for GPF
Plugin URI:         https://github.com/erikmolenaarnl/ac-column-template-gpf
Description:        Adds a custom column for Google Product Feed status and the product campaign priority.
Version:            1.1.1
Author:             Jory Hogeveen
Author URI:         https://www.keraweb.nl
GitHub Plugin URI:	https://github.com/erikmolenaarnl/ac-column-template-gpf
License:            GPLv2 or later
License URI:        http://www.gnu.org/licenses/gpl-2.0.html
Text Domain:        ac-column-template-gpf
*/

// 1. Set text domain
/* @link https://codex.wordpress.org/Function_Reference/load_plugin_textdomain */
load_plugin_textdomain( 'ac-gpf', false, plugin_dir_path( __FILE__ ) . '/languages/' );

// 2. Register the column.
add_action( 'ac/column_types', 'ac_register_column_gpf' );

function ac_register_column_gpf( \AC\ListScreen $list_screen ) {

	// Use the type: 'post', 'user', 'comment' or 'media'.
	if ( 'woocommerce' === $list_screen->get_group() ) {

		if ( ! class_exists( '\ACP\AdminColumnsPro' ) ) {

			require_once plugin_dir_path( __FILE__ ) . 'ac-column-gpf.php';

			$list_screen->register_column_type( new AC_Column_gpf );

		} else {

			// -------------------------------------- //
			// This part is for the PRO version only. //
			// -------------------------------------- //

			require_once plugin_dir_path( __FILE__ ) . 'ac-column-gpf.php';
			require_once plugin_dir_path( __FILE__ ) . 'acp-column-gpf.php';

			$list_screen->register_column_type( new ACP_Column_gpf );

		}
	}
}
