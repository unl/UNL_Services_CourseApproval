--TEST--
Sample Test
--FILE--
<?php
require_once 'test_framework.php';
$listing = new UNL_Services_CourseApproval_Listing('ACCT', 201);

$test->assertEquals('ACCT', $listing->subjectArea, 'Subject area');
$test->assertEquals(201, $listing->courseNumber, 'Course number');
$test->assertEquals('Introductory Accounting I', $listing->title, 'Course title');
$test->assertEquals('Fundamentals of accounting, reporting, and analysis to understand financial, managerial, and business concepts and practices. Provides foundation for advanced courses.', $listing->description, 'Course description');
$test->assertEquals('Math 104 with a grade of \'C\' or better;  14 cr hrs at UNL with a 2.5 GPA.', $listing->prerequisite, 'Prerequisite');

$listing = new UNL_Services_CourseApproval_Listing('ACCT', '201H');

$test->assertEquals('ACCT', $listing->subjectArea, 'Subject area');
$test->assertEquals('201H', $listing->courseNumber, 'Course number');
$test->assertEquals('Honors: Introductory Accounting I', $listing->title, 'Course title');

?>
===DONE===
--EXPECT--
===DONE===