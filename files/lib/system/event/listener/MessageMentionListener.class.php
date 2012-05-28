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
		if (!MODULE_MENTION && !MODULE_HASHTAGS)
			return;
		parent::execute($eventObj, $className, $eventName);
	}
	
	/* ----- onEvent  ----- */

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
			$this->enableMentions && MODULE_MENTION,
			$this->enableHashtags && MODULE_HASHTAGS);
	}
	
	/**
	* @see wcf\form\MessageForm::saved()
	*/
	public function onSaved($eventObj, $className) {
		
		if(MENTION_TRACKING || MENTION_NOTIFICATION)
			$this->saveMentions($eventObj);			
		
		if(HASHTAG_TAGGING)
			$this->doTagging($message);		
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
	
	/* ----- Helper functions ----- */
	
	protected function saveMentions($eventObj) {
		
		$controller = $this->detectController();
		$message = $this->getMessage($eventObj->objectAction);
		
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
				if(MENTION_NOTIFICATION)
					; // TODO fire notification
			}
		}		
	}
	
	protected function doTagging($message) {
		// object is taggable, so hashtags can be added as tags
		if ($message != null && $message instanceof wcf\system\tagging\ITagged) {
			$taggable = $message->getTaggable();
			// TODO: get objectType (string) from $taggable->getObjectTypeID()
			// => TagEngine::getObjectTags()
			// => merge with hashtags
			// => TagEngine::addObjectTags()
			// => open question: safe way to retrieve languageID ?
			$tags = MentionParser::getInstance()->getHashtags();
		}
	}
	
	/**
	 * Try to detect the message controller. This is based on best current practice of WCF 
	 * naming conventions, e.g. if the message was created in 'PostAdd', then the controller
	 * is likely to be 'Post'.
	 * 
	 * @return string controller of the message or null on failure.
	 */
	protected function detectController() {			
		$controller = RequestHandler::getInstance()->getActiveRequest()->getPageName();
		
		if (StringUtil::endsWith($controller, 'Add'))
			$controller = StringUtil::substring($controller, 0, StringUtil::length($haystack) - 3);
		else if (StringUtil::endsWith($controller, 'Edit'))
			$controller = StringUtil::substring($controller, StringUtil::length($haystack) - 4, 4);
		else return null;
		
		// TODO: validate controller
		// => check if in all dependent packages of this package a valid class can be found
		// if that fails, return null
		return $controller;
	}
	
	protected function getMessage($objectAction) {
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
			$message = null;
		}
		return $message;
	}
}