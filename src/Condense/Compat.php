<?php

namespace ShrinkPress\Build\Condense;

use ShrinkPress\Build\Project;

class Compat
{
	use Instance;

	const compatibility_php = Composer::vendors . '/shrinkpress/compatibility.php';

	function addFunction(Project\Entity\WpFunction $entity)
	{

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
}
