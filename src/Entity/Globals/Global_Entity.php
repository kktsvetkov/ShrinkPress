<?php

namespace ShrinkPress\Build\Entity\Globals;

interface Global_Entity extends \JsonSerializable
{
	function globalName();

	function load(array $data);
}
