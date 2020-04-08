<?php

namespace ShrinkPress\Build;

use ShrinkPress\Build\Verbose;

class Source extends Assist\Umbrella
{
	function read($filename)
	{
		Verbose::log("Read: {$filename}", 3);
		return parent::read($filename);
	}

	function write($filename, $contents)
	{
		Verbose::log("Write: {$filename}", 1);
		return parent::write($filename, $contents);
	}

	function unlink($filename)
	{
		Verbose::log("Delete: {$filename}", 1);
		return parent::unlink( $filename );
	}

}
