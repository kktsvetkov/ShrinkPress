<?php

namespace ShrinkPress\Build\Entity\Includes;

interface Include_Entity extends \JsonSerializable
{
	function includedFile();

	function load(array $data);
}
