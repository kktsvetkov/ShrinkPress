<?php

namespace ShrinkPress\Build;

class Project
{
	protected $build;

	const LOG_FOUND = 'found.csv';
	const LOG_IGNORE = 'ignore.csv';
	const LOG_ERROR = 'error.txt';

	const LOG_FUNCTIONS = 'functions.csv';
	const LOG_CLASSES = 'classes.csv';

	function __construct($build)
	{
		$this->build = $build;
		Verbose::log("Project: {$build}", 1);
	}

	/**
	* Starts the project from the files in $base folder
	* @param $base $log
	*/
	function start($base)
	{
		Verbose::log("Source: {$base}", 1);

		$this->clear(self::LOG_FOUND);
		$this->log(self::LOG_FOUND, '# ' . gmdate(\DateTime::RFC850));
		$this->log(self::LOG_FOUND, "# {$base}" );

		$this->clear(self::LOG_IGNORE);
		$this->log(self::LOG_IGNORE, '# ' . gmdate(\DateTime::RFC850));
		$this->log(self::LOG_IGNORE, "# {$base}");

		$this->clear(self::LOG_ERROR);
	}

	/**
	* Clears a log
	* @param string $log
	*/
	protected function clear($log)
	{
		$local = $this->build . '/' . $log;
		if (file_exists($local))
		{
			return unlink($local);
		}
		return true;
	}

	/**
	* Report a $file to a $log
	* @param string $log
	* @param string $file
	*/
	function log($log, $file)
	{
		$local = $this->build . '/' . $log;
		file_put_contents($local, $file . "\n", FILE_APPEND);
	}

	/**
	* Reads the harvested details about this $entity
	* @param WpEntity $entity
	*/
	function read(WpEntity $entity)
	{
		$local = $this->build . '/' . $entity->getFile();
		if (file_exists($local))
		{
			$data = include $local;
			$entity->load($data);
		}
	}

	/**
	* Writes the harvested details about this $entity
	* @param WpEntity $entity
	*/
	function write(WpEntity $entity)
	{
		$local = $this->build . '/' . $entity->getFile();

		$dir = dirname($local);
		if (!file_exists($dir))
		{
			mkdir($dir, 0777, true);
		}

		file_put_contents($local, var_export($entity->getData(), true));
	}
}
