<?php

namespace ShrinkPress\Reframe\Unparse\Build;

use ShrinkPress\Reframe\Unparse;
use ShrinkPress\Reframe\Index;

use ShrinkPress\Reframe\Assist;

class UseClasses implements Task
{
	static $used = array();

	/**
	* Store "use class as class" declarations for later
	*/
	static function toUse($filename, $classOrNamespace, $as)
	{
		self::$used[ $filename ][ $classOrNamespace ] = $as;
	}

	function build(Unparse\Source $source, Index\Index_Abstract $index )
	{
		foreach (self::$used as $filename => $classes)
		{
			Assist\Verbose::log("Use classes for: {$filename}", 1);

			$code = $source->read( $filename );
			$code = rtrim($code) . "\n";

			foreach ($classes as $className => $as)
			{
				Assist\Verbose::log("Using: {$className}", 2);
				$code = Assist\Code::addUse($code, $className, $as);
			}

			$source->write( $filename , $code);
		}
	}
}
