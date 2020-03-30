<?php

namespace ShrinkPress\Build\Project\Entity;

/**
* The calls made to a WordPress function from another functions
*/
class WpCall implements WpEntity
{
	public $name;

	public $filename;
	public $line;

	public $callers = [];
}
