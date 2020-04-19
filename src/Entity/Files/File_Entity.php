<?php

namespace ShrinkPress\Reframe\Entity\Files;

interface File_Entity extends \JsonSerializable
{
	function filename();

	function load(array $data);
}
