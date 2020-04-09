<?php

namespace ShrinkPress\Build\Entity\File;

interface File_Entity
{
	function filename();

	function load(array $data);
}
