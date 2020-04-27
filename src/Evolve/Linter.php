<?php

namespace ShrinkPress\Reframe\Evolve;

class Linter
{
	const no_syntax_errors = 'No syntax errors detected in %s';

	static function getLintError($php)
	{
		$file = trim($php);
		$output = shell_exec('php -l ' . $f);

		$no_syntax_errors = sprintf(self::no_syntax_errors, $file);
		if (trim($o) != $no)
		{
			throw new \RuntimeException($output);
		}
	}

	static function lintVendors($folder)
	{
		if (!is_dir($folder))
		{
			return false;
		}

		$dir = opendir($folder);
		while (false !== ($file = readdir($dir)))
		{
			if (0 === strpos($file, '.'))
			{
				continue;
			}

			$full = $folder . '/' . $file;
			if (is_dir($full))
			{
				self::lintVendors($full);
				continue;
			}

			self::getLintError($full);
		}

		closedir($dir);
	}

	static function lintGitModified()
	{
		exec('git status', $output);

		foreach($output as $line)
		{
			if (preg_match('~^modified\:\s+(.+\.php)$~Uis', trim($line), $R))
			{
				self::getLintError($R[1]);
			}
		}
	}

	static function all()
	{
		self::lintGitModified();
		self::lintVendors( Composer::vendors );
	}
}
