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
 * PHP code generator helpers.
 *
 * @author     David Grudl
 * @package Nette\Latte
 */
class NPhpWriter extends NObject
{
	/** @var NMacroTokenizer */
	private $argsTokenizer;

	/** @var string */
	private $modifiers;

	/** @var array */
	private $context;



	public static function using(NMacroNode $node, $context = NULL)
	{
		return new self($node->tokenizer, $node->modifiers, $context);
	}



	public function __construct(NMacroTokenizer $argsTokenizer, $modifiers = NULL, $context = NULL)
	{
		$this->argsTokenizer = $argsTokenizer;
		$this->modifiers = $modifiers;
		$this->context = $context;
	}



	/**
	 * Expands %node.word, %node.array, %node.args, %escape(), %modify(), %var, %raw in code.
	 * @param  string
	 * @return string
	 */
	public function write($mask)
	{
		$args = func_get_args();
		array_shift($args);
		$word = strpos($mask, '%node.word') === FALSE ? NULL : $this->argsTokenizer->fetchWord();
		$me = $this;
		$mask = NStrings::replace($mask, '#%escape(\(([^()]*+|(?1))+\))#', callback(create_function('$m', 'extract(NCFix::$vars['.NCFix::uses(array('me'=>$me)).'], EXTR_REFS);
			return $me->escape(substr($m[1], 1, -1));
		')));
		$mask = NStrings::replace($mask, '#%modify(\(([^()]*+|(?1))+\))#', callback(create_function('$m', 'extract(NCFix::$vars['.NCFix::uses(array('me'=>$me)).'], EXTR_REFS);
			return $me->formatModifiers(substr($m[1], 1, -1));
		')));

		return NStrings::replace($mask, '#([,+]\s*)?%(node\.word|node\.array|node\.args|var|raw)(\?)?(\s*\+\s*)?()#',
			callback(create_function('$m', 'extract(NCFix::$vars['.NCFix::uses(array('me'=>$me,'word'=> $word, 'args'=>& $args)).'], EXTR_REFS);
			list(, $l, $macro, $cond, $r) = $m;

			switch ($macro) {
			case \'node.word\':
				$code = $me->formatWord($word); break;
			case \'node.args\':
				$code = $me->formatArgs(); break;
			case \'node.array\':
				$code = $me->formatArray();
				$code = $cond && $code === \'array()\' ? \'\' : $code; break;
			case \'var\':
				$code = var_export(array_shift($args), TRUE); break;
			case \'raw\':
				$code = (string) array_shift($args); break;
			}

			if ($cond && $code === \'\') {
				return $r ? $l : $r;
			} else {
				return $l . $code . $r;
			}
		')));
	}



	/**
	 * Formats modifiers calling.
	 * @param  string
	 * @return string
	 */
	public function formatModifiers($var)
	{
		$modifiers = ltrim($this->modifiers, '|');
		if (!$modifiers) {
			return $var;
		}

		$tokenizer = $this->preprocess(new NMacroTokenizer($modifiers));
		$inside = FALSE;
		while ($token = $tokenizer->fetchToken()) {
			if ($token['type'] === NMacroTokenizer::T_WHITESPACE) {
				$var = rtrim($var) . ' ';

			} elseif (!$inside) {
				if ($token['type'] === NMacroTokenizer::T_SYMBOL) {
					if ($this->context && $token['value'] === 'escape') {
						$var = $this->escape($var);
						$tokenizer->fetch('|');
					} else {
						$var = "\$template->" . $token['value'] . "($var";
						$inside = TRUE;
					}
				} else {
					throw new NLatteException("Modifier name must be alphanumeric string, '$token[value]' given.");
				}
			} else {
				if ($token['value'] === ':' || $token['value'] === ',') {
					$var = $var . ', ';

				} elseif ($token['value'] === '|') {
					$var = $var . ')';
					$inside = FALSE;

				} else {
					$var .= $this->canQuote($tokenizer) ? "'$token[value]'" : $token['value'];
				}
			}
		}
		return $inside ? "$var)" : $var;
	}



	/**
	 * Formats macro arguments to PHP code.
	 * @return string
	 */
	public function formatArgs()
	{
		$out = '';
		$tokenizer = $this->preprocess();
		while ($token = $tokenizer->fetchToken()) {
			$out .= $this->canQuote($tokenizer) ? "'$token[value]'" : $token['value'];
		}
		return $out;
	}



	/**
	 * Formats macro arguments to PHP array.
	 * @return string
	 */
	public function formatArray()
	{
		$out = '';
		$expand = NULL;
		$tokenizer = $this->preprocess();
		while ($token = $tokenizer->fetchToken()) {
			if ($token['value'] === '(expand)' && $token['depth'] === 0) {
				$expand = TRUE;
				$out .= '),';

			} elseif ($expand && ($token['value'] === ',') && !$token['depth']) {
				$expand = FALSE;
				$out .= ', array(';
			} else {
				$out .= $this->canQuote($tokenizer) ? "'$token[value]'" : $token['value'];
			}
		}
		if ($expand === NULL) {
			return "array($out)";
		} else {
			return "array_merge(array($out" . ($expand ? ', array(' : '') ."))";
		}
	}



	/**
	 * Formats parameter to PHP string.
	 * @param  string
	 * @return string
	 */
	public function formatWord($s)
	{
		return (is_numeric($s) || strspn($s, '\'"$') || in_array(strtolower($s), array('true', 'false', 'null')))
			? $s : '"' . $s . '"';
	}



	/**
	 * @return bool
	 */
	public function canQuote($tokenizer)
	{
		return $tokenizer->isCurrent(NMacroTokenizer::T_SYMBOL)
			&& (!$tokenizer->hasPrev() || $tokenizer->isPrev(',', '(', '[', '=', '=>', ':', '?'))
			&& (!$tokenizer->hasNext() || $tokenizer->isNext(',', ')', ']', '=', '=>', ':', '|'));
	}



	/**
	 * Preprocessor for tokens.
	 * @return NMacroTokenizer
	 */
	public function preprocess(NMacroTokenizer $tokenizer = NULL)
	{
		$tokenizer = $tokenizer === NULL ? $this->argsTokenizer : $tokenizer;
		$inTernary = $prev = NULL;
		$tokens = $arrays = array();
		while ($token = $tokenizer->fetchToken()) {
			$token['depth'] = $depth = count($arrays);

			if ($token['type'] === NMacroTokenizer::T_COMMENT) {
				continue; // remove comments

			} elseif ($token['type'] === NMacroTokenizer::T_WHITESPACE) {
				$tokens[] = $token;
				continue;
			}

			if ($token['value'] === '?') { // short ternary operators without :
				$inTernary = $depth;

			} elseif ($token['value'] === ':') {
				$inTernary = NULL;

			} elseif ($inTernary === $depth && ($token['value'] === ',' || $token['value'] === ')' || $token['value'] === ']')) { // close ternary
				$tokens[] = NMacroTokenizer::createToken(':') + array('depth' => $depth);
				$tokens[] = NMacroTokenizer::createToken('null') + array('depth' => $depth);
				$inTernary = NULL;
			}

			if ($token['value'] === '[') { // simplified array syntax [...]
				if ($arrays[] = $prev['value'] !== ']' && $prev['type'] !== NMacroTokenizer::T_SYMBOL && $prev['type'] !== NMacroTokenizer::T_VARIABLE && $prev['type'] !== NMacroTokenizer::T_KEYWORD) {
					$tokens[] = NMacroTokenizer::createToken('array') + array('depth' => $depth);
					$token = NMacroTokenizer::createToken('(');
				}
			} elseif ($token['value'] === ']') {
				if (array_pop($arrays) === TRUE) {
					$token = NMacroTokenizer::createToken(')');
				}
			} elseif ($token['value'] === '(') { // only count
				$arrays[] = '(';

			} elseif ($token['value'] === ')') { // only count
				array_pop($arrays);
			}

			$tokens[] = $prev = $token;
		}

		if ($inTernary !== NULL) { // close ternary
			$tokens[] = NMacroTokenizer::createToken(':') + array('depth' => count($arrays));
			$tokens[] = NMacroTokenizer::createToken('null') + array('depth' => count($arrays));
		}

		$tokenizer = clone $tokenizer;
		$tokenizer->reset();
		$tokenizer->tokens = $tokens;
		return $tokenizer;
	}



	public function escape($s)
	{
		switch ($this->context[0]) {
		case NParser::CONTEXT_TEXT:
			return "NTemplateHelpers::escapeHtml($s, ENT_NOQUOTES)";
		case NParser::CONTEXT_TAG:
			return "NTemplateHelpers::escapeHtml($s)";
		case NParser::CONTEXT_ATTRIBUTE:
			list(, $name, $quote) = $this->context;
			$quote = $quote === '"' ? '' : ', ENT_QUOTES';
			if (strncasecmp($name, 'on', 2) === 0) {
				return "htmlSpecialChars(NTemplateHelpers::escapeJs($s)$quote)";
			} elseif ($name === 'style') {
				return "htmlSpecialChars(NTemplateHelpers::escapeCss($s)$quote)";
			} else {
				return "htmlSpecialChars($s$quote)";
			}
		case NParser::CONTEXT_COMMENT:
			return "NTemplateHelpers::escapeHtmlComment($s)";
		case NParser::CONTEXT_CDATA;
			return 'NTemplateHelpers::escape' . ucfirst($this->context[1]) . "($s)"; // Js, Css
		case NParser::CONTEXT_NONE:
			switch (isset($this->context[1]) ? $this->context[1] : NULL) {
			case 'xml':
			case 'js':
			case 'css':
				return 'NTemplateHelpers::escape' . ucfirst($this->context[1]) . "($s)";
			case 'text':
				return $s;
			default:
				return "\$template->escape($s)";
			}
		}
	}

}
