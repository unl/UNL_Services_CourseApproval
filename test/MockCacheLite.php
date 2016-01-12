<?php

namespace UNLTest\Services\CourseApproval;

class MockCacheLite
{
	public function get($key, $group = '')
	{
		if ('creq_allcourses' === $key) {
			return file_get_contents(__DIR__ . '/data/all-courses.xml');
		}

		return false;
	}

	public function save($data, $key, $group = '')
	{
		return true;
	}

	public function clean($key)
	{
		return true;
	}

	public function remove($key)
	{
		return true;
	}

	public function setOption()
	{
	}
}
