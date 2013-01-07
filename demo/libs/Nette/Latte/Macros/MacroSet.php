<?php

/**
 * This file is part of the Nette Framework (http://nette.org)
 *
 * Copyright (c) 2004 David Grudl (http://davidgrudl.com)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 * @package Nette\Latte\Macros
 */



/**
 * Base IMacro implementation. Allowes add multiple macros.
 *
 * @author     David Grudl
 * @package Nette\Latte\Macros
 */
class NMacroSet extends NObject implements IMacro
{
	/** @var NParser */
	public $parser;

	/** @var array */
	private $macros;



	public function __construct(NParser $parser)
	{
		$this->parser = $parser;
	}



	public function addMacro($name, $begin, $end = NULL)
	{
		$this->macros[$name] = array($begin, $end);
		$this->parser->addMacro($name, $this);
		return $this;
	}



	public static function install(NParser $parser)
	{
		return new self($parser);
	}



	/**
	 * Initializes before template parsing.
	 * @return void
	 */
	public function initialize()
	{
	}



	/**
	 * Finishes template parsing.
	 * @return array(prolog, epilog)
	 */
	public function finalize()
	{
	}



	/**
	 * New node is found.
	 * @return bool|string
	 */
	public function nodeOpened(NMacroNode $node)
	{
		$node->isEmpty = !isset($this->macros[$node->name][1]);
		return $this->compile($node, $this->macros[$node->name][0]);
	}



	/**
	 * Node is closed.
	 * @return string
	 */
	public function nodeClosed(NMacroNode $node)
	{
		return $this->compile($node, $this->macros[$node->name][1]);
	}



	/**
	 * Generates code.
	 * @return string
	 */
	private function compile(NMacroNode $node, $def)
	{
		$node->tokenizer->reset();
		$writer = NPhpWriter::using($node, $this->parser->context);
		if (is_string($def)&& substr($def, 0, 1) !== "\0") {
			$code = $writer->write($def);
		} else {
			$code = callback($def)->invoke($node, $writer);
			if ($code === FALSE) {
				return FALSE;
			}
		}
		return "<?php $code ?>";
	}

}
