<?php

namespace ShrinkPress\Build\Project\Entity;

/**
* The calls made to a WordPress function from fitler and\or action hooks
*/
class WpHook implements WpEntity
{
	public $name;

	public $filename;
	public $line;

	public $callers = [];
}
