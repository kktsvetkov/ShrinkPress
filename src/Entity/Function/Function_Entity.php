<?php

namespace ShrinkPress\Build\Entity\Class;

interface Function_Entity extends \JsonSerializable
{
	function functionName();

	function load(array $data);
}
