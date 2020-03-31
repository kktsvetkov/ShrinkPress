<?php

namespace ShrinkPress\Build\Project\Entity;

abstract class WpEntity
{
	public $name;

	function __construct($name, array $data = [])
	{
		$this->name = $name;

		if ($data)
		{
			$this->load($data);
		}
	}

	function getData()
	{
		return get_object_vars($this);
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

		return $this;
	}
}
