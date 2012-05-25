<?php
namespace wcf\data\user\mention;

use wcf\data\DatabaseObjectEditor;

/**
 * Provides functions to edit mentions.
 *
 * @author	Sebastian Teumert
 * @copyright	© 2012 Sebastian Teumert
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	net.teumert.wcf.mention
 * @subpackage	data.user.mention
 * @category 	Community Framework (third party)
 */
class UserMentionEditor extends DatabaseObjectEditor {
	/**
	 * @see	wcf\data\DatabaseObjectDecorator::$baseClass
	 */
	protected static $baseClass = 'wcf\data\user\mention\UserMention';
}
