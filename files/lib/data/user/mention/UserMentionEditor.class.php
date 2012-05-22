<?php
namespace wcf\data\user\mention;

use wcf\data\DatabaseObjectEditor;

/**
 * Provides functions to edit mentions.
 *
 * @author	
 * @copyright	
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	
 * @subpackage	data.user.mention
 * @category 	Community Framework
 */
class UserMentionEditor extends DatabaseObjectEditor {
	/**
	 * @see	wcf\data\DatabaseObjectDecorator::$baseClass
	 */
	protected static $baseClass = 'wcf\data\user\mention\UserMention';
}
