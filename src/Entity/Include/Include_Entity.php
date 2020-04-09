<?php

namespace ShrinkPress\Build\Entity\Class;

interface Include_Entity extends \JsonSerializable
{
	function filename();

	function load(array $data);
}
