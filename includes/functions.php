<?php
/*============================================*/
//THE FILES IS FOR ALL COMMON FUNCTIONS
/*============================================*/

// don't call the file directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Get user all roles
 * @param $id
 *
 * @return array|void
 */
function erpc_get_user_roles($id=null){
	if($id == null){
		$id = get_current_user_id();
	}
	if(!is_user_logged_in()) return;
	$user_meta=get_userdata($id);
	return $user_meta->roles;
}



/**
 * Check the user is leave agent
 * @param $id
 *
 * @return bool
 */
function erpca_user_is_leave_agent($id){
	if(in_array(erpca_get_leave_agent_role(), erpca_get_user_roles($id))){
		return true;
	}
	return false;
}

/**
 * check the user is HRM
 * @param $id
 *
 * @return bool
 */
function erpca_user_is_hrm($id){
	if((!in_array('administrator', erpca_get_user_roles($id))) && (in_array(erp_hr_get_manager_role(), erpca_get_user_roles($id)))){
		return true;
	}
	return false;
}

/**
 * check the user is GM
 * @param $id
 *
 * @return bool
 */
function erpca_user_is_gm($id){
	if(in_array(erpca_get_general_manager_role(), erpca_get_user_roles($id))){
		return true;
	}
	return false;
}