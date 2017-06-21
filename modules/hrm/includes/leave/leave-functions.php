<?php
/**
 *  Get all the leave request depending on the user roles
 * @return array
 */
function erpc_hr_leave_get_requests_count() {
	global $wpdb;

	$statuses = erpa_hr_leave_request_get_statuses();
	$counts   = array();

	foreach ($statuses as $status => $label) {
		$counts[ $status ] = array( 'count' => 0, 'label' => $label );
	}

	$cache_key = 'erpc-hr-leave-request-counts';
	$results = wp_cache_get( $cache_key, 'erp' );
	$results = false;
	$status_codes = erpa_get_leave_request_codes_by_role();
	$array = implode(",",$status_codes);



	if ( false === $results ) {
		$sql     = "SELECT status, COUNT(id) as num FROM {$wpdb->prefix}erp_hr_leave_requests WHERE status IN ($array) GROUP BY status;";
		$results = $wpdb->get_results( $sql );

		wp_cache_set( $cache_key, $results, 'erp' );
	}


	foreach ($results as $row) {
		if ( array_key_exists( $row->status, $counts ) ) {
			$counts[ $row->status ]['count'] = (int) $row->num;
		}

		$counts['all']['count'] += (int) $row->num;
	}
	return $counts;
}

/**
 * Get leave requests status
 *
 * @since 0.1
 *
 * @param  int|boolean  $status
 *
 * @return array|string
 */
function erpa_hr_leave_request_get_statuses( $status = false ) {
	$roles = erpc_get_user_roles(get_current_user_id());
	var_dump($roles);
	$statuses = array(
		'all' => __( 'All', 'erp' ),
		'1' => __( 'Approved', 'erp' ),
		'3' => __( 'Rejected', 'erp' )
	);

	if(in_array(erpca_get_leave_agent_role(), $roles)){
		$statuses['11']   = __( 'Pending', 'erp' );
	}elseif(in_array('erp_hr_manager', $roles)){
		$statuses['12']   = __( 'Pending', 'erp' );
	}elseif(in_array(erpca_get_general_manager_role(), $roles)){
		$statuses['13']   = __( 'Pending', 'erp' );
	}

	if ( false !== $status && array_key_exists( $status, $statuses ) ) {
		return $statuses[ $status ];
	}

	return $statuses;
}

/**
 * Get leave request page links depending on the roles
 * @return array
 */
function erpa_get_leave_request_codes_by_role($single = false){
	$roles = erpc_get_user_roles(get_current_user_id());
	$status_codes = [];
	$status_codes[] = '1';
	if(in_array(erpca_get_leave_agent_role(), $roles)){
		$status_codes[] = '11';
		$status_codes[] = '3';
		$status_codes[] = '2';
	}elseif(in_array(erp_hr_get_manager_role(), $roles)){
		$status_codes[] = '12';
		$status_codes[] = '3';
	}elseif(in_array(erpca_get_general_manager_role(), $roles)){
		$status_codes[] = '13';
		$status_codes[] = '3';
	}

	if($single){
		return array_pop($status_codes);
	}
	return $status_codes;
}


/**
 * Fetch the leave requests
 *
 * @since 0.1
 *
 * @param  array   $args
 *
 * @return array
 */
function erpc_hr_get_leave_requests( $args = array() ) {
	global $wpdb;

	$defaults = array(
		'user_id'   => 0,
		'policy_id' => 0,
		'status'    => 1,
		'year'      => date( 'Y' ),
		'number'    => 20,
		'offset'    => 0,
		'orderby'   => 'created_on',
		'order'     => 'DESC',
	);

	$args  = wp_parse_args( $args, $defaults );
	$where = '';

	if ( 'all' != $args['status'] && $args['status'] != '' ) {

		if ( empty( $where ) ) {
			$where .= " WHERE";
		} else {
			$where .= " AND";
		}

		if ( is_array( $args['status'] ) ) {
			$where .= " `status` IN(" . implode( ",", array_map( 'intval', $args['status'] ) ) . ") ";
		} else {
			$where .= " `status` = " . intval( $args['status'] ) . " ";
		}
	}

	if ( $args['user_id'] != '0' ) {

		if ( empty( $where ) ) {
			$where .= " WHERE req.user_id = " . intval( $args['user_id'] );
		} else {
			$where .= " AND req.user_id = " . intval( $args['user_id'] );
		}
	}

	if ( $args['policy_id'] ) {

		if ( empty( $where ) ) {
			$where .= " WHERE req.policy_id = " . intval( $args['policy_id'] );
		} else {
			$where .= " AND req.policy_id = " . intval( $args['policy_id'] );
		}
	}

	if ( ! empty( $args['year'] ) ) {
		$from_date = $args['year'] . '-01-01';
		$to_date   = $args['year'] . '-12-31';

		if ( empty( $where ) ) {
			$where .= " WHERE req.start_date >= date('$from_date') AND req.start_date <= date('$to_date')";
		} else {
			$where .= " AND req.start_date >= date('$from_date') AND req.start_date <= date('$to_date')";
		}
	}

	$cache_key = 'erp_hr_leave_requests_' . md5( serialize( $args ) );
	//$requests  = wp_cache_get( $cache_key, 'erp' );
	$limit     = $args['number'] == '-1' ? '' : 'LIMIT %d, %d';

	$sql = "SELECT req.id, req.user_id, u.display_name, req.policy_id, pol.name as policy_name, req.status, req.reason, req.comments, req.created_on, req.days, req.start_date, req.end_date
        FROM {$wpdb->prefix}erp_hr_leave_requests AS req
        LEFT JOIN {$wpdb->prefix}erp_hr_leave_policies AS pol ON pol.id = req.policy_id
        LEFT JOIN $wpdb->users AS u ON req.user_id = u.ID
        $where
        ORDER BY {$args['orderby']} {$args['order']}
        $limit";

	var_dump($sql);

	//if ( $requests === false ) {
	if ( $args['number'] == '-1' ) {
		$requests = $wpdb->get_results( $sql );
	} else {
		$requests = $wpdb->get_results( $wpdb->prepare( $sql, absint( $args['offset'] ), absint( $args['number'] ) ) );
	}
//		wp_cache_set( $cache_key, $requests, 'erp', HOUR_IN_SECONDS );
//	}

	return $requests;
}

function erpc_hr_leave_request_get_pending_code($id) {
	$roles = erpc_get_user_roles( $id );
	$code  = 2;
	if ( in_array( erpca_get_leave_agent_role(), $roles ) ) {
		$code = 11;
	} elseif ( in_array( erp_hr_get_manager_role(), $roles ) ) {
		$code = 12;
	} elseif ( in_array( erpca_get_general_manager_role(), $roles ) ) {
		$code = 13;
	}

}

function erpca_update_leave_request_status($id, $status_code){
	$request = \WeDevs\ERP\HRM\Models\Leave_request::find( $id );
	error_log('Got request for updating '. $request->id );
	$request->status = $status_code;
	$request->save();
}

