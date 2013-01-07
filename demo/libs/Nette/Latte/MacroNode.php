<?php

/**
 * This file is part of the Nette Framework.
 *
 * Copyright (c) 2004, 2010 David Grudl (http://davidgrudl.com)
 *
 * This source file is subject to the "Nette license", and/or
 * GPL license. For more information please see http://nette.org
 * @package Nette\Latte
 */



/**
 * Macro element node.
 *
 * @author     David Grudl
 * @package Nette\Latte
 */
class NMacroNode extends NObject
{
	/** @var IMacro */
	public $macro;

	/** @var string */
	public $name;

	/** @var bool */
	public $isEmpty = FALSE;

	/** @var string  raw arguments */
	public $args;

	/** @var string  raw modifier */
	public $modifiers;

	/** @var bool */
	public $closing = FALSE;

	/** @var NMacroTokenizer */
	public $tokenizer;

	/** @var int @internal */
	public $offset;

	/** @var NMacroNode */
	public $parentNode;

	/** @var string */
	public $content;

	/** @var stdClass  user data */
	public $data;



	public function __construct(IMacro $macro, $name, $args = NULL, $modifiers = NULL, NMacroNode $parentNode = NULL)
	{
		$this->macro = $macro;
		$this->name = (string) $name;
		$this->modifiers = (string) $modifiers;
		$this->parentNode = $parentNode;
		$this->tokenizer = new NMacroTokenizer($this->args);
		$this->data = new stdClass;
		$this->setArgs($args);
	}



	public function setArgs($args)
	{
		$this->args = (string) $args;
		$this->tokenizer->tokenize($this->args);
	}



	public function close($content)
	{
		$this->closing = TRUE;
		$this->content = $content;
		return $this->macro->nodeClosed($this);
	}

}
