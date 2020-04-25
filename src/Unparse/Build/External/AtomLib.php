<?php

namespace ShrinkPress\Reframe\Unparse\Build\External;

use ShrinkPress\Reframe\Unparse;
use ShrinkPress\Reframe\Index;

use ShrinkPress\Reframe\Unparse\Build\External as ExternalTask;

class AtomLib implements Unparse\Build\Task
{
	function build(Unparse\Source $source, Index\Index_Abstract $index )
	{
		ExternalTask::movePackage($source, $index,
			'AtomLib', 'ShrinkPress\\Atom', 'atom');
	}
}
