<?php

namespace ShrinkPress\Evolve;

abstract class Reframe
{
	// const reframeClass = Reframe_Proto::class;
	// const reframeClass = Reframe_Migrate::class;
	const reframeClass = Reframe_PhpDoc::class;

	static private $instance;

	static private function instance()
	{
		if (!isset(self::$instance))
		{
			$class = self::reframeClass;
			self::$instance = new $class;
		}

		return self::$instance;
	}

	final static function getFunction(array $f)
	{
		return self::instance()->reframeFunction($f['function'], $f['filename']);
	}

	abstract function reframeFunction($function, $filename);

	final static function getClass(array $c)
	{
		return self::instance()->reframeClass($c['class'], $c['filename']);
	}

	abstract function reframeClass($class, $filename);

	final static function getGlobal(array $g)
	{
		return self::instance()->reframeGlobal($g['global'], $g['filename']);
	}

	abstract function reframeGlobal($global, $filename);
}
