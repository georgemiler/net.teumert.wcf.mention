<?php
namespace wcf\system\event\listener;

use wcf\data\user\mention\UserMention;
use wcf\data\user\mention\UserMentionEditor;
use wcf\system\bbcode\MentionParser;
use wcf\system\event\AbstractEventListener;
use wcf\system\request\RequestHandler;
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
		
		// at first we need to get the controller of the message
		// this is guesswork and based on best current practice of WCF naming conventions
		$controller = RequestHandler::getInstance()->getActiveRequest()->getPageName();

		if (StringUtil::endsWith($controller, 'Add')) 
			$controller = StringUtil::substring($controller, 0, StringUtil::length($haystack) - 3);
		else if (StringUtil::endsWith($controller, 'Edit'))
			$controller = StringUtil::substring($controller, StringUtil::length($haystack) - 4, 4);
		else // likely no valid controller
			return;
		
		// now we need a route controller to build the route to this message
		$message = null;		
		if ($eventObj->objectAction->getActionName() === 'create') {
			$returnValues = $eventObj->objectAction->getReturnValues();
			$message = $returnValues['returnValues'];
		} else if ($eventObj->objectAction->getActionName() === 'update') {
			$returnValues = $eventObj->objectAction->getReturnValues();
			$message = $returnValues['returnValues'][0];
		} else if ($eventObj->objectAction->getActionName() === 'delete') {
			$messages = $eventObj->objectAction->getObjects();
			foreach ($messages as $message)
				if ($message instanceof wcf\system\request\IRouteController)
					UserMentionEditor::deleteAll(UserMention::getMentionIDs($controller, $message->getID()));
				// TODO revoke unread notifications			
		}
		
		// object is route controller, so it can be linked and notification is possible
		if ($message != null && $message instanceof wcf\system\request\IRouteController) {
			$mentionedUsers = MentionParser::getInstance()->getMentions();
						
			foreach ($mentionedUsers as $userID => $username) {
				$mention = UserMentionEditor::create(array(
					'userID' => WCF::getUser()->userID,
					'mentionedUserID' => $userID,
					'controller' => $controller,
					'messageID' => $message->getID(),
					'messageTitle' => $message->getTitle()
					));
				// TODO fire notification
			}
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