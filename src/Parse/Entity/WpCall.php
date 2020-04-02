<?php

namespace ShrinkPress\Build\Parse\Entity;

/**
* The calls made to a WordPress function from another functions
*/
class WpCall extends EntityAbstract
{
	public $functionName = '';

	function __construct( $functionName )
	{
		$this->functionName = (string) $functionName;
	}
}
