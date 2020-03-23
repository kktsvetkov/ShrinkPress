<?php

namespace ShrinkPress\Build;

class Project
{
	protected $build;

	const LOG_FOUND = 'found.csv';
	const LOG_IGNORE = 'ignore.csv';

	const LOG_FUNCTIONS = 'functions.csv';
	const LOG_CLASSES = 'classes.csv';

	const LOG_CHANGES = 'changes.log';

	function __construct($build)
	{
		$this->build = $build;
		Verbose::log("Project: {$build}", 1);
	}

	/**
	* Clears a log
	* @param string $log
	*/
	function clear($log)
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

		file_put_contents(
			$local,
			'<?php return ' . var_export($entity->getData(), true) . '; '
			);
	}

	function functions()
	{
		$local = $this->build . '/' . self::LOG_FUNCTIONS;
		$funcs = array_map('trim', file($local));
		$funcs = array_values( array_unique($funcs) );
		return $funcs;
	}
}
