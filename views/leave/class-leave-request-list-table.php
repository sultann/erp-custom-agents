<?php

/**
 * List table class
 */
class ERPC_Leave_Requests_List_Table extends \WP_List_Table {

    private $counts = array();
    private $page_status;
    private $user_roles;

    function __construct() {
        global $status, $page;

        parent::__construct( array(
            'singular' => 'leave',
            'plural'   => 'leaves',
            'ajax'     => false
        ) );

        $this->table_css();

        $this->user_roles = erpca_get_user_roles(get_current_user_id());
    }

    /**
     * Message to show if no requests found
     *
     * @return void
     */
    function no_items() {
        _e( 'No requests found.', 'erp' );
    }

    /**
     * Default column values if no callback found
     *
     * @param  object  $item
     * @param  string  $column_name
     *
     * @return string
     */
    function column_default( $item, $column_name ) {

        $balance   = erp_hr_leave_get_balance( $item->user_id );
        $policy = erp_hr_leave_get_policy( $item->policy_id );

        if ( isset( $balance[ $item->policy_id ] ) ) {
            $scheduled = $balance[ $item->policy_id ]['scheduled'];
            $available = $balance[ $item->policy_id ]['entitlement'] - $balance[ $item->policy_id ]['total'];
        } else {
            $scheduled = 0;
            $available = 0;
        }

        switch ( $column_name ) {

            case 'policy':
                return stripslashes( $item->policy_name );

            case 'from_date':
                return erp_format_date( $item->start_date );
            case 'to_date':
                return erp_format_date( $item->end_date );

            case 'status':
            	$status_class = $this->get_status_level_class($item->status);
                return '<span class="status-' . $status_class . '">' . $this->get_status_level($item->status) . '</span>';

            case 'available':
                if ( $available < 0 ) {
                    return sprintf( '<span class="red">%d %s</span>', number_format_i18n( $available ), __( 'days', 'erp' ) );
                } elseif ( $available > 0 ) {
                    return sprintf( '<span class="green">%d %s</span>', number_format_i18n( $available ), __( 'days', 'erp' ) );
                } else if(  $available === 0 ){
                    return sprintf( '<span class="gray">%d %s</span>', 0, __( 'days', 'erp' ) );
                } else {
                    return sprintf( '<span class="green">%d %s</span>', number_format_i18n( $policy->value ), __( 'days', 'erp' ) );
                }

            case 'reason':
                return stripslashes( $item->reason );

            case 'comment' :
                return stripslashes( $item->comments );
            default:
                return isset( $item->$column_name ) ? $item->$column_name : '';
        }
    }

    function get_status_level($status){
	    if($status == 1){
	    	return 'Approved';
	    }elseif (($status == 2) || ($status> 3)){
		    return 'Pending';
	    }else{
		    return 'Rejected';
	    }
    }

	function get_status_level_class($status){
		if($status == 1){
			return '1';
		}elseif (($status == 2) || ($status> 3)){
			return '2';
		}else{
			return '3';
		}
	}

    /**
     * Get sortable columns
     *
     * @return array
     */
    function get_sortable_columns() {
        $sortable_columns = array(
            'days' => array( 'days', false ),
        );

        return $sortable_columns;
    }

    /**
     * Get the column names
     *
     * @return array
     */
    function get_columns() {
        $columns = array(
            'name'      => __( 'Employee Name', 'erp' ),
            'policy'    => __( 'Leave Policy', 'erp' ),
            'from_date' => __( 'From Date', 'erp' ),
            'to_date'   => __( 'To Date', 'erp' ),
            'days'      => __( 'Days', 'erp' ),
            'available' => __( 'Available', 'erp' ),
            'status'    => __( 'Status', 'erp' ),
            'reason'    => __( 'Leave Reason', 'erp' ),

        );
        if ( isset( $_GET['status'] ) && $_GET['status'] == 3 ) {
            $columns['comment'] =  __( 'Reject Reason', 'erp' );
        }

        return $columns;
    }


    function get_default_page_status(){
	    $page_status = 11;
    	$user_id = get_current_user_id();
    	if(erpca_user_is_leave_agent($user_id)){
		    $page_status = 11;
	    }elseif (erpca_user_is_hrm($user_id)){
		    $page_status = 12;
	    }else{
		    $page_status = 13;
	    }
	    return   $page_status;
    }


	/**
	 * Set the views
	 *
	 * @return array
	 */
	public function get_views() {
		$status_links   = array();
		$base_link      = admin_url( 'admin.php?page=erp-leave-extended' );
		foreach ($this->counts as $key => $value) {
			$class = ( $key == $this->page_status ) ? 'current' : 'status-' . $key;
			$status_links[ $key ] = sprintf( '<a href="%s" class="%s">%s <span class="count">(%s)</span></a>', add_query_arg( array( 'status' => $key ), $base_link ), $class, $value['label'], $value['count'] );
		}

		return $status_links;
	}


	/**
	 * Render the employee name column
	 *
	 * @param  object  $item
	 *
	 * @return string
	 */
	function column_name( $item ) {
		$tpl         = '?page=erp-leave-extended&leave_action=%s&id=%d';
		$nonce       = 'erp-hr-leave-req-nonce';
		$actions     = array();

		$delete_url  = wp_nonce_url( sprintf( $tpl, 'delete', $item->id ), $nonce );
		$reject_url  = wp_nonce_url( sprintf( $tpl, 'reject', $item->id ), $nonce );
		$approve_url = wp_nonce_url( sprintf( $tpl, 'approve', $item->id ), $nonce );
		$pending_url = wp_nonce_url( sprintf( $tpl, 'pending', $item->id ), $nonce );
		if ( erp_get_option( 'erp_debug_mode', 'erp_settings_general', 0 ) ) {
			$actions['delete'] = sprintf( '<a href="%s">%s</a>', $delete_url, __( 'Delete', 'erp' ) );
		}



		$user_id = get_current_user_id();
		if(erpca_user_is_leave_agent($user_id)){
			if(in_array($item->status, [11,2,3])) {
				$actions['reject']   = sprintf( '<a class="erp-hr-leave-reject-btn" data-id="%s" href="%s">%s</a>', $item->id, $reject_url, __( 'Reject', 'erp' ) );
				$actions['approved'] = sprintf( '<a href="%s">%s</a>', $approve_url, __( 'Approve', 'erp' ) );
			}else{
				unset($actions);
			}

		}elseif (erpca_user_is_hrm($user_id)){
			if($item->status == 12){
				$actions['reject']   = sprintf( '<a class="erp-hr-leave-reject-btn" data-id="%s" href="%s">%s</a>', $item->id, $reject_url, __( 'Reject', 'erp' ) );
				$actions['approved'] = sprintf( '<a href="%s">%s</a>', $approve_url, __( 'Approve', 'erp' ) );
			}else{
				unset($actions);
			}
		}elseif (erpca_user_is_gm($user_id)){
			if($item->status == 13){
				$actions['reject']   = sprintf( '<a class="erp-hr-leave-reject-btn" data-id="%s" href="%s">%s</a>', $item->id, $reject_url, __( 'Reject', 'erp' ) );
				$actions['approved'] = sprintf( '<a href="%s">%s</a>', $approve_url, __( 'Approve', 'erp' ) );
			}else{
				unset($actions);
			}
		}else{
			unset($actions);
		}



//		if(in_array(erpca_get_leave_agent_role(), $this->user_roles)){
//			if ( $item->status == '11') {
//				$actions['reject']   = sprintf( '<a class="erp-hr-leave-reject-btn" data-id="%s" href="%s">%s</a>', $item->id, $reject_url, __( 'Reject', 'erp' ) );
//				$actions['approved'] = sprintf( '<a href="%s">%s</a>', $approve_url, __( 'Approve', 'erp' ) );
//
//			}elseif($item->status == '3'){
//				$actions['pending'] = sprintf( '<a href="%s">%s</a>', $pending_url, __( 'Mark Pending', 'erp' ) );
//			}
//		}elseif(in_array(erp_hr_get_manager_role(), $this->user_roles)){
//			if ( $item->status == '12' ) {
//
//				$actions['reject']   = sprintf( '<a class="erp-hr-leave-reject-btn" data-id="%s" href="%s">%s</a>', $item->id, $reject_url, __( 'Reject', 'erp' ) );
//				$actions['approved'] = sprintf( '<a href="%s">%s</a>', $approve_url, __( 'Approve', 'erp' ) );
//
//			}
//		}else{
//			if ( $item->status == '13' ) {
//
//				$actions['reject']   = sprintf( '<a class="erp-hr-leave-reject-btn" data-id="%s" href="%s">%s</a>', $item->id, $reject_url, __( 'Reject', 'erp' ) );
//				$actions['approved'] = sprintf( '<a href="%s">%s</a>', $approve_url, __( 'Approve', 'erp' ) );
//
//			}
//		}

		return sprintf( '<a href="%3$s"><strong>%1$s</strong></a> %2$s', $item->display_name, $this->row_actions( $actions ), erp_hr_url_single_employee( $item->user_id ) );
	}


	/**
	 * Prepare the class items
	 *
	 * @return void
	 */
	function prepare_items() {

		$columns               = $this->get_columns();
		$hidden                = array( );
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		$per_page              = 20;
		$current_page          = $this->get_pagenum();
		$offset                = ( $current_page -1 ) * $per_page;
		$this->page_status     = isset( $_GET['status'] ) ? sanitize_text_field( $_GET['status'] ) : $this->get_default_page_status();

		// only necessary because we have sample data
		$args = array(
			'offset'  => $offset,
			'number'  => -1,
			'status'  => erpa_get_leave_request_codes_by_role(),
			'year'    => '',
			'orderby' => isset( $_GET['orderby'] ) ? $_GET['orderby'] : 'created_on',
			'order'   => isset( $_GET['order'] ) ? $_GET['order'] : 'DESC',
		);

//		var_dump($args);

		$this->counts = erpc_hr_leave_get_requests_count();
		$this->items  = erpc_hr_get_leave_requests( $args );
//		var_dump($this->counts);
        $this->set_pagination_args( array(
            'total_items' => $this->counts[ $this->page_status ]['count'],
            'per_page'    => $per_page
        ) );
	}




}
