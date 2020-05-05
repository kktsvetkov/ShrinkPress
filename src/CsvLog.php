<?php

namespace ShrinkPress\Evolve;

class CsvLog
{
	static function clean($filename)
	{
		if (!file_exists($filename))
		{
			return false;
		}

		return unlink($filename);
	}

	static function append($filename, array $data)
	{
		$fp = fopen($filename, 'a+');
		fputcsv($fp, $data);
		fclose($fp);
	}
}
