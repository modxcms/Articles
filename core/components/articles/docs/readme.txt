-----------------------
Articles - 2.0.0-alpha1
-----------------------

Now compatible with MODX 3!

At the time of writing, MODX 3.0.0-pl has just been released and there has been a lot of refactoring work done to make Articles compatible. However, due to incompatible class keys and the way custom resources classes function (Articles makes use of these) in MODX, the developers have had to split Articles into two versions. 

Before upgrading to MODX 3 with Articles installed, you'll need to update Articles to at least version 1.8.0-pl. This will ensure an older incompatible version of Articles doesn't cause any errors during the upgrade process.

Once upgraded to MODX 3.0, articles won't yet be fully usable. Go to the package manager, and you'll see a new version of Articles (2.0.0-alpha1 at time of writing) will be available. Download and install this to start using Articles with MODX 3! 

Please bear in mind that it's currently an Alpha version, and be sure to report any issues you encounter on Github: https://github.com/modxcms/Articles


Original Version
-----------------------
First Released: November 29th, 2011
Author: Shaun McCormick <shaun+articles@modx.com>
License: GNU GPLv2 (or later at your option)

This component is a Custom Resource Class for MODX 2.2+. It allows you to easily create Article containers (such as
a Blog, News section, or Events section) on your site, complete with a custom UI for managing them and Articles.
It automatically handles URL archiving, RSS feeds, comments, tags and more.

Please read the official documentation at:
https://docs.modx.org/current/en/extras/articles

Thanks for using Articles!
Shaun McCormick
shaun+articles@modx.com