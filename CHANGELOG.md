# Course Approval Service Changelog

## 1.0.2

* Restore backwards-compatible properties to the SubjectArea object
* Use lazy-loading on SubjectArea getters

## 1.0.1

* Fix Course object should not set default XPath namespace if it is not registered

## 1.0.0

Namespaced API

* General API improvements for maintainability, speed.

## 0.5.1

Bug Fix:

* PHP 5.3 compatibility

## 0.5.0

Add support for multiple search facets and auto-faceting of queries.

Examples:

* Ace 3 honors MATH

## 0.4.0

Feature Release!

New tools for filtering out courses

Features:

* Add support for searching by course number suffix. E.g. 41 for 141, 241, 341

## 0.3.4

Bugfix:

* Correct variable reference for course activity types

## 0.3.3

Add methods to retrieve possible values and descriptions for course data:

* UNL_Services_CourseApproval_Course::getPossibleActivities()
* UNL_Services_CourseApproval_Course::getPossibleAceOutcomes()
* UNL_Services_CourseApproval_Course::getPossibleCampuses()
* UNL_Services_CourseApproval_Course::getPossibleDeliveryMethods()
* UNL_Services_CourseApproval_Course::getPossibleTermsOffered()

## 0.3.2

Convert method signatures for compatibility with LimitIterator

## 0.3.1

Performance improvement and new method signature.

Finding subsequent courses now supports passing a search driver.
```
getSubsequentCourses(UNL_Services_CourseApproval_SearchInterface $driver = null)
```

## 0.3.0

Feature Release!

Now that graduate courses are in CREQ, some new tools for filtering out courses
and various bug fixes.

Features:

* Add filters for graduate and undergraduate courses
* Add support for searching for graduate and undergraduate courses
* Add support for retrieving the intersection of two queries
* Support strings of '*' or 'x' when searching for courses by number

Bug fixes:

* Search::byTitle() method missing
* Searching for 497* returns no results [#1]
* Add in missing + sign, supporting 3XX number prefix queries


## 0.2.0

Feature Release:

 * Courses now support getSubsequentCourses() (reverse-prereqs)
 * Searching by prerequisite is required for all search drivers
 * Add XPATH prerequisite search support

## 0.1.0

First API release
