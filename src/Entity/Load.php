<?php

namespace ShrinkPress\Reframe\Entity;

trait Load
{
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

	function jsonSerialize()
	{
		$data = get_object_vars($this);
		return $data;
	}
}
