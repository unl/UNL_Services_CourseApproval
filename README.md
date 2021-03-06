[![Build Status](https://travis-ci.org/unl/UNL_Services_CourseApproval.svg)](https://travis-ci.org/unl/phpunltemplates)
[![Coverage Status](https://coveralls.io/repos/unl/UNL_Services_CourseApproval/badge.svg?branch=master&service=github)](https://coveralls.io/github/unl/UNL_Services_CourseApproval?branch=master)

Client API for the curriculum request system at creq.unl.edu

This project provides a simple API for the course data within the creq system built by Tim Steiner.

This project optionally uses `Cache_Lite` for caching data from the creq system.

Currently data is cached on the local system in `/tmp/cache_*` files and stored for one week.

See the `docs/examples/` directory for examples.

For information on the XML format, see the XSD at http://courseapproval.unl.edu/schema/courses.xsd
