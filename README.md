# Yireo_LokiAdminComponents

**This Magento 2 module allows for developers to build admin grids and admin forms in the Magento 2 Admin Panel in
a faster way. No more ugly UiComponents that nobody understands. Create grids and forms quickly. And gradually add
features to them, without getting stuck.**

The LokiAdminComponents are based on the [Yireo LokiComponents](https://github.com/yireo/Yireo_LokiComponents)
module which is also used in the [Yireo LokiCheckout](https://loki-checkout.com/). However, this admin tool shows
that the Loki suite of Yireo can be applied in many more places.

*Note that this library makes use of Alpine.js in the Magento Admin Panel. However, TailwindCSS is not used, we stick to the native Magento Admin Panel classes instead.*

## Installation
```bash
composer require yireo/magento2-loki-admin-components
bin/magento module:enable Yireo_LokiAdminComponents Yireo_LokiComponents
```

## Examples
As a demo, you could opt to install the following examples:

- [YireoTraining_ExampleLokiAdminProducts](https://github.com/yireo-training/YireoTraining_ExampleLokiAdminProducts) - a product repository-based grid with various modifications including inline editing of the product name; 
- [YireoTraining_ExampleLokiAdminCmsPage](https://github.com/yireo-training/YireoTraining_ExampleLokiAdminCmsPage) - a bare-bones repository-based implementation that is ugly, but it shows what you get with minimal effort;
- [YireoTraining_ExampleLokiAdminArrayGrid](https://github.com/yireo-training/YireoTraining_ExampleLokiAdminArrayGrid) - a bare-bones array-based grid;
- [YireoTraining_YireoTraining_ExampleLokiAdminEavEntityType](https://github.com/yireo-training/YireoTraining_YireoTraining_ExampleLokiAdminEavEntityType) - a bare-bones collection-based grid to display EAV entity types;

![Screenshot of YireoTraining_ExampleLokiAdminProducts](loki-admin-grid-products.png)

## Features

### Grid features
- Autodetection of columns
- Search
- Pagination
- Sorting columns
- Custom cell templates
- Inline editing
- Filters
- Mass Actions
- Custom cell templates
- ... (more docs coming soon)

### Grid provider handlers
- `repository`
- `collection`
- `array`

### Form features
- Autodetection of columns
- Field-types (select, text, number, datetime, etc) with option to configure more
- Form actions (Back, Save and Close, Save and Continue, Delete, etc)
- ... (more docs coming soon)

### Documentation
See [the wiki](https://github.com/yireo/Yireo_LokiAdminComponents/wiki)

# Todo
- Easily extend LokiComponent partials via XML layout
- Add file `view/adminhtml/loki/example-grid.xml`
- Example database query in custom array provider
- Extension Attributes
- Filesystem navigation
- Tiles instead of grid layout
- Delete add confirmation
- Add custom SearchCriteriaBuilder via repository addons
- Switch field for enabled/disabled column within grid
- Export selected to CSV, JSON, XSLX
