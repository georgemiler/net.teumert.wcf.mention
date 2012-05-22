<?php
namespace wcf\data\user\mention;

use wcf\data\DatabaseObject;
use wcf\system\WCF;

/**
 * Represents a user mention
 *
 * @author	
 * @copyright	
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	
 * @subpackage	data.user.mention
 * @category 	Community Framework
 */
class UserMention extends DatabaseObject {
	/**
	 * @see	wcf\data\DatabaseObject::$databaseTableName
	 */
	protected static $databaseTableName = 'user_mention';
	
	/**
	 * @see	wcf\data\DatabaseObject::$databaseTableIndexName
	 */
	protected static $databaseTableIndexName = 'mentionID';	
}
