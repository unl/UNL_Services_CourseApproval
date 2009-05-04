<?xml version="1.0" encoding="utf-8" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
  <title>Courses</title>
</head>

<body>

<?php
chdir(dirname(dirname(dirname(__FILE__))));

require_once 'UNL/Autoload.php';

if (!isset($_GET['subject'])) {
    echo 'Enter a subject code';
    exit();
}


$subject = new UNL_Services_CourseApproval_SubjectArea($_GET['subject']);

echo '<h1>There are '.count($subject->courses).' courses for '.$subject.'</h1>';

echo '<dl>';

foreach ($subject->courses as $course) {
    $listings = '';
    foreach ($course->codes as $listing) {
        $listings .= $listing->courseNumber.'/';
    }
    $listings = trim($listings, '/');
    
    $credits = '';
    if (isset($course->credits['Single Value'])) {
        $credits = $course->credits['Single Value'];
    }
    
    echo "<dt class='course'>
            <span class='number'>$listings</span>. <span class='title'>{$course->title}</span>
            <span class='credit'>($credits cr)</span>
          </dt>
          <dd>";
    if (!empty($course->prerequisite)) {
        echo "<p class='prereqs'>{$course->prerequisite}</p>";
    }
    echo "<p class='description'>{$course->description}</p>
          </dd>";
}
echo '</dl>';
echo '<div>';
highlight_file(__FILE__);
echo '</div>';
?>
</body>
</html>