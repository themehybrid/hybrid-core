# Contributing

The code for the framework is handled via its [GitHub Repository](https://github.com/justintadlock/hybrid-core).  You can open tickets, create patches, and send pull requests there.

## Pull requests

Problem first.  Solution second.

Pull requests should have a ticket open for discussion first.  I rarely accept pull requests that aren't for a specific issue for various reasons.  It's far better to post an issue and let me or the community provide feedback prior to creating a pull request.

Please don't make pull requests against the `master` branch.  This is the latest, stable code.  You can make a pull request against one of the point branches or the `dev` (future release) branch.

## Theme review guidelines

Hybrid Core is developed with the WordPress.org [theme review guidelines](https://make.wordpress.org/themes/handbook/review) in mind.  While the framework is not a theme in and of itself, many theme authors use it within their themes on the official theme repository.  These guidelines should sufficiently cover any other marketplace or theme shop rules for submission.  All new features and code changes must abide by these guidelines to make it into the project.

## Coding standards

In general, Hybrid Core follows all WordPress [coding standards](https://make.wordpress.org/core/handbook/best-practices/coding-standards).  There are instances where it doesn't, opting for personal choices of my own, but in terms of contributing, following the WordPress standards is best practice.

## Script and style files

The framework consists of several script and style files.  When making patches or pull requests with changes to these files, only do so to the primary file.  Don't create patches for the minified (`.min`) versions of the files.  Those will be minified after a patch is merged into the code base.

## Language

All text strings follow U.S. English by default.  While such guides are generally unneeded, in cases where style considerations are necessary, these will typically follow conventions laid out in *Elements of Style* or the *AP Stylebook*.

## Licensing

Any code contributed to Hybrid Core via patches, pull requests, or other means will be licensed under the [GPL version 2](http://www.gnu.org/licenses/old-licenses/gpl-2.0.html) or later.  By contributing code to the project, you provide consent to use such code under this license.  The exception to this rule is when bringing in third-party code with an alternate open source license.

## Versioning

Hybrid Core uses [semantic versioning](http://semver.org).  Version numbers will look like `3.2.1` where `3` is the "major" release, `2` is the minor release, and `1` is the patch release.