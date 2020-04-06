<?php

namespace ShrinkPress\Build\File;

use ShrinkPress\Build\Verbose;

class ShrinkPress extends FileAbstract
{
	public $classVendor = 'ShrinkPress';

	public $classPackage = 'Unknown';

	public $subNamespace = '';

	public $className;

	public $uses = array();

	public $methods = array();

	static function fromClass($fullClass)
	{
		$fullClass = (string) $fullClass;
		$c = explode('\\', trim($fullClass, '\\'));

		$sp = new self('dummy');

		$sp->className = array_pop($c);
		$sp->classVendor = array_shift($c);
		$sp->classPackage = array_shift($c);
		$sp->subNamespace = $c
			? join('\\', $c)
			: '';

		$sp->filename = $sp->classFile();
		return $sp;
	}

	function packageFolder()
	{
		return ComposerJson::vendors
			. '/' . strtolower($this->classVendor)
			. '/' . $this->classPackage
			. '/src/';
	}

	function classFile()
	{
		$sub = '';
		if ($this->subNamespace)
		{
			$sub = str_replace('\\', '/', $this->subNamespace) . '/';
		}

		return $this->packageFolder()
			. $sub
			. $this->className . '.php';
	}

	function fullClassName()
	{
		return '\\' . $this->classPackage()
			. $this->subNamespace
			. '\\' . $this->name;
	}

	function classPackage()
	{
		return $this->vendor . '\\' . $this->package . '\\';
	}

	function classNamespace()
	{
		return '\\' . $this->classPackage() . $this->subNamespace;
	}
}
