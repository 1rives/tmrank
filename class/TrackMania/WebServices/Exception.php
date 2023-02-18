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
 * Exception thrown by the services when something goes wrong
 */
class Exception extends \Exception
{

	protected $HTTPStatusCode;
	protected $HTTPStatusMessage;

	function __construct($message='', $code=0, $statusCode=0, $statusMessage='')
	{
		parent::__construct($message, $code);

		$this->HTTPStatusCode = $statusCode;
		$this->HTTPStatusMessage = $statusMessage;
	}

	/**
	 * The HTTP status code returned in case of an error, eg. 404
	 * @return int
	 */
	function getHTTPStatusCode()
	{
		return $this->HTTPStatusCode;
	}

	/**
	 * The HTTP status message returned in case of an error, eg. "Not Found"
	 * @return string
	 */
	function getHTTPStatusMessage()
	{
		return $this->HTTPStatusMessage;
	}

}
?>