<?php

namespace ShrinkPress\Build\Parse\Entity;

class WpGlobal extends EntityAbstract
{
	public $globalName = '';

	function __construct( $globalName )
	{
		$this->globalName = (string) $globalName;
	}
}
