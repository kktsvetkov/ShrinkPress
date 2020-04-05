<?php

namespace ShrinkPress\Build\Parse\Entity;

class WpInclude extends EntityAbstract
{
	public $includedFile = '';

	public $includedType = '';

	public $fromFolder = '';

	function __construct( $includedFile )
	{
		$this->includedFile = (string) $includedFile;
	}

	public $docCommentLine = 0;

}
