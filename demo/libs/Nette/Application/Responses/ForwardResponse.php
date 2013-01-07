<?php

/**
 * This file is part of the Nette Framework (http://nette.org)
 *
 * Copyright (c) 2004 David Grudl (http://davidgrudl.com)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 * @package Nette\Application\Responses
 */



/**
 * Forwards to new request.
 *
 * @author     David Grudl
 *
 * @property-read NPresenterRequest $request
 * @package Nette\Application\Responses
 */
class NForwardResponse extends NObject implements IPresenterResponse
{
	/** @var NPresenterRequest */
	private $request;



	/**
	 * @param  NPresenterRequest  new request
	 */
	public function __construct(NPresenterRequest $request)
	{
		$this->request = $request;
	}



	/**
	 * @return NPresenterRequest
	 */
	final public function getRequest()
	{
		return $this->request;
	}



	/**
	 * Sends response to output.
	 * @return void
	 */
	public function send(IHttpRequest $httpRequest, IHttpResponse $httpResponse)
	{
	}

}
