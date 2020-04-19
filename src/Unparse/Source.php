<?php

namespace ShrinkPress\Reframe\Unparse;

use ShrinkPress\Reframe\Assist;

class Source Extends Assist\Umbrella
{
	function read($filename)
	{
		Assist\Verbose::log("Read: {$filename}", 3);
		return parent::read($filename);
	}

	function write($filename, $contents)
	{
		Assist\Verbose::log("Write: {$filename}", 2);
		return parent::write($filename, $contents);
	}

       function unlink($filename)
       {
		Assist\Verbose::log("Delete: {$filename}", 1);
		return parent::unlink($filename);
       }
}
