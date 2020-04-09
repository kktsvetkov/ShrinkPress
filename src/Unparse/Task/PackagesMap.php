<?php

namespace ShrinkPress\Build\Unparse\Task;

use ShrinkPress\Build\Entity\File;
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

		$this->functionsMissing($storage);
		ksort($this->functionsMigration);
		$source->write(
			File\Composer_JSON::vendors . '/shrinkpress/migrate/functions.json',
			json_encode($this->functionsMigration, JSON_PRETTY_PRINT)
		);

		ksort($this->classesMigration);
		$source->write(
			File\Composer_JSON::vendors . '/shrinkpress/migrate/classes.json',
			json_encode($this->classesMigration, JSON_PRETTY_PRINT)
		);

		$this->globalsMissing($storage);
		ksort($this->globalsMigration);
		$source->write(
			File\Composer_JSON::vendors . '/shrinkpress/migrate/globals.json',
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

	protected function globalsMissing(Storage\StorageAbstract $storage)
	{
		$globals = $storage->getGlobals();
		$globalsMissing = array_diff(
			$globals,
			array_keys($this->globalsMigration)
			);
		foreach ($globalsMissing as $global)
		{
			$this->globalsMigration[ $global ] =
				'ShrinkPress\\Scope\\Globals::$' . $global;
		}

		unset($this->globalsMigration[ 'HTTP_RAW_POST_DATA' ]);
		unset($this->globalsMigration[ 'GETID3_ERRORARRAY' ]);
		unset($this->globalsMigration[ 'PHP_SELF' ]);
	}

	protected function functionsMissing(Storage\StorageAbstract $storage)
	{
		$functions = $storage->getFunctions();
		$functionsMissing = array_diff(
			$functions,
			array_keys($this->functionsMigration)
			);
		foreach ($functionsMissing as $function)
		{
			$this->functionsMigration[ $function ] =
				'ShrinkPress\\Scope\\Functions::' . $function;
		}
	}
}
