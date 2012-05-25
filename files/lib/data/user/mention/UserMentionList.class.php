<?php
namespace wcf\data\user\mention;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of mentions.
 * 
 * @author	Sebastian Teumert
 * @copyright	© 2012 Sebastian Teumert
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	net.teumert.wcf.mention
 * @subpackage	data.user.mention
 * @category 	Community Framework (third party)
 */
class UserMentionList extends DatabaseObjectList {
	/**
	 * @see	wcf\data\DatabaseObjectList::$className
	 */
	public $className = 'wcf\data\user\mention\UserMention';
}
