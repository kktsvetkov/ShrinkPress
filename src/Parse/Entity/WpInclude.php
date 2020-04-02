<?php

namespace ShrinkPress\Build\Parse\Entity;

class WpInclude extends EntityAbstract
{
	public $includedFile = '';

	function __construct( $includedFile )
	{
		$this->includedFile = (string) $includedFile;
	}

	public $docCommentLine = 0;

}
