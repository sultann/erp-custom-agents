<?php
namespace Pluginever\ERPCA;

trait ERPCA_User{
	public $user_id;

	public $user_roles;

	public $user_data;


	public function user_roles(){
		$user_data = get_userdata($this->user_id);
		return $user_data->roles;
	}

	public function is_user_leave_agent(){
		if(in_array(erpca_get_leave_agent_role(), erpca_get_user_roles($this->user_id))){
			return true;
		}
		return false;
	}

	public function is_user_hrm(){
		if((!in_array('administrator', erpca_get_user_roles($this->user_id))) && (in_array(erp_hr_get_manager_role(), erpca_get_user_roles($this->user_id)))){
			return true;
		}
		return false;
	}

	public function is_user_gm(){
		if(in_array(erpca_get_general_manager_role(), erpca_get_user_roles($this->user_id))){
			return true;
		}
		return false;
	}


}