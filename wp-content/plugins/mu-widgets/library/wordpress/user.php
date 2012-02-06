<?php
/*****************************************************************************************
* ??document??
*****************************************************************************************/
class wv46v_user extends bv46v_base {
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private $_user = null;
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function __construct(&$application, $user_id = null) {
		parent::__construct ( $application );
		if (null === $user_id) {
			global $current_user;
			if (null !== $current_user->data) {
				$user = $current_user->data;
			} else {
				$user = new stdClass ();
				$user->ID = - 1;
				$fields = array ('user_login', 'user_nicename', 'user_email', 'user_url', 'display_name', 'first_name', 'last_name', 'nickname' );
				foreach ( $fields as $field ) {
					$user->$field = '';
				}
			}
			$this->_user = $user;
		} else {
			$this->_user = get_userdata ( $user_id );
		}
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function __get($key) {
		if (isset ( $this->_user->$key )) {
			return $this->_user->$key;
		}
		return '';
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function get_role() {
		global $wpdb;
		$wp_roles = new WP_Roles ();
		$wp_roles = array_keys ( $wp_roles->roles );
		$cap_key = $wpdb->get_blog_prefix ( get_current_blog_id () ) . 'capabilities';
		$capabilities = array_keys ( $this->$cap_key );
		$role_a = array_intersect ( $wp_roles, $capabilities );
		foreach ( $role_a as $role ) {
			return $role;
		}
		return false;
	}
}
