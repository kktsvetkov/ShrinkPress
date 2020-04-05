<?php

namespace ShrinkPress\Build\Parse\Entity;

class WpGlobal extends EntityAbstract
{
	public $globalName = '';

	const TYPE_ARRAY = 'array';
	const TYPE_KEYWORD = 'keyword';

	public $globalType = '';

	function __construct( $globalName )
	{
		$this->globalName = (string) $globalName;
	}
}
