<?php
namespace wcf\system\event\listener;
use wcf\data\acp\session\access\log\ACPSessionAccessLogEditor;
use wcf\data\acp\session\log\ACPSessionLog;
use wcf\data\acp\session\log\ACPSessionLogEditor;
use wcf\system\event\IEventListener;
use wcf\system\WCF;

/**
 * Executes the mention parser on messages.
 * 
 * @author	Sebastian Teumert
 * @copyright	© 2012 Sebastian Teumert
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	net.teumert.wcf.mention
 * @subpackage	system.event.listener
 * @category 	Community Framework (third party)
 */
class SessionAccessLogListener implements IEventListener {
	/**
	 * @see wcf\system\event\IEventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		if (!MODULE_MENTION) 
			return;
		
		// parse mentions		
		$eventObj->text = MentionParser::getInstance()->parse($eventObj->text);
		// TODO add options to message form & read them here		
	}
}
