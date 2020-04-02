<?php

namespace ShrinkPress\Build\Parse\Entity;

/**
* The calls made to a WordPress function from fitler\action hooks
*/
class WpHook extends WpCall
{
	public $hook = '';

}
