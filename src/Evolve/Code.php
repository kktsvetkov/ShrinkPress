<?php

namespace ShrinkPress\Reframe\Evolve;

class Code
{
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
}
