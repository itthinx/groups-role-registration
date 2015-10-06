<?php
/**
 * groups-role-registration.php
 *
 * Copyright (c) 2015 www.itthinx.com
 *
 * This code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This header and all notices must be kept intact.
 *
 * @author itthinx
 * @package groups-role-registration
 * @since 1.0.0
 *
 * Plugin Name: Groups Role Registration
 * Plugin URI: http://www.itthinx.com/plugins/groups
 * Description: A simple implementation that adds a user to a group based on role.
 * Version: 1.0.0
 * Author: itthinx
 * Author URI: http://www.itthinx.com
 * Donate-Link: http://www.itthinx.com
 * License: GPLv3
 */

/**
 * Add to group by role.
 */
class Groups_Role_Registration {

	/**
	 * Adds our action on user_register.
	 */
	public static function init() {
		add_action( 'user_register', array( __CLASS__, 'user_register' ) );
	}

	/**
	 * Hooked on user_register, add to group by role.
	 */
	public static function user_register( $user_id ) {
		global $wp_roles;
		if ( !( class_exists( 'Groups_Group' ) && method_exists( 'Groups_Group', 'read_by_name' ) ) ) {
			return;
		} 
		if ( $user_id != null ) {
			$user = new WP_User( $user_id );
			if ( !empty( $user->roles ) && is_array( $user->roles ) ) {
				foreach ( $user->roles as $role ) {
					$role = $wp_roles->get_role( $role );
					if ( $role ) {
						$group = Groups_Group::read_by_name( $role->name );
						if ( !$group ) {
							$group_id = Groups_Group::create( array( 'name' => $role->name ) );
						} else {
							$group_id = $group->group_id;
						}
						if ( $group_id ) {
							if ( !Groups_User_Group::read( $user_id, $group_id ) ) {
								Groups_User_Group::create( array(
									'user_id' => $user_id,
									'group_id' => $group_id
								) );
							}
						}
					}
				}
			}
		}
	}
}
Groups_Role_Registration::init();
