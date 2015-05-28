<?php

/**
 * During delete files (uninstall process): delete options
 *
 * @since 2.0
 *
 *
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	echo 'Impossible to erase xili-theme-select plugin!';
	exit();
}

/*
 * Class to manage xili-theme-select uninstallation
 *
 * @since 2.0
 */
class xili_theme_select_uninstall {

	function __construct() {
		global $wpdb;

		// check if it is a multisite uninstall - if so, run the uninstall function for each blog id
		if (is_multisite()) {
			foreach ($wpdb->get_col("SELECT blog_id FROM $wpdb->blogs") as $blog_id) {
				switch_to_blog($blog_id);
				$this->uninstall($blog_id);
			}
			restore_current_blog($blog_id);
		}
		else {
			$this->uninstall();
		}
	}

	/*
	 * removes All plugin datas before deleting plugin files if option set
	 *
	 * @since 2.0
	 */
	function uninstall( $blog_id = 1 ) {

	 	delete_option( 'xilithemeselect_options' ); // version < 2.0
	 	delete_option( 'xilitheme-select-settings' );

	}

}

new xili_theme_select_uninstall();
?>