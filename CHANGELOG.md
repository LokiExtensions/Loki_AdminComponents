# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

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
