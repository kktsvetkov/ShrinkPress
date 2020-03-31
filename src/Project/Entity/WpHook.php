<?php

namespace ShrinkPress\Build\Project\Entity;

/**
* The calls made to a WordPress function from fitler and\or action hooks
*/
class WpHook extends WpEntity
{
	public $filename;
	public $line;

	public $callers = [];
}
