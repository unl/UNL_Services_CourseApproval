--TEST--
Search test
--FILE--
<?php
require_once 'test_framework.php';
$search = new UNL_Services_CourseApproval_Search();

$courses = $search->byNumber('201');
$test->assertEquals(2, count($courses), 'Two results returned');

$courses = $search->byTitle('Accounting');
$test->assertEquals(2, count($courses), 'Two results returned');

foreach ($courses as $course) {
    $test->assertNotFalse(
        strpos($course->title, 'Accounting'),
        'Course title contains the word Accounting'
    );
}


?>
===DONE===
--EXPECT--
===DONE===