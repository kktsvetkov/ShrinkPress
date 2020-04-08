<?php

namespace ShrinkPress\Build\Unparse\Task;

use ShrinkPress\Build\File;
use ShrinkPress\Build\Storage;
use ShrinkPress\Build\Source;
use ShrinkPress\Build\Verbose;

class PackagesMap extends TaskAbstract
{
	protected $functionsMigration = [];
	protected $classesMigration = [];
	protected $globalsMigration = [];

	const packagesFolder = __DIR__ . '/../../../packages';

	function build( Source $source, Storage\StorageAbstract $storage )
	{
		// $globals = $storage->getGlobals();
		// print_r($globals)

		$dir = new \DirectoryIterator( self::packagesFolder );
		foreach ($dir as $found)
		{
			if ($found->isDot())
			{
				continue;
			}

			if ($found->isFile())
			{
				Verbose::log("Package: {$found}", 2);

				$definition = file_get_contents(
					$found->getPathname()
					);
				$this->parsePackage( json_decode($definition, true) );
			}
		}

		ksort($this->functionsMigration);
		$source->write(
			File\ComposerJson::vendors . '/shrinkpress/migrate/functions.json',
			json_encode($this->functionsMigration)
		);
		$source->write(
			File\ComposerJson::vendors . '/shrinkpress/migrate/classes.json',
			json_encode($this->classesMigration)
		);

		ksort($this->globalsMigration);
		$source->write(
			File\ComposerJson::vendors . '/shrinkpress/migrate/globals.json',
			json_encode($this->globalsMigration, JSON_PRETTY_PRINT)
		);
	}

	protected function parsePackage(array $definition)
	{
		if (empty($definition[':package']))
		{
			throw new \UnexpectedValueException(
				'No :package definition'
			);
		}

		$packageName = $definition[':package'];
		foreach ($definition as $className => $moved)
		{
			// :package, :url, :description, etc.
			//
			if (0 === strpos($className, ':'))
			{
				continue;
			}

			$moved += array(
				'movedClass' => '',
				'movedFunctions' => array(),
				'movedGlobals' => array(),
				);

			if (!empty($moved['movedClass']))
			{
				$oldClassName = $moved['movedClass'];
				if (!empty($this->classesMigration[ $oldClassName ]))
				{
					throw new \UnexpectedValueException(
						"Class ${$oldClassName} already exist in migration map as "
							. $this->classesMigration[ $oldClassName ]
					);
				}

				$fullClassName = $packageName . '\\' . $className ;
				$this->classesMigration[ $className ] = $fullClassName;
			}

			foreach ($moved['movedGlobals'] as $global)
			{
				$globalName = array_shift($global);
				if (!empty($this->globalsMigration[ $globalName ]))
				{
					throw new \UnexpectedValueException(
						"Global \${$globalName} already exist in migration map as "
							. $this->globalsMigration[ $globalName ]
					);
				}

				$propertyName = !empty($global)
					? array_shift($global)
					: $globalName;

				$fullPropertyName = $packageName
					. '\\' . $className
					. '::$' . $propertyName;

				$this->globalsMigration[ $globalName ] = $fullPropertyName;
			}

			foreach ($moved['movedFunctions'] as $function)
			{
				$functionName = array_shift($function);
				if (!empty($this->functionsMigration[ $functionName ]))
				{
					throw new \UnexpectedValueException(
						"Function {$functionName}() already exist in migration map as "
							. $this->functionsMigration[ $functionName ]
							. '()'
					);
				}

				$methodName = !empty($function)
					? array_shift($function)
					: $functionName;

				$fullMethodName = $packageName
					. '\\' . $className
					. '::' . $methodName;

				$this->functionsMigration[ $functionName ] = $fullMethodName;
			}
		}
	}
}
