<?php namespace Myth\Auth;
/**
 * Sprint
 *
 * A set of power tools to enhance the CodeIgniter framework and provide consistent workflow.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package     Sprint
 * @author      Lonnie Ezell
 * @copyright   Copyright 2014-2015, New Myth Media, LLC (http://newmythmedia.com)
 * @license     http://opensource.org/licenses/MIT  (MIT)
 * @link        http://sprintphp.com
 * @since       Version 1.0
 */

interface AuthorizeInterface  {

	/**
	 * Returns the latest error string.
	 *
	 * @return mixed
	 */
	public function error();

	//--------------------------------------------------------------------

	//--------------------------------------------------------------------
	// Actions
	//--------------------------------------------------------------------

	/**
	 * Checks to see if a user is in a group.
	 *
	 * Groups can be either a string, with the name of the group, an INT
	 * with the ID of the group, or an array of strings/ids that the
	 * user must belong to ONE of. (It's an OR check not an AND check)
	 *
	 * @param $groups
	 *
	 * @return bool
	 */
	public function inGroup($groups, $user_id);

	//--------------------------------------------------------------------

	/**
	 * Checks a user's groups to see if they have the specified permission.
	 *
	 * @param int|string $permission
	 * @param int|string $user_id
	 *
	 * @return mixed
	 */
	public function hasPermission($permission, $user_id);

	//--------------------------------------------------------------------

	/**
	 * Makes a member a part of a group.
	 *
	 * @param $user_id
	 * @param $group        // Either ID or name
	 *
	 * @return bool
	 */
	public function addUserToGroup($user_id, $group);

	//--------------------------------------------------------------------

	/**
	 * Removes a single user from a group.
	 *
	 * @param $user_id
	 * @param $group
	 *
	 * @return mixed
	 */
	public function removeUserFromGroup($user_id, $group);

	//--------------------------------------------------------------------

	/**
	 * Adds a single permission to a single group.
	 *
	 * @param int|string $permission
	 * @param int|string $group
	 *
	 * @return mixed
	 */
	public function addPermissionToGroup($permission, $group);

	//--------------------------------------------------------------------

	/**
	 * Removes a single permission from a group.
	 *
	 * @param int|string $permission
	 * @param int|string $group
	 *
	 * @return mixed
	 */
	public function removePermissionFromGroup($permission, $group);

	//--------------------------------------------------------------------

	//--------------------------------------------------------------------
	// Groups
	//--------------------------------------------------------------------

	/**
	 * Grabs the details about a single group.
	 *
	 * @param $group
	 *
	 * @return object|null
	 */
	public function group($group);

	//--------------------------------------------------------------------

	/**
	 * Grabs an array of all groups.
	 *
	 * @return array of objects
	 */
	public function groups();

	//--------------------------------------------------------------------

	/**
	 * @param $name
	 * @param string $description
	 *
	 * @return mixed
	 */
	public function createGroup($name, $description='');

	//--------------------------------------------------------------------

	/**
	 * Deletes a single group.
	 *
	 * @param int $group_id
	 *
	 * @return bool
	 */
	public function deleteGroup($group_id);

	//--------------------------------------------------------------------

	/**
	 * Updates a single group's information.
	 *
	 * @param $id
	 * @param $name
	 * @param string $description
	 *
	 * @return mixed
	 */
	public function updateGroup($id, $name, $description='');

	//--------------------------------------------------------------------

	//--------------------------------------------------------------------
	// Permissions
	//--------------------------------------------------------------------

	/**
	 * Returns the details about a single permission.
	 *
	 * @param int|string $permission
	 *
	 * @return object|null
	 */
	public function permission($permission);

	//--------------------------------------------------------------------

	/**
	 * Returns an array of all permissions in the system.
	 *
	 * @return mixed
	 */
	public function permissions();

	//--------------------------------------------------------------------

	/**
	 * Creates a single permission.
	 *
	 * @param $name
	 * @param string $description
	 *
	 * @return mixed
	 */
	public function createPermission($name, $description='');

	//--------------------------------------------------------------------

	/**
	 * Deletes a single permission and removes that permission from all groups.
	 *
	 * @param $permission
	 *
	 * @return mixed
	 */
	public function deletePermission($permission);

	//--------------------------------------------------------------------

	/**
	 * Updates the details for a single permission.
	 *
	 * @param int    $id
	 * @param string $name
	 * @param string $description
	 *
	 * @return bool
	 */
	public function updatePermission($id, $name, $description='');

	//--------------------------------------------------------------------

}
