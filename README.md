# Loki_AdminComponents

**This Magento 2 module allows for developers to build admin grids and admin forms in the Magento 2 Admin Panel in
a faster way. No more ugly UiComponents that nobody understands. Create grids and forms quickly. And gradually add
features to them, without getting stuck.**

The LokiAdminComponents are based on the [Loki Components](https://github.com/LokiExtensions/Loki_Components) module which is also used in the [LokiCheckout](https://loki-checkout.com/). However, this admin tool shows that the Loki Extension suite can be applied in many more places.

*Note that this library makes use of Alpine.js in the Magento Admin Panel. As for CSS, we stick to the native Magento Admin Panel classes instead: Everything looks the same.*

## Installation
```bash
composer require loki/magento2-admin-components
bin/magento module:enable Loki_AdminComponents Loki_Components Loki_CssUtils Loki_Base
```

## Getting started with a grid
First create a module that generates a page in the Admin Panel (`etc/module.xml`, `registration.php`, a backend controller action class, perhaps `acl.xml` and `menu.xml`). 

In your new XML layout file, add the following:

```xml
<?xml version="1.0"?>
<page xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:module:View/Layout:etc/page_configuration.xsd">
    <update handle="loki_admin_components_grid"/>

    <body>
        <referenceContainer name="content">
            <block
                name="yireo-training.example-admin.grid"
                template="Loki_AdminComponents::grid.phtml">
                <arguments>
                    <argument name="provider" xsi:type="string">
                        Magento\Catalog\Api\ProductRepositoryInterface
                    </argument>
                </arguments>
            </block>
        </referenceContainer>
    </body>
</page>
```

And you're done. After refreshing the Magento cache, you now have a product grid.

Refer to the [documentation](https://loki-extensions.com/docs/admin-components/grid) for fine-tuning this grid.

## Getting started with a form
Again, first create a module that generates a page in the Admin Panel (`etc/module.xml`, `registration.php`, a backend controller action class, perhaps `acl.xml` and `menu.xml`).

In your new XML layout file, add the following:

```xml
<?xml version="1.0"?>
<page xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:module:View/Layout:etc/page_configuration.xsd">
    <update handle="loki_admin_components_form"/>

    <body>
        <referenceContainer name="content">
            <block
                name="yireo-training.example-admin.form"
                template="Loki_AdminComponents::form.phtml">
                <arguments>
                    <argument name="provider" xsi:type="string">
                        Magento\Catalog\Api\ProductRepositoryInterface
                    </argument>
                </arguments>
            </block>
        </referenceContainer>
    </body>
</page>
```

And you're done. After refreshing the Magento cache, you now have a basic product form.

Refer to the [documentation](https://loki-extensions.com/docs/admin-components/form) for fine-tuning this form.

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
See [https://loki-extensions.com/docs/admin-components](https://loki-extensions.com/docs/admin-components)

# Todo
- Extension Attributes
- Filesystem navigation
- Tiles instead of grid layout
- Delete add confirmation
- Add custom SearchCriteriaBuilder via repository addons
- Switch field for enabled/disabled column within grid
- Export selected to CSV, JSON, XSLX
