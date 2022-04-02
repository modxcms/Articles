# Articles 

This is the official repo for the MODX blogging extra Articles. 

## Introduction

Articles is a MODX Extra that incorporates a combo of other packages geared towards blog/article writing in MODX. It manages your posts and comments in custom grids, handles archiving and tagging all within a unified interface.

Articles is made up of the following packages:

- Quip
- TagLister
- Archivist
- getResources
- getPage

When installing Articles, the package manager will attempt to also download and install these for you if not already present.

## MODX 3.x Compatibility - Alpha Version

At the time of writing, MODX 3.0.0-pl has just been released and there has been a lot of refactoring work done to make Articles compatible. However, due to incompatible class keys and the way custom resources classes function (Articles makes use of these) in MODX, the developers have had to split Articles into two versions. 

Before upgrading to MODX 3 with Articles installed, you'll need to update Articles to at least version 1.8.0-pl. This will ensure an older incompatible version of Articles doesn't cause any errors during the upgrade process.

Once upgraded to MODX 3.0, articles won't yet be fully usable. Go to the package manager, and you'll see a new version of Articles (2.0.0-alpha1 at time of writing) will be available. Download and install this to start using Articles with MODX 3! 

Please bear in mind that it's currently an Alpha version, and be sure to report any issues you encounter on Github: [https://github.com/modxcms/Articles](https://github.com/modxcms/Articles)

 * The documentation can be found at [MODX Docs](https://docs.modx.org/current/en/extras/articles).
 * Help can be found at the [MODX Community](https://community.modx.com/c/support/extras)

## Slack
Join the conversation in our [public Slack workspace](https://modx.org).
