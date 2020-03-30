<?php

namespace ShrinkPress\Build\Condense;

use ShrinkPress\Build\Assist;
use ShrinkPress\Build\Project;

class Compat
{
	use Assist\Instance;

	const compatibility_php = Composer::vendors . '/shrinkpress/compatibility.php';

	const functions_json = Composer::vendors . '/shrinkpress/functions.json';

	protected $filesModified = [];

	function addFunction($args, Project\Entity\WpFunction $entity, Project\Source $source)
	{
		$replacement = Task\SortFunctions::$replace[ $entity->name ];
		$args = (string) $args;

		$call_args = Assist\Code::callArguments($args);

		$compat_func = "\n"
			. "function {$entity->name}{$args}"
			. "\n{"
			. "\n\treturn {$replacement}{$call_args};"
			. "\n}"
			. "\n";

		// pluggable function ?
		//
		$pluggable = (
			'wp-includes/pluggable.php' == $entity->fileOrigin ||
			'wp-includes/pluggable-deprecated.php' == $entity->fileOrigin
			);
		if ($pluggable)
		{
			$compat_func =
				"\nif ( ! function_exists( '{$entity->name}' ) ) :\n"
				. Transform::tabify( ltrim( $compat_func ) )
				. "endif;\n";
		}

		// append the compatibility function
		//
		$compat_php = $source->read(self::compatibility_php);
		$source->write(
			self::compatibility_php,
			$compat_php . $compat_func
			);

		// report a modified wordpress file, will be
		// inspected later for additional shrinking
		//
		if (empty($this->filesModified[ $entity->fileOrigin ]))
		{
			$this->filesModified[ $entity->fileOrigin ] = 0;
		}
		$this->filesModified[ $entity->fileOrigin ]++;
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
			json_encode(Task\SortFunctions::$replace, JSON_PRETTY_PRINT)
		);
	}
}
