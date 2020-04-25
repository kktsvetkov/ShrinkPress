<?php

namespace ShrinkPress\Reframe\Unparse\Build\External;

use ShrinkPress\Reframe\Unparse;
use ShrinkPress\Reframe\Index;

use ShrinkPress\Reframe\Entity;
use ShrinkPress\Reframe\Assist;

use ShrinkPress\Reframe\Unparse\Build\UseClasses;

class PhpMailer implements Unparse\Build\Task
{
	function build(Unparse\Source $source, Index\Index_Abstract $index )
	{
		$this->same($source, $index);
		// $this->latest($source, $index);
	}

	/**
	* Just move bundled PHPMailer into a package
	*/
	protected function same(Unparse\Source $source, Index\Index_Abstract $index )
	{
		$files = $index->readPackage('PHPMailer')->files();
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

		$classNamespace = 'ShrinkPress\\Mail';
		$namespace = $classNamespace . '\\';
		$folder = $composer::vendors . '/shrinkpress/mail/src';

		$composer->addPsr4($namespace, $folder);

		foreach ($classes as $className => $filename)
		{
			$fullClassName = $namespace . $className;

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

			$classFilename = $folder . '/' . $className . '.php';
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
						"{$namespace}{$className}",
						$className
						);
				}
			}
		}

		foreach ($files as $filename)
		{
			$source->unlink($filename);
		}
	}

	/**
	* Get the latest PHPMailer, ignore the bundled one
	*/
	protected function latest(Unparse\Source $source, Index\Index_Abstract $index )
	{

	}
}
