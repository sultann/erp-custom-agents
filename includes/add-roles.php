<?php
/**
 * The file is responsible for managing all the roles with erp
 */


/*===========================================*/
//COMMON
/*===========================================*/

/**
 * Add all custom roles
 *
 */
add_action('erp_hr_get_roles', 'erpca_add_agent_roles');
function erpca_add_agent_roles($roles){
	$custom_roles = [];

	$custom_roles = [
		erpca_get_leave_agent_role() => [
			'name'         => __( 'Leave Agent', 'erp' ),
			'public'       => false,
			'capabilities' => erpc_get_caps_for_role(erpca_get_leave_agent_role())
		],

		erpca_get_assets_agent_role() => [
			'name'         => __( 'Assets Agent', 'erp' ),
			'public'       => true,
			'capabilities' => erpc_get_caps_for_role(erpca_get_assets_agent_role())
		],
		erpca_get_payroll_agent_role() => [
			'name'         => __( 'Payroll Agent', 'erp' ),
			'public'       => true,
			'capabilities' =>  erpc_get_caps_for_role(erpca_get_payroll_agent_role())
		],
		erpca_get_attendance_agent_role() => [
			'name'         => __( 'Attendance Agent', 'erp' ),
			'public'       => true,
			'capabilities' =>  erpc_get_caps_for_role(erpca_get_attendance_agent_role())
		],
		erpca_get_recruitment_agent_role() => [
			'name'         => __( 'Recruitment Agent', 'erp' ),
			'public'       => true,
			'capabilities' =>  erpc_get_caps_for_role(erpca_get_recruitment_agent_role())
		],
		erpca_get_accounting_agent_role() => [
			'name'         => __( 'Accounting Agent', 'erp' ),
			'public'       => true,
			'capabilities' =>  erpc_get_caps_for_role(erpca_get_accounting_agent_role())
		],

		erpca_get_general_manager_role() => [
			'name'         => __( 'General Manager', 'erp' ),
			'public'       => true,
			'capabilities' =>  erpc_get_caps_for_role(erpca_get_general_manager_role())
		]
	];

	return array_merge($roles, $custom_roles);
}

/**
 * Get user roles
 * @param $user_id
 *
 * @return array
 */
function erpca_get_user_roles($user_id){
	if(!function_exists('is_user_logged_in')){
		require_once( ABSPATH . "wp-includes/pluggable.php" );
	}
	$user_meta=get_userdata($user_id);
	return $user_meta->roles;
}


/*===========================================*/
//HRM
/*===========================================*/
function erpca_get_leave_agent_role(){
	return apply_filters( 'erpca_get_leave_agent_role', 'erp_leave_agent' );
}

function erpca_get_assets_agent_role(){
	return apply_filters( 'erpca_get_assets_agent_role', 'erp_assets_agent' );
}

function erpca_get_payroll_agent_role(){
	return apply_filters( 'erpca_get_payroll_agent_role', 'erp_payroll_agent' );
}

function erpca_get_attendance_agent_role(){
	return apply_filters( 'erpca_get_attendance_agent_role', 'erp_attendance_agent' );
}

function erpca_get_recruitment_agent_role(){
	return apply_filters( 'erpca_get_recruitment_agent_role', 'erp_recruitment_agent' );
}

/*===========================================*/
//Accounting
/*===========================================*/
function erpca_get_accounting_agent_role(){
	return apply_filters( 'erpca_get_accounting_agent_role', 'erp_accounting_agent' );
}
/*===========================================*/
//GM
/*===========================================*/
function erpca_get_general_manager_role() {
	return apply_filters( 'erpca_get_general_manager_role', 'erp_general_manager' );
}

/*===========================================*/
//Capabilities
/*===========================================*/
/**
 * Returns an array of capabilities based on the role that is being requested.
 *
 * @param  string  $role
 *
 * @return array
 */
function erpc_get_caps_for_role( $role = '' ) {
	$caps = [];

	// Which role are we looking for?
	switch ( $role ) {

		case erpca_get_leave_agent_role():
			$caps = [
				'erp_leave_manage'         => true
			];
			break;
		case erpca_get_assets_agent_role():
			$caps = [
			];
			break;
		case erpca_get_payroll_agent_role():
			$caps = [
			];
			break;
		case erpca_get_attendance_agent_role():
			$caps = [
			];
			break;
		case erpca_get_recruitment_agent_role():
			$caps = [
			];
			break;
		case erpca_get_accounting_agent_role():
			$caps = [
			];
			break;
		case erpca_get_general_manager_role():
			$caps = [
			];
			break;
	}

	return apply_filters( 'erp_hr_get_caps_for_role', $caps, $role );
}