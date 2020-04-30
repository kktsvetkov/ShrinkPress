<?php

namespace ShrinkPress\Evolve;

class Code
{
	static function tabify($code)
	{
		$code = str_replace("\n", "\n\t" , $code);
		$code = "\t" . rtrim($code, "\t");
		return $code;
	}

	static function extractByLines($code, $fromLine, $toLine)
	{
		$lines = explode("\n", $code);
		$total = count($lines);

		if ($fromLine > $total || $fromLine < 1)
		{
			throw new \InvalidArgumentException(
				"Invalid \$fromLine {$fromLine}, total number of lines is {$total} "
			);
		}
		if ($toLine > $total || $toLine < 1)
		{
			throw new \InvalidArgumentException(
				"Invalid \$toLine {$toLine}, total number of lines is {$total} "
			);
		}

		$found = '';
		for ($i = $fromLine; $i < $toLine+1; $i++)
		{
			$found .= $lines[ $i - 1 ] . "\n";
			unset($lines[ $i - 1 ]);
		}

		return array($found, join("\n", $lines));
	}

	static function extractDefinition(&$code, array $e)
	{
		$c = self::extractByLines($code, $e['startLine'], $e['endLine']);
		$e['code'] = $c[0];
		$code = $c[1];

		if ($e['docCommentLine'])
		{
			$b = self::extractByLines($code,
				$e['docCommentLine'], $e['startLine']-1);

			$e['docComment'] = $b[0];
			$code = $b[1];
		}

		return $e;
	}

	static function injectCode($code, array $seek, $inject)
	{
		$tokens = token_get_all($code);

		$modified = array();
		$last = array();
		foreach ($tokens as $token)
		{
			$oken = is_scalar($token) ? $token : $token[1];
			$modified[] = $oken;

			// skip T_WHITESPACE
			//
			if (382 == $token[0])
			{
				continue;
			}

			array_push($last, $oken);
			if (count($last) > count($seek))
			{
				array_shift($last);
			}

			if ($seek == $last)
			{
				$modified[] = $inject;
			}
		}

		return join('', $modified);
	}

	static function renameMethod($code, $from, $to)
	{
		if ($from == $to)
		{
			return $code;
		}

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

	static function addUse($code, $classOrNamespace, $as = '')
	{
		$seek = array(379, 378, 382);
		$tokens = token_get_all($code);

		$modified = array();
		$last = array();
		foreach ($tokens as $i => $token)
		{
			$oken = is_scalar($token) ? $token : $token[1];
			$modified[] = $oken;

			array_push($last, $token[0]);
			if (count($last) > count($seek))
			{
				array_shift($last);
			}

			if ($seek == $last)
			{
				$space = array_pop($modified);

				$modified[] = "\n" . 'use '
					. $classOrNamespace
					. ($as ? " as {$as}" : '')
					. ';';

				$modified[] = $space;
			}
		}

		return join('', $modified);
	}

	static function replaceHook($code, $function, $method)
	{
		$tokens = token_get_all( '<?php ' . $code);
		array_shift($tokens);

		$modified = array();
		foreach ($tokens as $i => $token)
		{
			$oken = is_scalar($token) ? $token : $token[1];

			if (323 == $token[0])
			{
				if ($oken == "'{$function}'")
				{
					$oken = "'{$method}'";
				} else
				if ($oken == "\"{$function}\"")
				{
					$oken = "\"{$method}\"";
				}
			}

			$modified[] = $oken;
		}

		return join('', $modified);
	}

	static function replaceCall($code, $function, $method)
	{
		$tokens = token_get_all( '<?php ' . $code);
		array_shift($tokens);

		$modified = array();
		foreach ($tokens as $i => $token)
		{
			$oken = is_scalar($token) ? $token : $token[1];

			if (319 == $token[0])
			{
				if ($function == $oken)
				{
					$oken = $method;
				}
			}

			$modified[] = $oken;
		}

		return join('', $modified);
	}
}
