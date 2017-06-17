<?php
/**
 * Leave statuses
 *  801 opened and pending for leave agent
 *  802 leave agent approved and waiting for HRM Manager
 *  1 active
 */


class ERPC_Leave_Management {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 999 );

	}
	/**
	 * Add menu items
	 *
	 * @return void
	 */
	public function admin_menu() {
		remove_menu_page('erp-leave');
		add_menu_page( __( 'Leave Management', 'erp' ), __( 'Leave', 'erp' ), 'erp_leave_manage', 'erp-leave-extended', array( $this, 'empty_page' ), 'dashicons-arrow-right-alt', null );

		$leave_request = add_submenu_page( 'erp-leave-extended', __( 'Requests', 'erp' ), __( 'Requests', 'erp' ), 'erp_leave_manage', 'erp-leave-extended', array( $this, 'leave_requests' ) );
		add_submenu_page( 'erp-leave-extended', __( 'Leave Entitlements', 'erp' ), __( 'Leave Entitlements', 'erp' ), 'erp_leave_manage', 'erp-leave-extended-assign', array( $this, 'leave_entitilements' ) );
		add_submenu_page( 'erp-leave-extended', __( 'Holidays', 'erp' ), __( 'Holidays', 'erp' ), 'erp_leave_manage', 'erp-holiday-assign', array( $this, 'holiday_page' ) );
		add_submenu_page( 'erp-leave-extended', __( 'Policies', 'erp' ), __( 'Policies', 'erp' ), 'erp_leave_manage', 'erp-leave-extended-policies', array( $this, 'leave_policy_page' ) );
	}





	/**
	 * Render the leave policy page
	 *
	 * @return void
	 */
	public function leave_policy_page() {
		include WPERP_HRM_VIEWS . '/leave/leave-policies.php';
	}

	/**
	 * Render the holiday page
	 *
	 * @return void
	 */
	public function holiday_page() {
		include WPERP_HRM_VIEWS . '/leave/holiday.php';
	}

	/**
	 * Render the leave entitlements page
	 *
	 * @return void
	 */
	public function leave_entitilements() {
		include WPERP_HRM_VIEWS . '/leave/leave-entitlements.php';
	}

	/**
	 * Render the leave entitlements calendar
	 *
	 * @return void
	 */
	public function leave_calendar_page() {
		include WPERP_HRM_VIEWS . '/leave/calendar.php';
	}

	/**
	 * Render the leave requests page
	 *
	 * @return void
	 */
	public function leave_requests() {
		$view = isset( $_GET['view'] ) ? $_GET['view'] : 'list';

		switch ($view) {
			case 'new':
				include WPERP_HRM_VIEWS . '/leave/new-request.php';
				break;

			default:
				include ERPCA_VIEWS . '/leave/requests.php';
				break;
		}
	}

	/**
	 * An empty page for testing purposes
	 *
	 * @return void
	 */
	public function empty_page() {

	}

}

new ERPC_Leave_Management();