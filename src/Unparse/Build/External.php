<?php

namespace ShrinkPress\Reframe\Unparse\Build;

use ShrinkPress\Reframe\Unparse;
use ShrinkPress\Reframe\Index;
use ShrinkPress\Reframe\Entity;
use ShrinkPress\Reframe\Assist;

class External extends Group
{
	function __construct()
	{
		$this->addTask( new External\PhpMailer );
		$this->addTask( new External\AtomLib );
	}

	/**
	* Move php files with classes from WP package to a composer library
	*/
	static function movePackage(
		Unparse\Source $source,
		Index\Index_Abstract $index,
		$packageName,
		$classNamespace,
		$folder)
	{
		$files = $index->readPackage($packageName)->files();

		$classes = array();
		foreach ($files as $filename)
		{
			$file = $index->readFile($filename);
			foreach ($file->getClasses() as $className)
			{
				$classes[ $className ] = $filename;
			}
		}

		$composer = Entity\Files\Composer_JSON::instance();
		$libFolder = $composer::vendors . '/shrinkpress/' . $folder . '/src';
		$composer->addPsr4($classNamespace . '\\', $libFolder);

		foreach ($classes as $className => $filename)
		{
			$fullClassName = $classNamespace . '\\' . $className;

			$code = $source->read($filename);
			$lines = new Assist\FileLines($code);

			$entity = $index->readClass($className);
			$classCode = $lines->extract(
				$entity->docCommentLine
					? $entity->docCommentLine
					: $entity->startLine,
				$entity->endLine
				);

			$use = '';
			if ($entity->extends)
			{
				$use = "\n"
					. "use \\{$entity->extends} as {$entity->extends};"
					. "\n";
			}

			$classFilename = $libFolder . '/' . $className . '.php';
			$source->write($classFilename, '<?php '
				. "\n"
				. "\n" . 'namespace ' . $classNamespace . ';'
				. "\n"
				. $use
				. "\n" . $classCode
				. "\n"
				);
		}

		foreach ($files as $filename)
		{
			if (!$includes = $index->readIncludes($filename))
			{
				continue;
			}

			foreach ($includes->getIncludes() as $include)
			{
				$code = $source->read( $include[0] );

				$lines = new Assist\FileLines($code);
				$drop = $lines->extract($include[1], $include[1]);
				$code = (string) $lines;
				unset($lines);

				$source->write( $include[0] , $code);
			}
		}

		foreach ($files as $filename)
		{
			if (!$includes = $index->readIncludes($filename))
			{
				continue;
			}

			foreach ($includes->getIncludes() as $include)
			{
				foreach ($classes as $className => $classOriginFile)
				{
					if ($filename != $classOriginFile)
					{
						continue;
					}

					UseClasses::toUse(
						$include[0],
						"{$classNamespace}\\{$className}",
						$className
						);
				}
			}
		}

		// delete original files
		//
		foreach ($files as $filename)
		{
			$source->unlink($filename);
		}
	}
}
