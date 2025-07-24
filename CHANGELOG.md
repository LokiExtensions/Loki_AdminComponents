# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.2.1] - 24 July 2025
### Fixed
- Change workflow condition
- Add GitHub Actions

## [0.2.0] - 21 July 2025
### Fixed
- Rename PHP namespace from `Yireo_Loki*` to `Loki*`
- Rename composer package from `yireo/magento2-loki*` to `loki/magento2*`

## [0.1.1] - 11 July 2025
### Fixed
- Allow for item convertors in forms
- Configure HTML attributes per field
- Enhance exceptions when you are being stupid
- Remove allowActions from provider

## [0.1.0] - 10 July 2025
### Refactor
- Rename from `Yireo_LokiAdminComponents` to `Loki_AdminComponents`
- Rename from `yireo/magento2-loki-admin-components` to `loki/magento2-admin-components`

## [0.0.10] - 18 June 2025
### Fixed
- Merge fields detected with info from XML layout
- Add Datetime field-type
- Fix issue when filters are not saved to grid yet
- Allow custom cell templates via block-name
- Allow for static grid filters
- Add component groups `loki_admin.grid` and `loki_admin.form`
- Read `row_actions` from XML layout
- Additional field types (date, number, view)
- Refactor field detection
- Add createItem to provider handlers
- Add LESS file
- Fix saveAndClose action again by redirecting to index page
- Exception when no factory class is set in form
- Properly set indexUrl after form AJAX-reload
- Add current URI to component params
- Throw friendly error if block is not component yet
- Add buttons for filters
- Add recursive options for select options
- Fix z-index with limit box
- Move JS code into partials
- Visual mock for filters
- Fix redirect after mass action
- Working demo of mass actions with products
- Mass actions

## [0.0.9] - 17 May 2025
### Fixed
- First bits on filters
- Allow sorting of repositories, collections and arrays
- Support for buttons + subbuttons
- Support for buttons with redirect
- Allow setting `edit_url` via XML layout

## [0.0.8] - 16 May 2025
### Fixed
- Configure `allow_actions` via XML layout
- Allow configuring URLs via XML layout
- Hide any actions from array grid

## [0.0.7] - 16 May 2025
### Fixed
- Disallow actions with arrays
- Fix new ProviderHandlerListing for forms as well

## [0.0.6] - 16 May 2025
### Fixed
- Do not try to print an object
- Add instruction to enable `Yireo_LokiComponents`
- WIP on autodetecting searchable fields
- Remove tut and move to wiki

## [0.0.5] - 16 May 2025
### Fixed
- Make state methods get and save public
- Make sure each grid uses namespaced session values
- Refactor provider handler resolving

## [0.0.4] - 16 May 2025
### Fixed
- Add support for grid and collection providers
- Hide search when there are no searchable fields
- Add samples
- Automatically create fields from entity
- Refactor form actions into reusable buttons
- Detect provider handler type automatically
- Throw error if no identifier when loading item

## [0.0.3] - 13 May 2025
### Fixed
- Move non-public methods to bottom
- Fetch searchable fields from resource model if available
- Update MODULE.json
- Fix PHPStan issue because wrong type hint in core
- Update license

## [0.0.2] - 07 May 2025
### Added
- Basic implementation of array and collection handler
- Allow adding provider via XML layout as object
- Determine columns by default from first item being loaded
- Finish inline edit
- Save reposition to bookmark
- Make grid namespace configurable
- Allow for repositioning columns properly
- Refactor grid actions into DI-configurable actions
- Rename actions folder to action-buttons
- Simplify loading of grid bookmark

## [0.0.1] - 06 May 2025
### Added
- Initial release
