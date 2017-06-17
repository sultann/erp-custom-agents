<?php
// don't call the file directly
if ( !defined( 'ABSPATH' ) ) exit;

function erpc_get_user_roles($id){
	if(!is_user_logged_in()) return;
	$user_meta=get_userdata($id);
	return $user_meta->roles;
}

add_action( 'erp_hr_leave_new', 'create_leave_entry_action', 10,1 );

// remove this from erm/modules/hrm/includes/classajax.php
//		add_action( 'wp_ajax_erp-hr-leave-req-new', [$this, 'leave_request'] );
add_action( 'erp_new_leave_request_notification_recipients', 'send_leave_request_email_to_leave_agent', 10,1 );


function create_leave_entry_action($request_id){
	$request = \WeDevs\ERP\HRM\Models\Leave_request::find( $request_id );
	$request->status = 11;
	$request->save();


	global $wpdb;
	$table = $wpdb->prefix . 'erpc_actions';
	$wpdb->insert( $table, array(
		'type' => 'leave_request',
		'type_id' => $request_id,
		'action' => 'create',
	),
		array(
			'%s',
			'%d',
			'%s'
		)
	);
}

function send_leave_request_email_to_leave_agent($recipients){
	unset($recipients);
	$users = get_users( ['role' => erpca_get_leave_agent_role()] );
	$recipients = wp_list_pluck($users, 'user_email');
	return $recipients;
}


function erpc_hr_leave_request_get_statuses($status){
	if($status == 11){
		return "Pending";
	}elseif ($status ==12){
		return "pending for HRM";
	}elseif ($status==13){
		return "pending for GM";
	}else{
		erp_hr_leave_request_get_statuses($status);
	}
}