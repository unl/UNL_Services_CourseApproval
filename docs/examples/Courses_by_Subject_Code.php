<?php 
chdir(dirname(dirname(dirname(__FILE__))));

require_once 'UNL/Autoload.php';

$page = UNL_Templates::factory('Fixed');

$page->addStyleDeclaration('
.course .number {font-size:2.5em;padding:7px 2px;margin:0 5px 0 0;background-color:#E7F0F9;display:block; float:left;font-weight:bold;}
.course .title {font-size:1.5em; display:block; border-bottom:1px solid #C8C8C8;font-style:normal;font-weight:bold;}
.course .prereqs {color:#0F900A;font-weight:bold;margin:3px 0;}
.course .notes {font-style:italic;margin:3px 0;}
.course .details {float:right;width:220px;}
.course .description {border-left:3px solid #C8C8C8;padding-left:5px;float:left;width:440px;}
dd {margin:0 0 3em 0;padding:0;}
dt {padding:3em 0 0 0 !important;}
.course {clear:both;}
');

$page->titlegraphic = '<h1>Undergraduate Bulletin</h1>
                       <h2>Your Academic Guide</h2>';
$page->navlinks = '
<ul>
    <li>Academic Policies</li>
    <li>Achievement-Centered Education (ACE)</li>
    <li>Academic Colleges</li>
    <li>Areas of Study</li>
    <li>Courses</li>
</ul>
';
$page->leftRandomPromo = '';
$page->maincontentarea = '';
if (!isset($_GET['subject'])) {
    echo 'Enter a subject code';
    exit();
}


$subject = new UNL_Services_CourseApproval_SubjectArea($_GET['subject']);

$page->maincontentarea .= '<h1>There are '.count($subject->courses).' courses for '.$subject.'</h1>';

$page->maincontentarea .=  '<dl>';

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
    
    $page->maincontentarea .= "
        <dt class='course'>
            <span class='number'>$listings</span>
            <span class='title'>{$course->title}</span>
        </dt>
        <dd class='course'>";
    
        $prereqs = @$course->prerequisite;
        if (!empty($prereqs)) {
            $page->maincontentarea .= "<p class='prereqs'>Prereqs: {$prereqs}</p>";
        }
        $notes = @$course->notes;
        if (!empty($notes)) {
            $page->maincontentarea .= "<p class='notes'>{$notes}</p>";
        }
        $page->maincontentarea .= "<p class='description'>{$course->description}</p>";
        $page->maincontentarea .= '<table class="details">';
        $page->maincontentarea .= '<tr class="credits">
                                    <td>Credit Hours:</td>
                                    <td>'.$credits.'</td>
                                    </tr>';
        $page->maincontentarea .= '<tr class="format">
                                    <td>Course Format:</td>
                                    <td></td>
                                    </tr>';
        $page->maincontentarea .= '<tr class="campus">
                                    <td>Campus:</td>
                                    <td>'.implode(', ', $course->campuses).'</td>
                                    </tr>';
        $page->maincontentarea .= '<tr class="termsOffered">
                                    <td>Terms Offered:</td>
                                    <td>'.implode(', ', $course->termsOffered).'</td>
                                    </tr>';
        $page->maincontentarea .= '<tr class="deliveryMethods">
                                    <td>Course Delivery:</td>
                                    <td>'.implode(', ', $course->deliveryMethods).'</td>
                                    </tr>';
        $ace = @$course->aceOutcomes;
        $page->maincontentarea .= '<tr class="aceOutcomes">
                                    <td>ACE Outcomes:</td>
                                    <td>'.implode(', ', $ace).'</td>
                                    </tr>';
        $page->maincontentarea .= '</table>';
    $page->maincontentarea .= "</dd>";
}
$page->maincontentarea .= '</dl>';

echo $page;