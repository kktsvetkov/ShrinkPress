<?php

namespace ShrinkPress\Build\File;

abstract class FileAbstract implements \JsonSerializable
{
	protected $filename;

	function __construct($filename)
	{
		$this->filename = (string) $filename;

		$register = Register::instance();
		$register->addFile($this);
		$register->restore($this->filename, $this);
	}

	function filename()
	{
		return $this->filename;
	}

	function restore(array $data)
	{
		foreach ($data as $k => $v)
		{
			if (property_exists($this, $k))
			{
				$this->$k = $v;
			}
		}
	}

	function jsonSerialize()
	{
		$data = get_object_vars($this);
		return $data;
	}
}
