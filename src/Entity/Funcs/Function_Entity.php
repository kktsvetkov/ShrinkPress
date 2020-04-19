<?php

namespace ShrinkPress\Reframe\Entity\Funcs;

interface Function_Entity extends \JsonSerializable
{
	function functionName();

	function load(array $data);
}
