<?php

namespace ShrinkPress\Build\Entity\Class;

interface Include_Entity
{
	function filename();

	function load(array $data);
}
