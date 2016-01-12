<?php

namespace UNL\Services\CourseApproval\Search;

interface SearchInterface
{
    public function aceQuery($ace = null);
    public function subjectAndNumberQuery($subject, $number, $letter = null);
    public function subjectAndNumberPrefixQuery($subject, $number);
    public function subjectAndNumberSuffixQuery($subject, $number);
    public function numberPrefixQuery($number);
    public function numberSuffixQuery($number);
    public function honorsQuery();
    public function titleQuery($title);
    public function subjectAreaQuery($subject);
    public function numberQuery($number, $letter = null);
    public function creditQuery($credits);
    public function prerequisiteQuery($prereq);
    public function intersectQuery($query1, $query2);
    public function graduateQuery();
    public function undergraduateQuery();
    public function filterQuery($query);
    public function byTitle($query, $offset = 0, $limit = -1);
    public function byNumber($query, $offset = 0, $limit = -1);
    public function bySubject($query, $offset = 0, $limit = -1);
    public function byPrerequisite($query, $offset = 0, $limit = -1);
    public function graduateCourses($offset = 0, $limit = -1);
    public function undergraduateCourses($offset = 0, $limit = -1);
    public function byMany($queries = array(), $offset = 0, $limit = -1);
    public function determineQuery($query);
    public function byAny($query, $offset = 0, $limit = -1);
    public function getQueryResult($query, $offset = 0, $limit = -1);
}
