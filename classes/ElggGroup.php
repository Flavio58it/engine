<?php

/**
 * Class representing a container for other elgg entities.
 *
 * @package    Elgg.Core
 * @subpackage Groups
 * 
 * @property string $name        A short name that captures the purpose of the group
 * @property string $description A longer body of content that gives more details about the group
 */
class ElggGroup extends ElggEntity
	implements Friendable {

	/**
	 * Sets the type to group.
	 *
	 * @return void
	 */
	protected function initializeAttributes() {
		parent::initializeAttributes();

		$this->attributes['type'] = "group";
		$this->attributes['name'] = NULL;
		$this->attributes['description'] = NULL;
	}

	/**
	 * Construct a new group entity, optionally from a given guid value.
	 *
	 * @param mixed $guid If an int, load that GUID.
	 * 	If an entity table db row, then will load the rest of the data.
	 *
	 * @throws IOException|InvalidParameterException if there was a problem creating the group.
	 */
	function __construct($guid = null) {
		$this->initializeAttributes();

		// compatibility for 1.7 api.
		$this->initialise_attributes(false);

		if (!empty($guid)) {
			// Is $guid is a entity table DB row
			if ($guid instanceof stdClass) {
				// Load the rest
				if (!$this->load($guid)) {
					$msg = elgg_echo('IOException:FailedToLoadGUID', array(get_class(), $guid->guid));
					throw new IOException($msg);
				}

			// Is $guid is an ElggGroup? Use a copy constructor
			} else if ($guid instanceof ElggGroup) {
				elgg_deprecated_notice('This type of usage of the ElggGroup constructor was deprecated. Please use the clone method.', 1.7);

				foreach ($guid->attributes as $key => $value) {
					$this->attributes[$key] = $value;
				}

			} else if (is_numeric($guid)) {
				$guid = get_entity($guid,'group');
				if (!$this->load($guid)) {
					throw new IOException(elgg_echo('IOException:FailedToLoadGUID', array(get_class(), $guid)));
				}
			} 
		}
	}

	/**
	 * Add an ElggObject to this group.
	 *
	 * @param ElggObject $object The object.
	 *
	 * @return bool
	 */
	public function addObjectToGroup(ElggObject $object) {
		return add_object_to_group($this->getGUID(), $object->getGUID());
	}

	/**
	 * Remove an object from the containing group.
	 *
	 * @param int $guid The guid of the object.
	 *
	 * @return bool
	 */
	public function removeObjectFromGroup($guid) {
		return remove_object_from_group($this->getGUID(), $guid);
	}

	/**
	 * Returns an attribute or metadata.
	 *
	 * @see ElggEntity::get()
	 *
	 * @param string $name Name
	 *
	 * @return mixed
	 */
	public function get($name) {
		if ($name == 'username') {
			return 'group:' . $this->getGUID();
		}
		return parent::get($name);
	}

	/**
	 * Start friendable compatibility block:
	 *
	 * 	public function addFriend($friend_guid);
		public function removeFriend($friend_guid);
		public function isFriend();
		public function isFriendsWith($user_guid);
		public function isFriendOf($user_guid);
		public function getFriends($subtype = "", $limit = 10, $offset = 0);
		public function getFriendsOf($subtype = "", $limit = 10, $offset = 0);
		public function getObjects($subtype="", $limit = 10, $offset = 0);
		public function getFriendsObjects($subtype = "", $limit = 10, $offset = 0);
		public function countObjects($subtype = "");
	 */

	/**
	 * For compatibility with Friendable.
	 *
	 * Join a group when you friend ElggGroup.
	 *
	 * @param int $friend_guid The GUID of the user joining the group.
	 *
	 * @return bool
	 */
	public function addFriend($friend_guid) {
		return $this->join(get_entity($friend_guid));
	}

	/**
	 * For compatibility with Friendable
	 *
	 * Leave group when you unfriend ElggGroup.
	 *
	 * @param int $friend_guid The GUID of the user leaving.
	 *
	 * @return bool
	 */
	public function removeFriend($friend_guid) {
		return $this->leave(get_entity($friend_guid));
	}

	/**
	 * For compatibility with Friendable
	 *
	 * Friending a group adds you as a member
	 *
	 * @return bool
	 */
	public function isFriend() {
		return $this->isMember();
	}

	/**
	 * For compatibility with Friendable
	 *
	 * @param int $user_guid The GUID of a user to check.
	 *
	 * @return bool
	 */
	public function isFriendsWith($user_guid) {
		return $this->isMember($user_guid);
	}

	/**
	 * For compatibility with Friendable
	 *
	 * @param int $user_guid The GUID of a user to check.
	 *
	 * @return bool
	 */
	public function isFriendOf($user_guid) {
		return $this->isMember($user_guid);
	}

	/**
	 * For compatibility with Friendable
	 *
	 * @param string $subtype The GUID of a user to check.
	 * @param int    $limit   Limit
	 * @param int    $offset  Offset
	 *
	 * @return bool
	 */
	public function getFriends($subtype = "", $limit = 10, $offset = 0) {
		return get_group_members($this->getGUID(), $limit, $offset);
	}

	/**
	 * For compatibility with Friendable
	 *
	 * @param string $subtype The GUID of a user to check.
	 * @param int    $limit   Limit
	 * @param int    $offset  Offset
	 *
	 * @return bool
	 */
	public function getFriendsOf($subtype = "", $limit = 10, $offset = 0) {
		return get_group_members($this->getGUID(), $limit, $offset);
	}

	/**
	 * Get objects contained in this group.
	 *
	 * @param string $subtype Entity subtype
	 * @param int    $limit   Limit
	 * @param int    $offset  Offset
	 *
	 * @return array|false
	 */
	public function getObjects($subtype = "", $limit = 10, $offset = 0) {
		// @todo are we deprecating this method, too?
		return get_objects_in_group($this->getGUID(), $subtype, 0, 0, "", $limit, $offset, false);
	}

	/**
	 * For compatibility with Friendable
	 *
	 * @param string $subtype Entity subtype
	 * @param int    $limit   Limit
	 * @param int    $offset  Offset
	 *
	 * @return array|false
	 */
	public function getFriendsObjects($subtype = "", $limit = 10, $offset = 0) {
		// @todo are we deprecating this method, too?
		return get_objects_in_group($this->getGUID(), $subtype, 0, 0, "", $limit, $offset, false);
	}

	/**
	 * For compatibility with Friendable
	 *
	 * @param string $subtype Subtype of entities
	 *
	 * @return array|false
	 */
	public function countObjects($subtype = "") {
		// @todo are we deprecating this method, too?
		return get_objects_in_group($this->getGUID(), $subtype, 0, 0, "", 10, 0, true);
	}

	/**
	 * End friendable compatibility block
	 */

	/**
	 * Get a list of group members.
	 *
	 * @param int  $limit  Limit
	 * @param int  $offset Offset
	 * @param bool $count  Count
	 *
	 * @return mixed
	 */
	public function getMembers($limit = 10, $offset = 0, $count = false) {
		$options = array(   'relationship' => 'member',
							'relationship_guid' => $this->getGUID(),
							'inverse_relationship' => true,
							'type' => 'user',
							'limit' => $limit,
							'offset' => $offset,
							'count' => $count,
							'type' => 'user'
                        );
		return elgg_get_entities_from_relationship($options);
	}

	/**
	 * Returns whether the current group is public membership or not.
	 *
	 * @return bool
	 */
	public function isPublicMembership() {
		if ($this->membership == ACCESS_PUBLIC) {
			return true;
		}

		return false;
	}

	/**
	 * Return whether a given user is a member of this group or not.
	 *
	 * @param ElggUser $user The user
	 *
	 * @return bool
	 */
	public function isMember($user = null) {
		if ($user == null) {
			$user = elgg_get_logged_in_user_entity();
		}
		if (!$user) {
			return false;
		}
				
		return check_entity_relationship($user->guid, 'member', $this->getGUID());
	}

	 /**
      * Join a user to this group.
      *
      * @param ElggUser $user User joining the group.
      *
      * @return bool Whether joining was successful.
      */
	public function join(ElggUser $user) {
		$result = add_entity_relationship($user->guid, 'member', $this->guid);
        
		if ($result) {
			$params = array('group' => $this, 'user' => $user);
			elgg_trigger_event('join', 'group', $params);
		}
        
		return $result;
	}

	/**
	 * Remove a user from the group.
	 *
	 * @param ElggUser $user User to remove from the group.
	 *
	 * @return bool Whether the user was removed from the group.
	 */
	public function leave(ElggUser $user) {
		// event needs to be triggered while user is still member of group to have access to group acl
		$params = array('group' => $this, 'user' => $user);
		elgg_trigger_event('leave', 'group', $params);

		return remove_entity_relationship($user->guid, 'member', $this->guid);
	}
		
	/**
	 * Load the ElggGroup data from the database
	 *
	 * @param mixed $guid GUID of an ElggGroup entity or database row from entity table
	 *
	 * @return bool
	 */
	protected function load($guid) {
		
		foreach($guid as $k => $v){
                        $this->attributes[$k] = $v;
                }

                cache_entity($this);

		return true;
	}


	// EXPORTABLE INTERFACE ////////////////////////////////////////////////////////////

	/**
	 * Return an array of fields which can be exported.
	 *
	 * @return array
	 */
	public function getExportableValues() {
		return array_merge(parent::getExportableValues(), array(
			'name',
			'description',
			'icontime',
			'banner'
		));
	}

	/**
	 * Can a user comment on this group?
	 *
	 * @see ElggEntity::canComment()
	 *
	 * @param int $user_guid User guid (default is logged in user)
	 * @return bool
	 * @since 1.8.0
	 */
	public function canComment($user_guid = 0) {
		$result = parent::canComment($user_guid);
		if ($result !== null) {
			return $result;
		}
		return false;
	}
}
