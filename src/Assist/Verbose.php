<?php

namespace ShrinkPress\Build\Assist;

class Verbose
{
	const LEVEL_0 = 0;
	const LEVEL_1 = 1;
	const LEVEL_2 = 2;
	const LEVEL_3 = 3;

	protected static $level = self::LEVEL_0;

	static function level($level = null)
	{
		if (!is_null($level))
		{
			self::$level = self::valid( $level );
		}

		return self::$level;
	}

	private static function valid($level)
	{
		if ($level > self::LEVEL_3)
		{
			$level = self::LEVEL_3;
		} else
		if ($level < self::LEVEL_0)
		{
			$level = self::LEVEL_0;
		}

		return $level;
	}

	static function log($msg, $level = self::LEVEL_0)
	{
		$level = self::valid( $level );
		if (!$level)
		{
			return false;
		}

		if ($level > self::$level)
		{
			return false;
		}

		echo (string) $msg, "\n";
	}
}
