<?php

namespace ShrinkPress\Build\Condense\Task;

use ShrinkPress\Build\Project;
use ShrinkPress\Build\Condense;
use ShrinkPress\Build\Verbose;

class ReplaceFunctions extends TaskAbstract
{
	function condense(
		Project\Source $source,
		Project\Storage\StorageAbstract $storage
		)
	{
		$compat = Condense\Compat::instance();

		while ($replace = SortFunctions::pop())
		{

echo ':r ', count($replace), ' of ', count(SortFunctions::$map), "\n";

			foreach ($replace as $name)
			{
				$entity = $storage->readFunction($name);
				if (!$entity->fileOrigin)
				{
					Verbose::log("No file: {$entity->name}()", 3);
					continue;
				}

				// Verbose::log("Replace: {$entity->name}()", 1);

				// $found = $this->removeOriginal($entity, $source);
				// $this->declareMethod($found, $entity, $source);
				//
				// $compat->addFunction($entity, $source);
				// $this->replaceCalls();

				SortFunctions::remove( $entity->name );
			}
		}

		// no more functions left to replace,
		// check if there is anything left
		//
		if (!empty(SortFunctions::$map))
		{
			print_r(array_keys(SortFunctions::$map));
			exit;
		}

		$compat->dump($source);
	}

	protected function removeOriginal($entity, $source)
	{
		$code = $source->read($entity->fileOrigin);
		$lines = explode("\n", $code);

		// do not remote the lines for extracted entries,
		// just make them blank in order to make future
		// references to lines match; we can trim the
		// phantom empty lines later;
		//

		$doccoment = '';
		if ($entity->docCommentLine)
		{
			for ($i = $entity->docCommentLine; $i < $entity->startLine; $i++)
			{
				$doccoment .= $lines[ $i - 1 ] . "\n";
				$lines[ $i - 1 ] = '';
			}
		}

		$function = '';
		for ($i = $entity->startLine; $i <= $entity->endLine; $i++)
		{
			$function .= $lines[ $i - 1 ] . "\n";
			$lines[ $i - 1 ] = '';
		}

		$modified = join("\n", $lines);
		$source->write($entity->fileOrigin, $modified);

		return array(
			'doccoment' => $doccoment,
			'function' => $function,
			);
	}

	protected function renameMethod($method, $code)
	{
		return $code;
	}

	protected function declareMethod($declaration, $entity, $source)
	{
		// new method name ?
		//
		if ($entity->name != $entity->classMethod)
		{
			$declaration['function'] = $this->renameMethod(
				$entity->classMethod,
				$declaration['function']
				);
		}

		// get shrink class code
		//
		$code = '';
		if ($source->exists($entity->classFile))
		{
			$code = $source->read($entity->classFile);
		}

		if(!$code)
		{
			$code = Condense\Transform::wpClassFile(
				$entity->classNamespace,
				$entity->className
			);
		}

		// find the end of the class where to insert it
		//
		$insertBefore = 0;
		$tokens = token_get_all($code);
		for ($i = count($tokens) -1; $i >= 0; $i--)
		{
			if (is_scalar($tokens[ $i ]))
			{
				if ('}' == $tokens[$i])
				{
					$insertBefore = $i;
					break;
				}
			}
		}

		if (!$insertBefore)
		{
			throw new \RuntimeException(
				'End of class not found'
			);
		}

		$updated = array();
		foreach ($tokens as $i => $token)
		{
			if ($i == $insertBefore)
			{
				$updated[] = "\n";
				$updated[] = Condense\Transform::tabify(
					$declaration['doccoment']
					);
				$updated[] = Condense\Transform::tabify(
					$declaration['function']
					);
			}

			$oken = is_scalar($token) ? $token : $token[1];
			$updated[] = $oken;
		}

		$code = join('', $updated);
		$source->write($entity->classFile, $code);

		$entity->docComment = $declaration['doccoment'];
		$entity->functionCode = $declaration['function'];
	}

	protected function replaceCalls()
	{

	}
}
