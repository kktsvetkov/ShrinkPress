<?php

namespace ShrinkPress\Reframe\Entity\Includes;

interface Include_Entity extends \JsonSerializable
{
	function includedFile();

	function load(array $data);
}
