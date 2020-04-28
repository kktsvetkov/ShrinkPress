<?php

namespace ShrinkPress\Reframe\Evolve;

class phpDocPackages
{
	static function getFunction(array $f)
	{
		$d = self::extractPackages($f['filename']);

		if (empty($d[1]))
		{
			$d[1] = $d[0];
		}

		return array(
			'method' => $f['function'],
			'class' => $d[1],
			'namespace' => $ns = join('\\', $d),
			'full' => $ns . '\\' . $d[1] . '::' . $f['function']
			);
	}

	static $known = array();

	static function extractPackages($filename)
	{
		if (!empty(self::$known[ $filename ]))
		{
			return self::$known[ $filename ];
		}

		$result = array('WordPress', 'Core');
		$doccomment = substr(file_get_contents($filename), 0, 1024);

		if (preg_match('~\s*\*\s+@package\s+(.+)\s+\*~Uis', $doccomment, $R))
		{
			$result[0] = $R[1];
		}

		if (preg_match('~\s*\*\s+@subpackage\s+(.+)\s+\*~Uis', $doccomment, $R))
		{
			$result[1] = $R[1];
		}

		return self::$known[ $filename ] = $result;
	}
}
