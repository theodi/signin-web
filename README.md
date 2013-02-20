signin-web
==========

This repository contains the web-based portion of the ODI office signin
system. 

In time, the system will evolve and become more complete, and the resulting 
data will be made available as Open Data.

Usage
-----

To deploy master to the live server:

    bundle
    cap deploy

This will automatically set up the database connection details, etc, and update the cached staff list from the content on the main website.

If you want to update the staff list manually without a code deploy, run:

    cap staff:update

License
-------

This code is open source under the MIT license. See the LICENSE.md file for 
full details.

Authors
-------

Dave Tarrant <davetaz@theodi.org>
James Smith <james.smith@theodi.org>