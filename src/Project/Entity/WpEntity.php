<?php

namespace ShrinkPress\Build\Project\Entity;

interface WpEntity
{
	function getData();

	function load(array $data);
}
