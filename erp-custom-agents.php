<?php
/**
 * Plugin Name: ERP Custom Agents
 * Plugin URI:  http://pluginever.com
 * Description: The best WordPress plugin ever made!
 * Version:     0.1.0
 * Author:      PluginEver
 * Author URI:  http://pluginever.com
 * Donate link: http://pluginever.com
 * License:     GPLv2+
 * Text Domain: erp_custom_agents
 * Domain Path: /languages
 */

/**
 * Copyright (c) 2017 PluginEver (email : support@pluginever.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

// don't call the file directly
if ( !defined( 'ABSPATH' ) ) exit;
/**
 * Main initiation class
 */
include( dirname( __FILE__ ) . '/lib/requirements-check.php' );

class Erp_Custom_Agents {

	public $version = '1.0.0';

	public $dependency_plugins = [];

	
	/**
	 * Sets up our plugin
	 * @since  0.1.0
	 */
	public function __construct() {

		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
		add_action( 'admin_init', array( $this, 'admin_hooks' ) );
		add_action( 'init', [ $this, 'localization_setup' ] );
		$this->define_constants();
		$this->includes();
		add_action('wp_enqueue_scripts', [$this, 'load_assets']);
	}

	/**
	 * Activate the plugin
	 */
	function activate() {
		// Make sure any rewrite functionality has been loaded
		flush_rewrite_rules();
	}

	/**
	 * Deactivate the plugin
	 * Uninstall routines should be in uninstall.php
	 */
	function deactivate() {

	}

	/**
	 * Initialize plugin for localization
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function localization_setup() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'erp_custom_agents' );
		load_textdomain( 'erp_custom_agents', WP_LANG_DIR . '/erp_custom_agents/erp_custom_agents-' . $locale . '.mo' );
		load_plugin_textdomain( 'erp_custom_agents', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}



	/**
	 * Hooks for the Admin
	 * @since  0.1.0
	 * @return null
	 */
	public function admin_hooks() {

	}

	/**
	 * Include a file from the includes directory
	 * @since  0.1.0
	 * @param  string $filename Name of the file to be included
	 */
	public function includes( ) {
		require ERPCA_INCLUDES .'/functions.php';
	}


	/**
	 * Define Add-on constants
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function define_constants() {
		define( 'ERPCA_VERSION', $this->version );
		define( 'ERPCA_FILE', __FILE__ );
		define( 'ERPCA_PATH', dirname( ERPCA_FILE ) );
		define( 'ERPCA_INCLUDES', ERPCA_PATH . '/includes' );
		define( 'ERPCA_URL', plugins_url( '', ERPCA_FILE ) );
		define( 'ERPCA_ASSETS', ERPCA_URL . '/assets' );
		define( 'ERPCA_VIEWS', ERPCA_PATH . '/views' );
		define( 'ERPCA_TEMPLATES_DIR', ERPCA_PATH . '/templates' );
	}

	
	/**
	 * Add all the assets required by the plugin
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	function load_assets(){
		wp_register_style('erp-custom-agents', ERPCA_ASSETS.'/css/erp-custom-agents.css', [], date('i'));
		wp_register_script('erp-custom-agents', ERPCA_ASSETS.'/js/erp-custom-agents.js', ['jquery'], date('i'), true);
		wp_localize_script('erp-custom-agents', 'jsobject', ['ajaxurl' => admin_url( 'admin-ajax.php' )]);
		wp_enqueue_style('erp-custom-agents');
		wp_enqueue_script('erp-custom-agents');
	}



	/**
	 * Display an error message if WP ERP is not active
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function admin_notice($type='error', $message) {
		printf(
			'%s'. __( $message, 'erp_custom_agents' ) . '%s',
			'<div class="message '.$type.'"><p>',
			'</p></div>'
		);
	}





}

// init our class
$GLOBALS['Erp_Custom_Agents'] = new Erp_Custom_Agents();

/**
 * Grab the $Erp_Custom_Agents object and return it
 */
function erp_custom_agents() {
	global $Erp_Custom_Agents;
	return $Erp_Custom_Agents;
}
