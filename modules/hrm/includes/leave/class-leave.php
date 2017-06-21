<?php
namespace Pluginever\ERPCA\HRM\Leave;

use Pluginever\ERPCA\ERPCA_User;
use WeDevs\ERP\Framework\Traits\Hooker;

class Leave{
	use Hooker;
	use ERPCA_User;
	/**
	 * hrm constructor.
	 */
	public function __construct() {
		$this->includes();
		$this->hooks();
		$this->user_id = get_current_user_id();
	}


	/**
	 * Include a file from the includes directory
	 * @since  0.1.0
	 * @param  string $filename Name of the file to be included
	 */
	public function includes( ) {
		//leave
		require_once ERPCA_HRM_INCLUDES . '/leave/leave-functions.php';
		require_once ERPCA_HRM_INCLUDES . '/leave/admin-menu.php';
		require_once ERPCA_HRM_VIEWS . '/leave/class-leave-request-list-table.php';
		require_once ERPCA_HRM_EMAILS . '/leave/class-email-approved-leave-request.php';
		require_once ERPCA_HRM_EMAILS . '/leave/class-email-new-leave-request.php';
	}


	public function hooks(){
		$this->action('admin_init', 'restrict_old_leave_page');
		$this->action('erp_hr_leave_request_approved', 'update_leave_approve_to_custom_status', 10, 1);
		$this->action('erp_hr_leave_request_rejected', 'update_leave_reject_to_custom_status', 99, 1);
		$this->action('erp_new_leave_request_notification_recipients', 'send_leave_request_email_to_leave_agent', 10, 1);
		$this->action('erp_hr_leave_new', 'create_leave_entry_action', 10, 1);
		$this->filter('erp_hr_email_classes', 'hr_email_classes', 99);
	}

	/**
	 * Redirect user from original page to this plugin page
	 */
	public function restrict_old_leave_page(){
		global $pagenow;
		if($pagenow == 'admin.php' && isset($_GET['page']) && $_GET['page'] == 'erp-leave'){
			wp_redirect(admin_url('/admin.php?page=erp-leave-extended', 'http'), 301);
			exit;
		}
	}


	function update_leave_approve_to_custom_status($request_id){
		if($this->is_user_leave_agent()){
			erpca_update_leave_request_status($request_id, 12);
		}elseif ($this->is_user_hrm()){
			erpca_update_leave_request_status($request_id, 13);
		}else{
			erpca_update_leave_request_status($request_id, 1);
		}
	}


	/**
	 * Listen for leave request reject
	 * and change the status depending on who rejected
	 *
	 * @param $request_id
	 */
	public function update_leave_reject_to_custom_status($request_id){
		erpca_update_leave_request_status($request_id, 11);
	}

	/**
	 * Listen for new leave request admin notification
	 * and send that to leave agent
	 *
	 * @param $recipients
	 *
	 * @return array
	 */
	function send_leave_request_email_to_leave_agent($recipients){
		unset($recipients);
		$recipients = erpca_get_users_email_by_role(erpca_get_leave_agent_role());
		return $recipients;
	}

	/**
	 * Listen for new leave request and alter the status code to 11
	 * @param $request_id
	 */
	function create_leave_entry_action($request_id){
		error_log('got in custom code leave request');
		erpca_update_leave_request_status($request_id, 11);
	}

	public function hr_email_classes($emails){
		unset($emails['Approved_Leave_Request']);
		$emails['Approved_Leave_Request'] = new \Pluginever\ERPCA\HRM\Emails\Leave\Approved_Leave_Request();
		$emails['New_Leave_Request'] = new \Pluginever\ERPCA\HRM\Emails\Leave\New_Leave_Request();

		return $emails;
	}


}

new Leave();