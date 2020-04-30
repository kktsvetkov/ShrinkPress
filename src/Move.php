<?php

namespace ShrinkPress\Evolve;

class Move
{
	static function classNameToFilename($fullClassName)
	{
		$c = explode('\\', $fullClassName);
		$topLevel = array_shift($c);
		$library = array_shift($c);

		$filename = Composer::vendors
			. '/' . strtolower($topLevel)
			. '/' . strtolower($library)
			. '/src/' . join('/', $c) . '.php';

		return $filename;
	}

	static function createClass(array $s, $classFilename)
	{
		if (file_exists($classFilename))
		{
			throw new \RuntimeException(
				'Class file already exists: '
					. $classFilename
			);
		}

		if (!file_exists($dir = dirname($classFilename)))
		{
			mkdir($dir, 02777, true);
		}

		$code = array(
			'<?php ',
			'',
			'namespace ' . $s['namespace'] . ';',
			'',
			'class ' . $s['class'],
			'{',
			'}',
			''
			);
		file_put_contents($classFilename, join("\n", $code));

		$d = explode('/src/', $dir);
		Composer::addPsr4($s['namespace'], $d['0']);
	}

	static function moveFunction(array $f, array $m)
	{
		$fullClassName = $m['namespace'] . '\\' . $m['class'];
		$classFilename = self::classNameToFilename($fullClassName);
		if (!file_exists($classFilename))
		{
			self::createClass($m, $classFilename);
		}

		$renamed = Code::renameMethod($f['code'], $f['function'], $m['method']);
		self::insertMethod(
			(!empty($f['docComment'])
				? $f['docComment']
				: '') . $renamed,
			$classFilename);

		Composer::updateComposer();
	}

	static function insertMethod($methodCode, $classFilename)
	{
		if (!file_exists($classFilename))
		{
			throw new \InvalidArgumentException(
				'Class file not found: '
					. $classFilename
			);
		}

		$code = file_get_contents($classFilename);
		$methodCode = Code::tabify($methodCode);

		$className = pathinfo($classFilename, PATHINFO_FILENAME);
		$code = Code::injectCode($code,
			array('class', $className, '{'),
			"\n" . $methodCode . "\n"
			);

		file_put_contents($classFilename, $code);
	}
}
