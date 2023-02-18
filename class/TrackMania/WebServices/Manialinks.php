<?php
/**
 * TrackMania Web Services SDK for PHP
 *
 * @copyright   Copyright (c) 2009-2011 NADEO (http://www.nadeo.com)
 * @license     http://www.gnu.org/licenses/lgpl.html LGPL License 3
 * @author      $Author: maximeraoust $:
 * @version     $Revision: 23 $:
 * @date        $Date: 2011-07-21 15:26:21 +0200 (jeu., 21 juil. 2011) $:
 */

namespace TrackMania\WebServices;

/**
 * Access to public Manialinks data
 */
class Manialinks extends HTTPClient
{

	/**
	 * Retrieves information about a Short Manialink code. Response structure is:
	 * <code>
	 * Object
	 * (
	 *    [code] => example_manialink
	 *    [url] => http://example.com/
	 *    [login] => player_login
	 *    [coppersCost] => 0
	 * )
	 * </code>
	 * 
	 * @param string $code Short Manialink code
	 * @return object
	 * @throws \TrackMania\WebServices\Exception 
	 */
	function get($code)
	{
		return $this->execute('GET', '/tmf/manialinks/%s/', array($code));
	}

}

?>