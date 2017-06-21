<?php

/**
 * Get user all roles
 * @param $id
 *
 * @return array
 */
function erpc_get_user_roles($id=null){
	if($id == null){
		$id = get_current_user_id();
	}
	if(!is_user_logged_in()) return;
	$user_meta=get_userdata($id);
	return $user_meta->roles;
}

function erpca_get_users_email_by_role($role){
	if(!$role) return ;
	$args = array(
		'role' => $role,
		'orderby' => 'user_nicename',
		'order' => 'ASC'
	);
	$users = get_users($args);
	$emails = wp_list_pluck($users, 'user_email');
	return $emails;
}