<?php
namespace wcf\data\user\mention;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of mentions.
 * 
 * @author 	
 * @copyright	2001-2011 WoltLab GmbH
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	
 * @subpackage	data.user.mention
 * @category 	Community Framework
 */
class UserMentionList extends DatabaseObjectList {
	/**
	 * @see	wcf\data\DatabaseObjectList::$className
	 */
	public $className = 'wcf\data\user\mention\UserMention';
}
