<?php

namespace ShrinkPress\Build\Entity\Funcs;

interface Function_Entity extends \JsonSerializable
{
	function functionName();

	function load(array $data);
}
