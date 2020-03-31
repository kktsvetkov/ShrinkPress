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
		$composer = Condense\Composer::instance();

SortFunctions::$replace = array(
	'wp_die' => SortFunctions::$replace['wp_die'],
	'wp_redirect' => SortFunctions::$replace['wp_redirect'],
	'absint' => SortFunctions::$replace['absint'],
	'apply_filters' => SortFunctions::$replace['apply_filters'],
	'wp_doing_ajax' => SortFunctions::$replace['wp_doing_ajax'],
);

		foreach (SortFunctions::$replace as $name => $replacement)
		{
			$entity = $storage->readFunction($name);
			Verbose::log("Replace: {$name}() with {$replacement}()", 1);

			// stupid hack so that we can run this
			// without callling FunctionsMap
			//
			list($composerNamespace, $composerFolder) =
				Condense\Transform::wpNamespace(
					str_replace('ShrinkPress\\', '', $entity->classNamespace)
				);
			$composer->addPsr4(
				$composerNamespace,
				$composerFolder
				);

			// extract function (and its doccomment, if any)
			// from original file, and replace them with blank lines
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
			if (!empty(SortFunctions::$map[ $name ]))
			{
				$function = $this->replaceCalls(
					$function,
					SortFunctions::$map[ $name ],
					$entity->startLine
				);
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
		}

		// save the replacement map inside for anyone
		// who might want to use to convert more code
		//
		$source->write(
			$compat::functions_json,
			json_encode(SortFunctions::$replace, JSON_PRETTY_PRINT)
		);

		// stupid hack so that we can run this
		// without callling FunctionsMap
		//
		$source->write('composer.json', $composer->json() );
		$composer->dumpautoload( $source->basedir() );
	}

	protected function replaceCalls($code, array $calls, $offset )
	{
		$found = array();
$lines = [-$offset => -1];
		$tokens = token_get_all('<?php ' . $code);
		array_shift($tokens);

		$code = '';
		foreach ($tokens as $i => $token)
		{
			$oken = is_scalar($token) ? $token : $token[1];

			if (is_scalar($token))
			{
				$code .= $oken;
				continue;
			}

			// not our line of code ?
			//
			$line = $offset + $token[2] - 1;
$lines[$line] = 1;
			if (empty($calls[ $line ]))
			{
				$code .= $oken;
				continue;
			}

			if ("''" == $oken)
			{
				$code .= $oken;
				continue;
			}

			if (!in_array($token[0], array(323, 319)))
			{
				$code .= $oken;
				continue;
			}

			$seek = (319 == $token[0])
				? $oken
				: trim($oken, "'");

			// not something we are looking at this line
			//
			if (!in_array($seek, $calls[ $line ]))
			{
				$code .= $oken;
				continue;
			}

			// just in case, make sure there is a replacement
			//
			if (empty(SortFunctions::$replace[ $seek ]))
			{
				$code .= $oken;
				continue;
			}

			$replacement = SortFunctions::$replace[ $seek ];
			if (323 == $token[0])
			{
				$replacement = "'"
					. str_replace('\\', '\\\\', $replacement)
					. "'";
			}

			$code .= $replacement;

			// mark as found
			//
			$found[ $line ][] = $seek;
		}

print_r($calls);
print_r($found);
print_r(array_keys($lines));

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
