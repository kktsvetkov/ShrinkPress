<?php

namespace ShrinkPress\Reframe\Assist;

trait Instance
{
	static protected $instance;

	static function instance()
	{
		if (empty(self::$instance))
		{
			self::$instance = new self;
		}

		return self::$instance;
	}
}
