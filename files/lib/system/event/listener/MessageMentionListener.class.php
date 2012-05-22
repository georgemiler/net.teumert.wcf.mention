<?php
namespace wcf\system\event\listener;

use wcf\system\bbcode\MentionParser;
use wcf\system\event\AbstractEventListener;
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
class MessageMentionListener extends AbstractEventListener {
	
	protected $enableMentions = false;
	protected $enableHashtags = false;
	
	/**
	 * @see wcf\system\event\AbstractEventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		if (!MODULE_MENTION)
			return;
		parent::execute($eventObj, $className, $eventName);
	}

	/**
	 * @see wcf\form\MessageForm::readFormParameters()
	 */
	public function onReadFormParameters($eventObj, $className) {
		$this->enableMentions = $this->enableHashtags = 0;
		if (isset($_POST['enableMentions'])) $this->enableMentions = intval($_POST['enableMentions']);
		if (isset($_POST['enableHashtags'])) $this->enableHashtags = intval($_POST['enableHashtags']);
	}
	
	/**
	* @see wcf\form\MessageForm::save()
	*/
	public function onSave($eventObj, $className) {
		$eventObj->text = MentionParser::getInstance()->parse($eventObj->text, 
			$this->enableMentions && MENTION_ENABLE_MENTIONS,
			$this->enableHashtags && MENTION_ENABLE_HASHTAGS);
	}
	
	/**
	* @see wcf\form\MessageForm::saved()
	*/
	public function onSaved($eventObj, $className) {
		/* 	
		 * KISS Principle
		 *
		 * If someone just wants to link @mentions, and does neither want tracking 
		 * nor notifications, nothing needs to be done here
		 * (no need to store mentions in db when not tracking / notificating).
		 */ 
		if (!MODULE_MENTION_TRACKING)
			return;		
		
		$mentions = MentionParser::getInstance()->getMentions();
		
		$message = null;		
		if($eventObj->objectAction->getActionName() === 'create') {	
			$returnValues = $eventObj->objectAction->getReturnValues();
			$message = $returnValues['returnValues'];
		} else if ($eventObj->objectAction->getActionName() === 'update') {
			$returnValues = $eventObj->objectAction->getObjects();
			$message = $returnValues['returnValues'][0];
		} else if($eventObj->objectAction->getActionName() === 'delete') {
			
		}
		
		// object is route controller, so it can be linked and notification is possible
		if($message != null && $message instanceof wcf\system\request\IRouteController) {
			
		}	
	}
	
	/**
	* @see wcf\form\MessageForm::assignVariables()
	*/
	public function onAssignVariables($eventObj, $className) {
		WCF::getTPL()->assign(array(
			'enableMentions' => $this->enableMentions,
			'enableHashtags' => $this->enableHashtags
		));
	}
}