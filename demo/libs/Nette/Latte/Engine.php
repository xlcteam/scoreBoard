<?php

/**
 * This file is part of the Nette Framework (http://nette.org)
 *
 * Copyright (c) 2004 David Grudl (http://davidgrudl.com)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 * @package Nette\Latte
 */



/**
 * Templating engine Latte.
 *
 * @author     David Grudl
 * @package Nette\Latte
 */
class NLatteFilter extends NObject
{
	/** @var NParser */
	public $parser;



	public function __construct()
	{
		$this->parser = new NParser;
		NCoreMacros::install($this->parser);
		$this->parser->addMacro('cache', new NCacheMacro($this->parser));
		NUIMacros::install($this->parser);
		NFormMacros::install($this->parser);
	}



	/**
	 * Invokes filter.
	 * @param  string
	 * @return string
	 */
	public function __invoke($s)
	{
		$this->parser->context = array(NParser::CONTEXT_TEXT);
		$this->parser->setDelimiters('\\{(?![\\s\'"{}])', '\\}');
		return $this->parser->parse($s);
	}

}
