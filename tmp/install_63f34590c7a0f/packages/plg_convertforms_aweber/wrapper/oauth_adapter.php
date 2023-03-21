<?php

/**
 * @package         Convert Forms
 * @version         3.2.12 Pro
 *
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2020 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

interface AWeberOAuthAdapter
{
	public function request($method, $uri, $data = array());
	public function getRequestToken($callbackUrl = false);
}

?>
