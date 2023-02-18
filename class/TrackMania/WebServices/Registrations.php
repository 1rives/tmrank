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
 * Access to registrations data
 */
class Registrations extends HTTPClient
{

	/**
	 * Number of TMF registered accounts
	 * @return int 
	 * @throws \TrackMania\WebServices\Exception 
	 */
	function getPlayersCount()
	{
		return $this->execute('GET', '/tmf/registrations/');
	}

}

?>