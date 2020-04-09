<?php

namespace ShrinkPress\Build\Entity\Class;

interface Global_Entity extends \JsonSerializable
{
	function globalName();

	function load(array $data);
}
