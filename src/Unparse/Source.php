<?php

namespace ShrinkPress\Build\Unparse;

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
		Verbose::log("Write: {$filename}", 2);
		return parent::write($filename);
	}

       function unlink($filename)
       {
		Verbose::log("Delete: {$filename}", 1);
		return parent::unlink($filename);
       }
}
