<?php

namespace ShrinkPress\Build;

interface WpEntity
{
	function getFile();

	function getData();

	function load(array $data);
}
