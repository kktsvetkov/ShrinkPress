<?php

namespace ShrinkPress\Build\Project\Entity;

use ShrinkPress\Build\Condense;

/**
* New ShrinkPress Classes
*/
class ShrinkPressClass extends WpEntity
{
	public $vendor = 'ShrinkPress';

	public $package = 'Unknown';

	public $subNamespace = '';

	static function fromClass($class)
	{
		$c = explode('\\', trim($class, '\\'));

		$className = array_pop($c);
		$classVendor = array_shift($c);
		$classPackage = array_shift($c);
		$subNamespace = $c
			? join('\\', $c)
			: '';

		return new self($className, array(
			'vendor' => $classVendor,
			'package' => $classPackage,
			'subNamespace' => $subNamespace,
		));
	}

	static function fromClassMethod($method)
	{
		$r = explode('::', $method);
		return self::fromClass( array_shift($r) );
	}

	static function fromWpFunction(WpFunction $entity)
	{
		if (empty($entity->className))
		{
			return null;
		}

		return self::fromClass($entity->classNamespace . $entity->className);
	}

	function load(array $data)
	{
		parent::load($data);

		$this->vendor = trim($this->vendor, '\\');
		$this->package = trim($this->package, '\\');
		$this->subNamespace = trim($this->subNamespace, '\\');

		return $this;
	}

	function packageFolder()
	{
		return Condense\Composer::vendors
			. '/' . strtolower($this->vendor)
			. '/' . $this->package
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
			. $this->name . '.php';
	}

	function className()
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

	function __toString()
	{
		return $this->className();
	}
}
