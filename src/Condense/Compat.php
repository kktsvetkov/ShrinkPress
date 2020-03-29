<?php

namespace ShrinkPress\Build\Condense;

use ShrinkPress\Build\Assist;
use ShrinkPress\Build\Project;

class Compat
{
	use Assist\Instance;

	const compatibility_php = Composer::vendors . '/shrinkpress/compatibility.php';

	const functions_json = Composer::vendors . '/shrinkpress/functions.json';
	protected $functions = [];

	protected $filesModified = [];

	function addFunction(Project\Entity\WpFunction $entity, Project\Source $source)
	{
		$replacement = '\\' . $entity->classNamespace . $entity->className
			. '::' . $entity->classMethod;

		// extract arguments
		//
		$args = '';
		$tokens = token_get_all( '<?php ' . $entity->functionCode);
		$seek = array( 'function', $entity->name, '(');
		$last = array();
		$startAt = null;
		foreach ($tokens as $i => $token)
		{
			// skip T_WHITESPACE
			//
			if (382 == $token[0])
			{
				continue;
			}

			$oken = is_scalar($token) ? $token : $token[1];

			array_push($last, $oken);
			if (count($last) > count($seek))
			{
				array_shift($last);
			}

			if ($seek == $last)
			{
				$startAt = $i++;
			}

			if ('{' == $oken)
			{
				break;
			}

			if (!is_null($startAt) && $i > $startAt)
			{
				$args .= $oken;
			}
		}

		// append the compatibility function
		//
		$compat_php = $source->read(self::compatibility_php);
		$compat_php .= "\n"
			. "function {$entity->name}{$args}"
			. "\n{"
			. "\n\treturn {$replacement}{$args};"
			. "\n}"
			. "\n";
		$source->write(self::compatibility_php, $compat_php);

		// report a modified wordpress file
		//
		if (empty($this->filesModified[ $entity->fileOrigin ]))
		{
			$this->filesModified[ $entity->fileOrigin ] = 0;
		}
		$this->filesModified[ $entity->fileOrigin ]++;

		// keep a list of modified files
		//
		$this->functions[ $entity->name ] = $replacement;
	}

	function addClass()
	{

	}

	function addGlobals()
	{

	}

	function head()
	{
		return '<?php '
			. "\n /** @see shrinkpress */"
			. "\n";
	}

	function dump(Project\Source $source)
	{
		$source->write(
			self::functions_json,
			json_encode($this->functions, JSON_PRETTY_PRINT)
		);
	}
}
