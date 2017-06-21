<?php
namespace Pluginever\ERPCA\HRM;


class hrm{

	/**
	 * hrm constructor.
	 */
	public function __construct() {
		$this->define_constants();
		$this->includes();
		$this->hooks();
	}


	/**
	 * Define Add-on constants
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function define_constants() {
		define( 'ERPCA_HRM_PATH', dirname(__FILE__) );
		define( 'ERPCA_HRM_INCLUDES', ERPCA_HRM_PATH . '/includes' );
		define( 'ERPCA_HRM_EMAILS', ERPCA_HRM_PATH . '/emails' );
		define( 'ERPCA_HRM_VIEWS', ERPCA_HRM_PATH . '/views' );
	}


	/**
	 * Include a file from the includes directory
	 * @since  0.1.0
	 * @param  string $filename Name of the file to be included
	 */
	public function includes( ) {
		//leave
		require_once ERPCA_HRM_INCLUDES . '/leave/class-leave.php';
	}


	public function hooks(){

	}



}


new hrm();