--TEST--
Sample Test
--FILE--
<?php
require_once 'test_framework.php';
$listing = new UNL_Services_CourseApproval_Listing('ACCT', 201);

$test->assertEquals('ACCT', $listing->subjectArea, 'Subject area');
$test->assertEquals(201, $listing->courseNumber, 'Course number');
$test->assertEquals('Introductory Accounting I', $listing->title, 'Course title');

$listing = new UNL_Services_CourseApproval_Listing('ACCT', '201H');

$test->assertEquals('ACCT', $listing->subjectArea, 'Subject area');
$test->assertEquals('201H', $listing->courseNumber, 'Course number');
$test->assertEquals('Honors: Introductory Accounting I', $listing->title, 'Course title');

?>
===DONE===
--EXPECT--
===DONE===