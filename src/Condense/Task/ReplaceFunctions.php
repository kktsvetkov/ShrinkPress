<?php

namespace ShrinkPress\Build\Condense\Task;

use ShrinkPress\Build\Assist;
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

SortFunctions::$map = array(
	'wp_die' => SortFunctions::$map['wp_die'],
	'wp_redirect' => SortFunctions::$map['wp_redirect'],
	'absint' => SortFunctions::$map['absint'],
);

		foreach (SortFunctions::$map as $name => $calls)
		{
			$entity = $storage->readFunction($name);
			if (!$entity->fileOrigin)
			{
				Verbose::log("No file: {$entity->name}()", 3);
				continue;
			}

			Verbose::log("Replace: {$entity->name}()", 1);

			// extract function (and its doccomment, if any)
			// from original file
			//
			$code = $source->read( $entity->fileOrigin );
			$lines = new Assist\FileLines($code);

			$doccoment = '';
			if ($entity->docCommentLine)
			{
				$doccoment = $lines->extract(
					$entity->docCommentLine,
					$entity->startLine
				);
			}

			$function = $lines->extract(
				$entity->startLine,
				$entity->endLine + 1
				);
			$source->write($entity->fileOrigin, $lines);

			// leave the original function name for compatibility,
			// and do this first before there are any changes
			// to the code of the function 
			//
			$args = Assist\Code::arguments($function, $entity->name);
			$compat->addFunction($args, $entity, $source);

			// replace calls to other functions inside
			//
			if ( $calls )
			{
				$function = $this->replaceCalls($calls, $function);
			}

			// new method name ?
			//
			if ($entity->name != $entity->classMethod)
			{
				$function = Assist\Code::renameMethod(
					$entity->name,
					$entity->classMethod,
					$function
					);
			}

			$this->declareMethod(
				$doccoment . $function,
				$entity,
				$source);
// BREAK;
		}

		// save the replacement map inside for anyone
		// who might want to use to convert more code
		//
		$source->write(
			$compat::functions_json,
			json_encode(SortFunctions::$replace, JSON_PRETTY_PRINT)
		);
	}

	protected function replaceCalls(array $calls, $code)
	{
		print_r($calls);
		return $code;
	}

	protected function declareMethod($declaration, $entity, $source)
	{
		// get new shrinkpress class code
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
					$declaration
					);
			}

			$oken = is_scalar($token) ? $token : $token[1];
			$updated[] = $oken;
		}

		$code = join('', $updated);
		$source->write($entity->classFile, $code);
	}
}
