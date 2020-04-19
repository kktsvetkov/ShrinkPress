<?php

chdir(__DIR__ . '/reduced');
exec('git status', $s);

function php_lint($php)
{
	$f = trim($php);
	$o = shell_exec('php -l ' . $f);

	$no = "No syntax errors detected in {$f}";
	if (trim($o) == $no)
	{
		echo ".";
		return true;
	}

	echo "> {$f}\n{$o}";
	return false;
}

foreach($s as $l)
{
	if (!preg_match('~^modified\:\s+(.+\.php)$~Uis', trim($l), $R))
	{
		continue;
	}

	php_lint($R[1]);
}

function vendor_lint($folder)
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
			vendor_lint($full);
			continue;
		}

		php_lint($full);
	}

	closedir($dir);
}

vendor_lint(__DIR__ . '/reduced/wp-includes/vendor/shrinkpress');

echo "\n";
