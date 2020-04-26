<?php

namespace ShrinkPress\Reframe\Evolve;

class Code
{
	static function extract($code, $fromLine, $toLine)
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

}
