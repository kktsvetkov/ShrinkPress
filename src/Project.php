<?php

namespace ShrinkPress\Build;

class Project
{
	protected $build;

	const LOG_FOUND = 'found.txt';
	const LOG_IGNORE = 'ignore.txt';
	const LOG_ERROR = 'error.txt';

	function __construct($build)
	{
		$this->build = $build;
	}

	function start($base)
	{
		$this->clear(self::LOG_FOUND);
		$this->log(self::LOG_FOUND, '# ' . gmdate(\DateTime::RFC850));
		$this->log(self::LOG_FOUND, "# {$base}" );

		$this->clear(self::LOG_IGNORE);
		$this->log(self::LOG_IGNORE, '# ' . gmdate(\DateTime::RFC850));
		$this->log(self::LOG_IGNORE, "# {$base}");

		$this->clear(self::LOG_ERROR);
	}

	protected function clear($log)
	{
		$local = $this->build . '/' . $log;
		if (file_exists($local))
		{
			return unlink($local);
		}
		return true;
	}

	function log($log, $file)
	{
		$local = $this->build . '/' . $log;
		file_put_contents($local, $file . "\n", FILE_APPEND);
	}

	function read(WpEntity $entity)
	{
		$local = $this->build . '/' . $entity->getFile();
		if (file_exists($local))
		{
			$data = include $local;
			$entity->load($data);
		}
	}

	function write(WpEntity $entity)
	{
		$local = $this->build . '/' . $entity->getFile();
		file_put_contents($local, var_export($entity->getData(), true));
	}
}
