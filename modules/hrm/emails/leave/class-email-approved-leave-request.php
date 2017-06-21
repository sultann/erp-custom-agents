<?php
namespace Pluginever\ERPCA\HRM\Emails\Leave;

use Pluginever\ERPCA\ERPCA_User;
use WeDevs\ERP\Email;
use WeDevs\ERP\Framework\Traits\Hooker;

/**
 * Approved Leave Request
 */
class Approved_Leave_Request extends Email {
	use ERPCA_User;
	use Hooker;

	function __construct() {
		error_log('Custom Email was ran');
		$this->id             = 'approved-leave-request';
		$this->title          = __( 'Approved Leave Request', 'erp' );
		$this->description    = __( 'Approved leave request notification to employee.', 'erp' );

		$this->subject        = __( 'Your leave request has been approved', 'erp');
		$this->heading        = __( 'Leave Request Approved', 'erp');

		$this->find = [
			'full-name'    => '{employee_name}',
			'leave_type'   => '{leave_type}',
			'date_from'    => '{date_from}',
			'date_to'      => '{date_to}',
			'no_days'      => '{no_days}',
			'reason'       => '{reason}',
		];

		$this->action( 'erp_admin_field_' . $this->id . '_help_texts', 'replace_keys' );

		parent::__construct();
		$this->user_id = get_current_user_id();
	}

	function get_args() {
		return [
			'email_heading' => $this->heading,
			'email_body'    => wpautop( $this->get_option( 'body' ) ),
		];
	}

	public function trigger( $request_id = null ) {

		$request = erp_hr_get_leave_request( $request_id );
		if(!$this->is_user_gm()) {
			// notification email
			$emailer = wperp()->emailer->get_email( 'New_Leave_Request' );

			if ( is_a( $emailer, '\WeDevs\ERP\Email') ) {
				$emailer->trigger( $request_id );
				return;
			}
		}
		if ( ! $request ) {
			return;
		}

		$employee          = new \WeDevs\ERP\HRM\Employee( intval( $request->user_id ) );

		$this->recipient   = $employee->user_email;
		$this->heading     = $this->get_option( 'heading', $this->heading );
		$this->subject     = $this->get_option( 'subject', $this->subject );

		$this->replace = [
			'full-name'    => $request->display_name,
			'leave_type'   => $request->policy_name,
			'date_from'    => erp_format_date( $request->start_date ),
			'date_to'      => erp_format_date( $request->end_date ),
			'no_days'      => $request->days,
			'reason'       => $request->reason,
		];

		if ( ! $this->get_recipient() ) {
			return;
		}
		$recipient = $this->get_recipient();

		$this->send( $recipient, $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
	}

}
