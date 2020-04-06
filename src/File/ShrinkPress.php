<?php

namespace ShrinkPress\Build\File;

use ShrinkPress\Build\Verbose;

class ShrinkPress extends FileAbstract
{
	public $vendorName = 'ShrinkPress';
	public $packageName = 'Unknown';
	public $subNamespace = '';
	public $className;

	public $uses = array();

	public $methods = array();

	static function fromClass($fullClassName)
	{
		$fullClassName = (string) $fullClassName;
		$c = explode('\\', trim($fullClassName, '\\'));

		$sp = new self('dummy');

		$sp->className = array_pop($c);
		$sp->vendorName = array_shift($c);
		$sp->packageName = array_shift($c);
		$sp->subNamespace = $c
			? join('\\', $c)
			: '';

		$sp->filename = $sp->classFile();

		$composerJson = ComposerJson::instance();
		$composerJson->addPsr4(
			$sp->classPackage(),
			$sp->packageFolder()
			);

		return $sp;
	}

	static function fromClassMethod($fullMethodName)
	{
		$fullMethodName = (string) $fullMethodName;

		$r = explode('::', $fullMethodName);
		return self::fromClass( array_shift($r) );
	}

	function packageFolder()
	{
		return ComposerJson::vendors
			. '/' . strtolower($this->vendorName)
			. '/' . $this->packageName
			. '/src/';
	}

	function classFile()
	{
		$subNamespace = '';
		if ($this->subNamespace)
		{
			$subNamespace = str_replace('\\', '/', $this->subNamespace) . '/';
		}

		return $this->packageFolder()
			. $subNamespace
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
		return $this->vendorName . '\\' . $this->packageName . '\\';
	}

	function classNamespace()
	{
		return $this->classPackage() . $this->subNamespace;
	}

	function useClass($fullClassName)
	{
		$fullClassName = (string) $fullClassName;
		$sp = self::fromClass( $fullClassName );

		$this->uses[ $sp->classNamespace() ] = true;
		return $this;
	}

	function useMethod($fullMethodName)
	{
		$fullMethodName = (string) $fullMethodName;
		$sp = self::fromClassMethod( $fullMethodName );

		$this->uses[ $sp->classNamespace() ] = true;
		return $this;
	}
}
