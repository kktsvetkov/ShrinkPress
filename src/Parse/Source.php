<?php

namespace ShrinkPress\Build\Parse;

use ShrinkPress\Build\Assist;

class Source Extends Assist\Umbrella
{
	function read($filename)
	{
		Verbose::log("Read: {$filename}", 3);
		return parent::read($filename);
	}

	function write($filename, $contents)
	{
		throw new \RuntimeException(
			'You are not allowed to write files '
		);
	}

       function unlink($filename)
       {
		throw new \RuntimeException(
			'You are not allowed to delete files '
		);
       }
}
