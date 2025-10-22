# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.4.0] - 22 October 2025
### Fixed
- Value should be mixed
- Static filters should not depend upon regular filter interface
- WIP with columns selection and view selector
- Make sure CSP is applied to admin scripts
- Make sure search in multiple search fields are grouped with OR
- Detect resourceModel for grid repository just like with form repository
- Fix `from_to` filter being seen as not-empty wrongfully
- Fix wrong options model
- Add error message if options is not an OptionSourceInterface
- Allow button to be defined both via XML layout and custom PHP
- Extend docs
- Finalize mass actions
- Move mass actions from subclasses to XML layout
- Allow `from_to` with date
- Filtering of `from_to`
- Improve way of setting filters via filter builder
- Proper is equal filtering
- Allow clear specific filter
- Allow multiple filters to be set at once
- Allow for store view filte
- Allow for setting `field_type` in `grid_filters`
- Fetch exceptions in filters and reset state
- Working filter with grid repository
- WIP with new filtering in repository handler
- Improve API of grid filters and grid filter states
- Clear filter actions
- Add `button_actions` via XML layout

## [0.3.3] - 17 October 2025
### Fixed
- Better handling of inline edit actions
- Layout fix for filter expansion
- Cleanup static-filters and look & feel of filters
- Properly implement grid filtering
- Add CountryOptions ViewModel for usage in filters

## [0.3.2] - 15 October 2025
### Fixed
- Append template name to error message
- Add Column[] type hint to ArrayProviderInterface

## [0.3.1] - 06 October 2025
### Fixed
- Throw exception when template renderer is not found in critical templates

## [0.3.0] - 23 September 2025
### Fixed
- Fix block rendering of static blocks
- Implement new blockRenderer and childRenderer arguments
- Change containers into blocks to allow for caching
- Add `.prevent` modifier to `@click` event handler
- Rename Alpine store checkout to LokiCheckout, components to LokiComponents
- Rename loki-components to loki.script.component block

## [0.2.10] - 17 September 2025
### Fixed
- Bookmark status should not hide column, but simply set visible property
- Allow setting visible flag on grid column
- Set grid state paging from bookmark
- Fix loading different bookmark than default
- Merge XML-layout column info with current columns instead of removing all
- Refactor string-type columns into Column-class
- Add `store_id.phtml` cell template
- Change flag `allow_actions` into `hide_actions`

## [0.2.9] - 16 September 2025
### Fixed
- Rename loki-components.alpinejs to loki.alpinejs

## [0.2.8] - 04 September 2025
### Fixed
- Rename `field_type` text to input

## [0.2.7] - 02 September 2025
### Fixed
- Update README.md
- Add helper method for resource model
- Improve detection of model class with repository handler

## [0.2.6] - 28 August 2025
### Fixed
- Do not escape pricing format
- Fix wrong initialization of grid filter options
- Re-enable saving array values
- Add `product_id` field type
- Remove requirement of repository provider to define additional factory
- Get rid of confusing parent-block behaviour
- Implement delete and duplicate for collection provider
- Implement saving an item via collection provider
- Automatically set primary key to `field_type` "view"
- Allow changing HTML attributes of textarea; Rename `html_attributes` to `field_attributes`
- Transform `field_type` string to FieldTypeInterface instance
- Allow hiding a complete field with label by setting "visible=false"
- Simplify structure of Field and FieldFactory class
- Retrieve modelClass and resourceModelClass from provider
- Add CI files

## [0.2.5] - 26 August 2025
### Fixed
- Remove CSS util again
- Additional work on grids and form arguments

## [0.2.4] - 21 August 2025
### Fixed
- Do not add CSS util for Alpine-based `:class`
- Fix CSS utils
- Declare used PHP namespaces
- Document latest version of template
- Add missing `strict_types` declaration

## [0.2.3] - 18 August 2025
### Fixed
- Lower requirements to PHP 8.1
- Add escaping in PHTML templates

## [0.2.2] - 06 August 2025
### Fixed
- Lower PHP requirement to PHP 8.2+

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
