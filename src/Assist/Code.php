<?php

namespace ShrinkPress\Reframe\Assist;

class Code
{
	/**
	* Extract arguments declaration
	*
	* @param string $code PHP code with function declaration
	* @param string $functionName
	* @return string
	*/
	static function arguments($code, $functionName)
	{
		$args = '';
		$tokens = token_get_all( '<?php ' . $code);

		$seek = array( 'function', (string) $functionName, '(');

		$last = array();
		$startAt = null;
		foreach ($tokens as $i => $token)
		{
			$oken = is_scalar($token) ? $token : $token[1];

			// first, find where the arguments start,
			// skip white-space when seeking to compare
			//
			if (is_null($startAt) && (382 != $token[0]))
			{
				array_push($last, $oken);
				if (count($last) > count($seek))
				{
					array_shift($last);
				}

				if ($seek == $last)
				{
					$startAt = $i + 1;
					$args = '(';
				}

				continue;
			}

			// arguments stop right before the opening bracket
			//
			if ('{' == $oken)
			{
				break;
			}

			$args .= $oken;
		}

		return $args;
	}

	/**
	* Transform declaration function arguments into call arguments
	*
	* @param strng $args
	* @return string
	*/
	static function callArguments($args)
	{
		$args = (string) $args;
		if ( '()' == $args)
		{
			return $args;
		}

		$tokens = token_get_all( '<?php function x' . $args . '{}');

		$result = '';
		$tokens = array_slice($tokens, 5, -3);
		foreach ($tokens as $i => $token)
		{
			$oken = is_scalar($token) ? $token : $token[1];

			if ('&' == $oken)
			{
				continue;
			}

			switch ($token[0])
			{
				case ',':
					$result .= ', ';
					break;

				case 320:	// T_VARIABLE
					$result .= $oken;
					break;
			}
		}

		$result = rtrim($result, ', ');
		return '(' . $result . ')';
	}

	/**
	* Rename a function
	*
	* @param string $from function name
	* @param string $to new method name
	* @param string $code PHP code with function declaration
	* @return string
	*/
	static function renameMethod($from, $to, $code)
	{
		$tokens = token_get_all( '<?php ' . $code);
		array_shift($tokens);
		$code = '';

		$seek = array( 'function', (string) $from );
		$last = array();
		foreach ($tokens as $i => $token)
		{
			$oken = is_scalar($token) ? $token : $token[1];

			// ignore whitespace
			//
			if (382 != $token[0])
			{
				array_push($last, $oken);
				if (count($last) > count($seek))
				{
					array_shift($last);
				}
			}

			if ($seek == $last)
			{
				$oken = (string) $to;
			}

			$code .= $oken;
		}

		return $code;
	}

	/**
	* Ident with one tab
	* @param string $code
	* @return string
	*/
	static function tabify($code)
	{
		$code = str_replace("\n", "\n\t" , $code);
		$code = "\t" . rtrim($code, "\t");
		return $code;
	}

	static function extractPackage($code, $entity)
	{
		$doccomment = substr($code, 0, 1024);

		if (preg_match('~\s*\*\s+@package\s+(.+)\s+\*~Uis', $doccomment, $R))
		{
			$entity->docPackage = $R[1];
		}

		if (preg_match('~\s*\*\s+@subpackage\s+(.+)\s+\*~Uis', $doccomment, $R))
		{
			$entity->docSubPackage = $R[1];
		}

		return $entity;
	}
}
