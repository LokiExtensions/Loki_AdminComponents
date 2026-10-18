# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.8.1] - 18 October 2026
### Added
- support button URLs and sub-buttons in forms

### Fixed
- document button, field and fieldset fixes
- let security filter know about all form fields
- display fields and fieldsets added by a form provider
- prevent provider fieldsets from being resolved twice
- use URL of sub-button itself in button template
- add unit tests for ButtonsResolver and default resolvers
- do not fail on missing provider when resolving buttons and fields
- resolve buttons from XML layout correctly
- fix button argument mismatch in FormBuilder::createButton
- Fix `TypeError` in `FormBuilder::createButton()` caused by new `$id` argument of `ButtonFactory::create()` (issue #20)
- Add optional `$id` argument to `FormBuilder::createButton()` and `ButtonFactory::createLinkAction()`
- Default button ID to its method in `ButtonFactory::create()`, and to its URL in `createLinkAction()`
- Add `getId()` to `ButtonInterface`
- Key buttons of `Form` and `Grid` by button ID
- Fix `subButtons` of XML layout buttons causing a `TypeError`; they are now converted into button objects
- Properly cast XML layout `primary` flag to boolean (also when overriding existing buttons)
- Allow overriding existing buttons by ID via XML layout `buttons` or `button_actions`
- Prevent XML layout buttons with numeric keys from overwriting other buttons
- Do not throw exceptions when a form or grid has no provider while resolving buttons, fields or fieldsets
- Throw `NoProviderException` (now extending `RuntimeException`) from `GridRepository::getProvider()`
- Use the URL of sub-buttons themselves in template `ui/button.phtml`
- Do not translate button methods and CSS classes, and escape button URLs in template `ui/button.phtml`
- Prevent provider fieldsets from being resolved twice
- Deprecate `BlockFieldsetsResolver`, a duplicate of `ProviderFieldsetsResolver`
- Actually use the configured `fieldsResolvers` in `Form\Field\FieldsResolver`, so that fields added via `Form::addField()` are displayed
- Deprecate `BlockFieldsResolver`, a duplicate of `ProviderFieldsResolver`
- Key fields and fieldsets of `Form` by their code
- Do not replace a provider fieldset `base` with an empty fieldset
- Place XML layout fields into their own `fieldset` instead of the fieldset of a previous field
- Skip empty fieldsets
- Return all fields of all fieldsets in `FormViewModel::getFields()` (so the security filter also knows about provider fields)
- Render form buttons via `ui/button.phtml`, adding support for button URLs and sub-buttons in forms
- Add `redirectAction()` to the form component type
- Remove the non-working hard-coded `other_save_actions` from template `form/actions.phtml` (use sub-buttons instead)
- Add unit tests and integration tests for buttons and resolvers

## [0.8.0] - 13 October 2026
### Fixed
- Prevent grid columns from being displayed twice once a uiBookmark exists for a grid without provider columns
- Add `trim()` its second argument for PHP 8.6 compatibility
- Fix missing return type in `\Loki\AdminComponents\Form\Field\Field::setBlock`
- Make system strings translatable
- Make block optional in Field class
- Implement multiple FieldsResolvers and FieldsetsResolvers
- Resolve form buttons via ButtonsResolver
- Add new Grid object to be used with GridProviderInterface
- Rewrite button resolver mechanism for both form and grid
- Remove unneeded message manager from FormRepository
- When actions are not successfull, do not redirect back to `index_url`
- When item validators fail, do not redirect to `index_url`
- Add option for `item_validators` when saving a form
- Change form redirect to use RedirectException
- Implement NoProviderException in various places
- Field type `customer_select`: When customer is empty, return empty string

## [0.7.2] - 08 August 2026
### Security
- Rewrite `x-html` to `x-text` because of CSP

### Fixed
- Allow for field type `text` anyway
- Fix README for using `text` field type (which ideally is `input`)
- Make sure to merge uiBookmark based columns with detected ones
- Prevent PHP notice with non-string property names
- Properly set DOM ID for product ID field type

## [0.7.1] - 14 July 2026
### Fixed
- Remove faulty namespace import
- Allow admin component to define `acl_resource` for added security

## [0.7.0] - 03 July 2026
### Added
- Entity selection
- First draft of FormBuilder API
- Functioning active columns in grid

### Fixed
- Refactor ButtonsResolver for cleariy
- Fix a11y issues
- Require PHP 8.1 as a minimum
- Add form context and repository context class
- Fix issue when filter state contains a non-serializable value
- Fix issue with preconfigured filter objects not being added properly
- Fix possible issues in RepositionAction
- Fix required star with quick hack in inline style
- Allow form buttons to be defined via XML layout
- Allow for bookmarks of non-bookmark entities
- Prevent exception when grid namespace doesn't have bookmark record
- Fix issue with pagination in bookmark grid
- Set default gridFilter value via XML layout
- Rename `script x-ref="initialData"` to `data` element for browser compliance

## [0.6.4] - 05 May 2026
### Fixed
- Fix sorting of countries
- Add cell template for just HTML
- Allow textarea to determine rows

## [0.6.3] - 01 May 2026
### Fixed
- Fix issue with massactions only selecting first item
- New multiselect field type (even though this already was possible with select)
- New Yesno options model

## [0.6.2] - 02 March 2026
### Fixed
- Fix JavaScript TypeError: columns initialized as object instead of array

## [0.6.1] - 20 February 2026
### Fixed
- Add `#[\ReturnTypeWillChange]` to allow for removal of `mixed` type hint

## [0.6.0] - 12 February 2026
### Added
- Category selection input implementation #14 @rajeev-k-tomy

### Fixed
- Fix intermittent issues where `this.data.id` is not available with `category_selection` Alpine components @rajeev-k-tomy
- Avoid sending children infor as part of UI data from CategoryTreeNode DTO @rajeev-k-tomy
- Change `category_selection` input alpine component name to CategorySelection @rajeev-k-tomy
- Remove unnecessary classes from `category_selection/select_inner_item.phtml` @rajeev-k-tomy
- Separate main script from the main template of category selection

## [0.5.2] - 28 January 2026
### Fixed
- Add hidden-attribute to field-blocks
- Fix new field types not having proper interface
- Fix when there are no fieldset definitions
- Allow Options-model to be either string or object
- #8 Allow to set label for the base fieldset @rajeev-k-tomy
- #4 Custom fields configured via layout xml file are not appearing in the form @rajeev-k-tomy
- Resolve select field options from the Select field type class
- #3 Create an abstract FieldType class to give access Field instance in every FieldType @rajeev-k-tomy

## [0.5.1] - 23 January 2026
### Fixed
- Do not escape JSON
- Make sure visible-flag of columns defaults to true

## [0.5.0] - 21 January 2026
### Added
- Finalize `entity_select`, add type `product_select` and `customer_select`

### Fixed
- Allow fields defined in XML layout only (without provider)

## [0.4.4] - 12 January 2026
### Fixed
- Add new GitHub Action workflows
- Copy generic CI/CD files
- Implement required fields in a simple way
- Implement filtering for array grids
- Allow using `searchable_fields` to *restrict* which fields are searched by the ArrayHandler

## [0.4.3] - 30 December 2025
### Fixed
- Default unknown field types to `input`
- Add field dependencies
- Allow for fieldsets
- Redirect massactions simply to current page
- WIP UiComponents

## [0.4.2] - 21 November 2025
### Fixed
- First draft of new entity select field
- Allow both Alpine method and regular JS call in cell actions
- Allow for cell actions to be Alpine methods as well
- Allow for using EAV collection as provider

## [0.4.1] - 14 November 2025
### Fixed
- Add FilterScope to every Filter
- Add component and scope args to FilterInterface::filter()
- Basic editor functionality
- Update composer keywords
- Update composer keywords
- Update composer keywords

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
