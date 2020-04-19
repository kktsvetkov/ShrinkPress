<?php

namespace ShrinkPress\Reframe\Entity\Packages;

interface Package_Entity extends \JsonSerializable
{
	function packageName();

	function load(array $data);
}
