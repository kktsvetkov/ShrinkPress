<?php

namespace ShrinkPress\Build\Entity\File;

abstract class File_Abstract implements File_Entity
{
	protected $filename;

	function __construct($filename)
	{
		$this->filename = (string) $filename;
	}

	function filename()
	{
		return $this->filename;
	}

	function load(array $data)
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
