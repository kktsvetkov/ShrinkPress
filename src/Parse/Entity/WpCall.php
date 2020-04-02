<?php

namespace ShrinkPress\Build\Parse\Entity;

/**
* The calls made to a WordPress function from another functions
*/
class WpCall extends EntityAbstract
{
	public $name;

	public $callers = [];
}
