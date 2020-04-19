<?php

namespace ShrinkPress\Reframe\Entity\Globals;

interface Global_Entity extends \JsonSerializable
{
	function globalName();

	function load(array $data);
}
