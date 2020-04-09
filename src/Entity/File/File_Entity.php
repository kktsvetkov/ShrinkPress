<?php

namespace ShrinkPress\Build\Entity\File;

interface File_Entity extends \JsonSerializable
{
	function filename();

	function load(array $data);
}
