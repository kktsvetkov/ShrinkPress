<?php

namespace ShrinkPress\Build\Parse\Entity;

/**
* The calls made to a WordPress function from fitler\action hooks
*/
class WpCallback extends WpCall
{
	public $functionName = '';

	function __construct( $functionName )
	{
		$this->functionName = (string) $functionName;
	}

	public $hookName = '';

	public $hookFunction = '';
}
