<?php
namespace wcf\system\bbcode;

use wcf\system\application\ApplicationHandler;
use wcf\system\bbcode\URLParser;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\event\EventHandler;
use wcf\system\request\LinkHandler;
use wcf\System\Regex;
use wcf\system\WCF;

/**
 * WCF @Mention & #Hashtag Parser
 *
 * Parses messages and generates bbcode [url] tags around @username mentions and #hashtags.
 * 
 * @author     Sebastian Teumert
 * @copyright  © 2012 Sebastian Teumert
 * @license    GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package    net.teumert.wcf.mentions
 * @subpackage system.bbcode
 * @category   Community Framework (third party)
 */
class MentionParser extends URLParser {	

	/** Twitter extractor - used to extract @mentions and #hashtags */
	protected $extractor;
	
	protected $enableMentions;
	protected $enableHashtags;
	
	protected $mentions = array();
	protected $hashtags = array();
	
	protected function init() {
		parent::init();
		// since the unmodified twitter-text-php library is used, wcf autoloading does not work
		if(!class_exists('\\Twitter_Extractor'))
			require_once WCF_DIR.'lib/Twitter/Extractor.php';
	}
	
	/**
	* Adds [url]-bbcodes to all elements (usernames, hastags, urls) in the text.
	*
	* @return  string
	*/	
	public function parse($text, $enableMentions = true, $enableHashtags = true) {
		// store values	
		$this->text = $text;		
		$this->enableMentions = $enableMentions;
		$this->enableHashtags = $enableHashtags;
		
		// reset mentions & hashtags
		$this->mentions = array();
		$this->hashtags = array();
		
		// create extractor
		$this->extractor = new \Twitter_Extractor($this->text);
		
		// cache codes
		$this->cacheCodes();
		
		// call event
		EventHandler::getInstance()->fireAction($this, 'beforeParsing');
		
		// do actual work
		if($this->enableMentions) $this->parseMentions();
		if($this->enableHashtags) $this->parseHashtags();
		
		// call event
		EventHandler::getInstance()->fireAction($this, 'afterParsing');
		
		if (count($this->cachedCodes) > 0) {
			// insert cached codes
			$this->insertCachedCodes();
		}
		
		return $this->text;
	}
	
	public function createBBCodeLink($url, $display = null) {
		$path = WCF::getPath(ApplicationHandler::getInstance()->getAbbreviation(PACKAGE_ID));
		// make sure path is absolute
		if(ApplicationHandler::getInstance()->getActiveGroup() === null) 
			$url = $path . $url;
		if($display === null)
			return "[url]" . $url . "[/url]";
		return "[url='" . $url ."']" . $display . "[/url]";
	} 
	
	/**
	 */
	protected function parseHashtags() {
		$this->hashtags = $this->extractor->extractHashtags();
		foreach($this->hashtags as $hashtag) {			
			$url = LinkHandler::getInstance()->getLink('Search', array('q' => $hashtag));
			$this->text = StringUtil::replace($hashtag, 
				$this->createBBCodeLink($url, $hashtag),
				$this->text);
		}
	}
	
	protected function parseMentions() {
		// this is *deliberately* local
		$mentions = $this->extractor->extractMentionedUsernames();
		
		if(count($mentions) > 0) {
			
			$sql = "user.username LIKE CONCAT(?, '%')";
			
			$likeCondition = str_repeat($sql . " OR", count($mentions) -1);
			$likeCondition .= " " . $sql;
			
			$conditions = new PreparedStatementConditionBuilder();
			$conditions->add("(" . $likeCondition . ")", $mentions);
			
			$sql = "SELECT user.username, user.userID 
				FROM wcf".WCF_N."_user user
				" . $conditions 
				. "ORDER by user.username DESC";
			
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute($conditions->getParameters());
			
			while ($row = $statement->fetchArray()) {
				// type safe check since strpos can be 0
				if(false !== strpos($this->text, $row['username'])) {
					// mention is confirmed, write to global array
					$this->mentions[$row['userID']] = $row['username'];
					
					$url = LinkHandler::getInstance()->getLink('User', 
						array(
							'id' => $row['userID'],
							'title' => $row['username']
						));					
					$hash = StringStack::pushToStringStack($this->createBBCodeLink($url, $row['username']), 'mentionParser');
					$this->text = StringUtil::replace($row['username'], $hash, $this->text);
					// string stack avoids replacement of usernames in already inserted URLs 					
				}
			}
			$this->text = StringStack::reinsertStrings($this->text, 'mentionParser');
		}
	}
	
	/**
	 * Returns the currently stored mentions
	 * 
	 * @return array<string> array containing mentions in the form userID => username
	 */
	public function getMentions() {
		return $this->mentions;
	}
	
	public function getHashtags() {
		return $this->hashtags;
	}
}
