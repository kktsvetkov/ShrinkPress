<?php

namespace ShrinkPress\Build;

interface WpEntity
{
	function getData();

	function load(array $data);
}
