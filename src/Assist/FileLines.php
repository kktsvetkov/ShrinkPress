<?php

namespace ShrinkPress\Build\Assist;

class FileLines
{
	protected $lines = [];

	function __construct($code)
	{
		$this->lines = explode("\n", $code);
	}

	function __toString()
	{
		return join("\n", $this->lines);
	}

	function extract($fromLine, $toLine)
	{
		$total = count($this->lines);
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

		// do not remote the lines for extracted entries,
		// just make them blank in order to make future
		// references to lines match; we can trim the
		// phantom empty lines later;
		//
		$found = '';
		for ($i = $fromLine; $i < $toLine; $i++)
		{
			$found .= $this->lines[ $i - 1 ] . "\n";
			$this->lines[ $i - 1 ] = '';
		}

		return $found;
	}
}
