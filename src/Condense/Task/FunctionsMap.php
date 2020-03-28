<?php

namespace ShrinkPress\Build\Condense\Task;

use ShrinkPress\Build\Project;
use ShrinkPress\Build\Condense;

class FunctionsMap extends TaskAbstract
{
	function condense(
		Project\Source $source,
		Project\Storage\StorageAbstract $storage
		)
	{
		$composer = Condense\Composer::instance();

		// $packages = Condense\Packages::instance();
		$packages = Condense\ProtoPackages::instance();
		$packages->setStorage($storage);

		foreach ($packages->getPackages() as $package)
		{
			$def = $packages->definition( $package );
			foreach($def as $lib => $classes)
			{
				$ns = Condense\Transform::wpNamespace( $lib );
				$composer->addPsr4(
					$namespace = $ns[0],
					$folder = $ns[1]);

				foreach ($classes as $class => $methods)
				{
					foreach ($methods as $method)
					{
						$func = $method[0];
						$entity = $storage->readFunction(
							$func
							);

						if (!$entity)
						{
							throw new \UnexpectedValueException(
								"Not able to find function {$func}()"
							);
						}

						$entity->classNamespace = $namespace;
						$entity->className = $class;
						$entity->classMethod = !empty($method[1])
							? $method[1]
							: Condense\Transform::wpFunction( $func );
						$entity->classFile = $folder
							. str_replace('\\', '/', $class)
							. '.php';

						$storage->writeFunction( $entity );
					}
				}
			}

			// update composer
			//
			$source->write('composer.json', $composer->json() );
			$composer->dumpautoload( $source->basedir() );
		}
	}


}
