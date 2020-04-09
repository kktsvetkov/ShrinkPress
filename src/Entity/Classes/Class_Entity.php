<?php

namespace ShrinkPress\Build\Entity\Classes;

interface Class_Entity extends \JsonSerializable
{
	function className();

	function load(array $data);
}
