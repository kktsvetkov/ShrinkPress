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

		foreach ($storage->sortedFunctions as $name => $calls)
		{
			$entity = $storage->readFunction($name);

			if (!$entity->fileOrigin)
			{
				Verbose::log("No file: {$entity->name}()", 3);
				continue;
			}

			Verbose::log("Replace: {$entity->name}()", 1);

			$this->declareMethod($entity, $source);
			$this->removeOriginal();
			$compat->addFunction($entity);
			$this->replaceCalls();
BREAK;
		}
	}

	protected function getFunctionDeclaration($entity, $source)
	{
		$code = $source->read($entity->fileOrigin);
		$lines = explode("\n", $code);

		$doccoment = '';
		if ($entity->docCommentLine)
		{
			for ($i = $entity->docCommentLine; $i < $entity->startLine; $i++)
			{
				$doccoment .= $lines[ $i - 1 ] . "\n";
			}
		}

		$function = '';
		for ($i = $entity->startLine; $i <= $entity->endLine; $i++)
		{
			$function .= $lines[ $i - 1 ] . "\n";
		}

		return array(
			'doccoment' => $doccoment,
			'function' => $function,
			);
	}

	protected function renameMethod($method, $code)
	{
		return $code;
	}

	protected function declareMethod($entity, $source)
	{
		// get function declaration (including doccomment)
		//
		$declaration = $this->getFunctionDeclaration($entity, $source);

		// new method name ?
		//
		if ($entity->name != $entity->classMethod)
		{
			$declaration['function'] = $this->renameMethod(
				$entity->classMethod,
				$declaration['function']
				);
		}

		// get shrink class
		//
		$code = '';
		if ($source->exists($entity->classFile))
		{
			$code = $source->read($entity->classFile);
		} else
		{
			$code = Condense\Transform::wpClassFile(
				$entity->classNamespace,
				$entity->className
			);
		}
		$tokens = token_get_all($code);

		// find the end of the class where to insert it
		//
		$insertBefore = 0;
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
				$updated[] = "\n\n";
				$updated[] = Condense\Transform::tabify(
					$declaration['doccoment']
					);
				$updated[] = Condense\Transform::tabify(
					$declaration['function']
					);
				$updated[] = "\n";
			}

			$oken = is_scalar($token) ? $token : $token[1];
			$updated[] = $oken;
		}

		$code = join('', $updated);
		$source->write($entity->classFile, $code);

		$entity->docComment = $declaration['doccoment'];
		$entity->functionCode = $declaration['function'];
	}

	protected function removeOriginal()
	{

	}

	protected function addCompatiblity()
	{

	}

	protected function replaceCalls()
	{

	}
}
