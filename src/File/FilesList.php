<?php

namespace ShrinkPress\Build\File;

use ShrinkPress\Build\Assist;

class FilesList extends FileAbstract
{
	use Assist\Instance;

	protected $filename = 'files';

	function __construct()
	{
		$register = Register::instance();
		$register->addFile($this);
	}

	protected $files = array();

	function files()
	{
		return array_keys($this->files);
	}

	function addFile($file)
	{
		$file = (string) $file;
		$this->files[ $file ] = 1;
	}

}
