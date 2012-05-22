<?php
namespace wcf\data\user\mention;

use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\exception\ValidateActionException;
use wcf\system\user\notification\object\UserMentionNotificationObject;
use wcf\system\user\notification\UserNotificationHandler;
use wcf\system\WCF;

/**
 * Executes mention-related actions.
 * 
 * @author	
 * @copyright	
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	
 * @subpackage	data.user.mention
 * @category 	Community Framework
 */
class UserMentionAction extends AbstractDatabaseObjectAction {
	/**
	 * @see	wcf\data\AbstractDatabaseObjectAction::$className
	 */
	public $className = 'wcf\data\user\mention\UserMentionEditor';
	
	
	
	
	
	
}
