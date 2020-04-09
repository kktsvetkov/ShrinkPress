<?php

namespace ShrinkPress\Build\Entity\Class;

interface Class_Entity
{
	function className();

	function load(array $data);
}
