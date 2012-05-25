<?php
namespace wcf\data\user\mention;

use wcf\data\DatabaseObject;
use wcf\system\WCF;

/**
 * Represents a user mention
 *
 * @author	Sebastian Teumert
 * @copyright	© 2012 Sebastian Teumert
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	net.teumert.wcf.mention
 * @subpackage	data.user.mention
 * @category 	Community Framework (third party)
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

	/**
	 * Returns all mentions in the message with the given id that is handled by the given controller.
	 * 
	 * @param	string		$controller	Message controller, e.g. 'Post'
	 * @param	integer		$messageID	ID of the message
	 * @return 	array<wcf\data\user\mention\UserMention> 	List of mentions in the message
	 */
	public static function getMentions($controller, $messageID) {
		$sql = "SELECT	mentionID
			FROM	wcf".WCF_N."_user_mention
			WHERE	messageID = ?
				AND controller = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($messageID, $controller));
		
		$mentions = array();
		while ($row = $statement->fetchArray()) {
			$mentions[] = new UserMention(null, $row);
		}
		
		return $mentions;
	}
}
