<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program (see LICENSE.txt in the base directory.  If
 * not, see:
 *
 * @link      <http://www.gnu.org/licenses/>.
 * @author    niel
 * @copyright 2017 nZEDb
 */
namespace app\models;


/**
 * Groups - class for groups table.
 *
 * @package app\models
 */
class Groups extends \lithium\data\Model
{
	public $hasMany = [
		'Releases' => [
			'to'  => 'Releases',
			'key' => 'groups_id',
			]
		];

	public $_meta = [
		'connection'	=> 'default',
		'key'			=> 'id',
		'source'		=> 'groups',
	];

	public $_schema = [];

	public $validates = [
		'name' => [
			[
				'notEmpty',
				'required' => true,
				'message' => 'You must supply a name for this group.'
			]
		]
	];

	/**
	 * Find single group item using its id field.
	 *
	 * @param $groupID
	 *
	 * @return mixed
	 */
	public static function findByID($groupID)
	{
		return Groups::find('first', ['conditions' => ['id' => $groupID]]);
	}

	/**
	 * Convenience method to return the 'id' of supplied group name.
	 *
	 * @param      $group			Name of group to find 'id' of.
	 * @param bool $returnAlways	Whether the method should return (null) regardless. Default
	 *								is to throw an exception.
	 *
	 * @return integer|null         The group's id number, or null if not found and return is
	 *								required.
	 * @throws \Exception
	 */
	public static function findID($group, $returnAlways = false)
	{
		$result = Groups::find('first', ['conditions' => ['name' => $group]]);

		if ($result !== false && $result->count() > 0) {
			$primary = $result->data()[0]['id'];
		} else {
			if ($returnAlways === false) {
				throw new \Exception("No group entry!");
			} else {
				$primary = null;
			}
		}

		return $primary;
	}

	public static function findRange($page = 1, $limit = ITEMS_PER_PAGE, $name = '', $active = -1)
	{
		$options = [
			'limit' => $limit,
			'order' => ['name' => 'ASC'],
			'page'  => (int)$page
		];

		$where = [];
		if ($active > -1) {
			$where += ['active' => $active];
		}

		if ($name != '') {
			$where += ['name' => ['LIKE' => "%$name%"]];
		}

		if (!empty($where)) {
			$options += ['conditions' => $where];
		}

		$result = Groups::find('all', $options);

		return $result;
	}

	/**
	 * Checks group name is standard and replaces any shorthand prefixes
	 *
	 * @param string $groupName The full name of the usenet group being evaluated
	 *
	 * @return string|bool The name of the group replacing shorthand prefix or false if groupname was malformed
	 */
	public function isValidGroupName($groupName)
	{
		if (preg_match('#^([\w-]+\.)+[\w-]+$#i', $groupName)) {

			return preg_replace('#^a\.b\.#i', 'alt.binaries.', $groupName, 1);
		}

		return false;
	}

	/**
	 * Override of lithium\data\Model::remove() to allow removal of a group to chain removing the
	 * associated Releases.
	 *
	 * @param array $conditions
	 * @param array $options
	 *
	 * @return boolean Returns `true` if the remove operation succeeded, otherwise `false`.
	 */
	public static function remove($conditions = [], array $options = [])
	{
		// TODO delete the releases covered by the groups to be removed. Prefereably NOT singly as
		// is currently done.

		parent::remove($conditions, $options);
	}
}
