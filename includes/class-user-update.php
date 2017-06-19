<?php
class ERPCA_User_Update{
	/**
	 * The HR users admin loader
	 *
	 * @package WP-ERP\HR
	 * @subpackage Administration
	 */
	public function __construct() {
		$this->setup_actions();
	}

	/**
	 * Setup the admin hooks, actions and filters
	 *
	 * @return void
	 */
	function setup_actions() {
		// Bail if in network admin
		if ( is_network_admin() ) {
			return;
		}

		// User profile edit/display actions
		add_action( 'erp_user_profile_role', array( $this, 'role' ) );
		add_action( 'erp_update_user', array( $this, 'update_user' ), 10, 2 );
	}

	function update_user( $user_id, $post ) {

		// Bail if current user cannot promote the passing user
		if ( ! current_user_can( 'promote_user', $user_id ) ) {
			return;
		}

		$user = get_user_by( 'id', $user_id );

		if(isset($post['leave_agent'])){
			$user->add_role( erpca_get_leave_agent_role() );
		} else {
			$user->remove_role(erpca_get_leave_agent_role() );
		}

		if(isset($post['assets_agent'])){
			$user->add_role( erpca_get_assets_agent_role() );
		} else {
			$user->remove_role(erpca_get_assets_agent_role() );
		}

		if(isset($post['payroll_agent'])){
			$user->add_role( erpca_get_payroll_agent_role() );
		} else {
			$user->remove_role(erpca_get_payroll_agent_role() );
		}

		if(isset($post['attendance_agent'])){
			$user->add_role( erpca_get_attendance_agent_role() );
		} else {
			$user->remove_role(erpca_get_attendance_agent_role() );
		}

		if(isset($post['recruitment_agent'])){
			$user->add_role( erpca_get_recruitment_agent_role() );
		} else {
			$user->remove_role(erpca_get_recruitment_agent_role() );
		}

		if(isset($post['accounting_agent'])){
			$user->add_role( erpca_get_accounting_agent_role() );
		} else {
			$user->remove_role(erpca_get_accounting_agent_role() );
		}
		if(isset($post['general_manager'])){
			$user->add_role( erpca_get_general_manager_role() );
		} else {
			$user->remove_role(erpca_get_general_manager_role() );
		}
	}

	function role( $profileuser ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$is_leave_agent = in_array( erpca_get_leave_agent_role(), $profileuser->roles ) ? 'checked' : '';
		$is_assets_agent = in_array( erpca_get_assets_agent_role(), $profileuser->roles ) ? 'checked' : '';
		$is_payroll_agent = in_array( erpca_get_payroll_agent_role(), $profileuser->roles ) ? 'checked' : '';
		$is_attendance_agent = in_array( erpca_get_attendance_agent_role(), $profileuser->roles ) ? 'checked' : '';
		$is_recruitment_agent = in_array( erpca_get_recruitment_agent_role(), $profileuser->roles ) ? 'checked' : '';
		$is_accounting_agent = in_array( erpca_get_accounting_agent_role(), $profileuser->roles ) ? 'checked' : '';
		$is_general_manager = in_array( erpca_get_general_manager_role(), $profileuser->roles ) ? 'checked' : '';
		?>
		<label for="erp-leave-agent">
			<input type="checkbox" id="erp-leave-agent" <?php echo $is_leave_agent; ?> name="leave_agent" value="<?php echo erpca_get_leave_agent_role(); ?>">
			<span class="description"><?php _e( 'Leave Agent', 'erp' ); ?></span>
		</label>

		<label for="erp-assets-agent">
			<input type="checkbox" id="erp-assets-agent" <?php echo $is_assets_agent; ?> name="assets_agent" value="<?php echo erpca_get_assets_agent_role(); ?>">
			<span class="description"><?php _e( 'Assets Agent', 'erp' ); ?></span>
		</label>
		<label for="erp-payroll-agent">
			<input type="checkbox" id="erp-payroll-agent" <?php echo $is_payroll_agent; ?> name="payroll_agent" value="<?php echo erpca_get_payroll_agent_role(); ?>">
			<span class="description"><?php _e( 'Payroll Agent', 'erp' ); ?></span>
		</label>
		<label for="erp-attendance-agent">
			<input type="checkbox" id="erp-attendance-agent" <?php echo $is_attendance_agent; ?> name="attendance_agent" value="<?php echo erpca_get_attendance_agent_role(); ?>">
			<span class="description"><?php _e( 'Attendance Agent', 'erp' ); ?></span>
		</label>
		<label for="erp-recruitment-agent">
			<input type="checkbox" id="erp-recruitment-agent" <?php echo $is_recruitment_agent; ?> name="recruitment_agent" value="<?php echo erpca_get_recruitment_agent_role(); ?>">
			<span class="description"><?php _e( 'Recruitment Agent', 'erp' ); ?></span>
		</label>
		<label for="erp-accounting-agent">
			<input type="checkbox" id="erp-accounting-agent" <?php echo $is_accounting_agent; ?> name="accounting_agent" value="<?php echo erpca_get_accounting_agent_role(); ?>">
			<span class="description"><?php _e( 'Accounting Agent', 'erp' ); ?></span>
		</label>
		<label for="erp-general_manager">
			<input type="checkbox" id="erp-general_manager" <?php echo $is_general_manager; ?> name="general_manager" value="<?php echo erpca_get_general_manager_role(); ?>">
			<span class="description"><?php _e( 'General Manager', 'erp' ); ?></span>
		</label>

		<?php
	}
}

new ERPCA_User_Update();