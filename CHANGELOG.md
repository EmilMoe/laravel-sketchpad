# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).


## [1.1.8] - 2017-05-05

### Added

- Implemented support for `@field` tags
- Implemented controller reordering
- Implemented support for implicit pagination
- New `text()` and `code()` helper functions
- Data injection for `md()` function
- Static `Sketchpad::$form` and `Sketchpad::$data` properties
- Automatic injection of `$route` variable into views
- Included app controllers folder in paths
- Custom page title functionality
- Added custom Help page
- Implemented live-reloading for custom pages
- Added scroll to section functionality for Settings
- Added index pages for all help controllers
- Bootstrap formatting for Markdown tables
- Added changelog

### Changed

- Huge docs and examples update!
  - Created new Sketchpad docs sections
  - Updated a lot of help to be better-formatted and more consistent
  - Improved example controller and view content
- Moved various settings to single "Site" section
- New Param sub-components for different data types
- Disabled Settings page now shows a message
- Index pages can be re-run
- Added formatting classes to `pr()` and `vd()`
- Updated FontAwesome to latest version
- Improved submission of form data
- Improved formatting of tables and forms
- Simplified copying of user views in setup

### Fixed

- Defended against showing parent paths in Browse filesystem
- Defended against saving settings if not allowed in admin.json
- Fixed edge-case with buggy internal routes
- Fixed bug with method titles showing parameters
- Fixed Markdown not showing code or blockquotes correctly
- Fixed bug where coloured @icon icons not showing
- Fixed scroll to top animation
- Fixed bug with intercepted link currentTarget
- Fixed URL encoding bug with route parameters
- Fixed bug in NumberParam where unset variables don't show 0
- Fixed bug where Output only styles first markdown table
- Fixed bug in internal href links not working for links with hashes
- Fixed bug with validate-path directive causing settings to be reloaded on focus
- Removed absolute path references from RouteReference classes
- Updated demo link in readme

### Upgrade notes

New Settings `site` section:

- Copy `site` node from `vendor/davestewart/sketchpad/package/config/admin.json`


## [1.1.0] - 2017-04-21

### Added

- Test mode functionality 
- Clientside script to auto-resize iframe
- User can now edit and show custom home page
- Basic admin permissions to prevent showing of Setup and Settings pages

### Changed

- Moved routes to separate file
- Example Browse Filesystem tool now only browses from site root downwards
- Refactored `publish/` folder to be `package/`
- Moved `resources/views` to `package/views`
- Moved `$config` references for Example controller / view to method

### Fixed

- Controller labels not being humanized
- Bug with new controllers not being added
- Layout fix when moving between controller indexes and methods
- Prevented spellcheck on settings fields
- Setup edge-case bugs for custom setups which required two install attempts
- URLs now load properly into iframes
- Better iframe error handling
- Page titles now show for non-console routes
- `tb()` and `ls()` now display booleans correctly
- `vd()` now displays preformatted text, also added better formatting


## [1.0.0] - 2017-04-12

### Added

- Scroll to top when changing methods
- Pressing enter now submits method changes
- Added icon parameter to `alert()` helper
- Parameter values now stay the same when reloading
- Shorthand `sketchpad:link` format for in-page links
- Shorthand `$assets/` URL reference for user assets URLs

### Changed

- Search page starts with no results; type to filter
- Nicer console transitions
- Various UI updates
- Improved resilience of watcher / store / state updates for controllers / methods
- Major refactor of router > console
- Better error handling / feedback on session timeouts
- Simplified return of different data formats by using container div rather than headers
- Improved VueJS data injection functionality; now uses `$data` variable
- Removed BrowserSync watching in favour of LiveReload

### Fixed

- Minor bugs in table rendering
- Bug with JSON data return < Laravel 5.2
- Bug with wrong active path showing for paths with similar names
- Various inconsistencies with controller reloading / missing routes
- Various installer bugs / optmiisations


## [1.0.0-beta] - 2017-03-24

- check commit log :)


## [0.3.0] - 2016-05-12

- check commit log :)


## [0.2.0] - 2016-04-30

- check commit log :)


## [0.1.0] - 2016-04-27

- check commit log :)

## [0.0.0] - 2016-04-19

- check commit log :)

