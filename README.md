# Yireo_LokiAdminComponents

**This Magento 2 module allows for developers to build admin grids and admin forms in the Magento 2 Admin Panel in
a faster way. No more ugly UiComponents that nobody understands. Create grids and forms quickly. And gradually add
features to them, without getting stuck.**

The LokiAdminComponents are based on the [Yireo LokiComponents](https://github.com/yireo/Yireo_LokiComponents)
module which is also used in the [Yireo LokiCheckout](https://loki-checkout.com/). However, this admin tool shows
that the Loki suite of Yireo can be applied in many more places.

## Installation
```bash
composer require yireo/magento2-loki-admin-components
bin/magento module:enable Yireo_LokiAdminComponents
```

As a demo, you could opt to install the following examples:

- [YireoTraining_ExampleLokiAdminProducts](https://github.com/yireo-training/YireoTraining_ExampleLokiAdminProducts) - a product repository-based grid with various modifications including inline editing of the product name; 
- [YireoTraining_ExampleLokiAdminCmsPage](https://github.com/yireo-training/YireoTraining_ExampleLokiAdminCmsPage) - a bare-bones repository-based implementation that is ugly, but it shows what you get with minimal effort;
- [YireoTraining_ExampleLokiAdminArrayGrid](https://github.com/yireo-training/YireoTraining_ExampleLokiAdminArrayGrid) - a bare-bones array-based grid;
- [YireoTraining_YireoTraining_ExampleLokiAdminEavEntityType](https://github.com/yireo-training/YireoTraining_YireoTraining_ExampleLokiAdminEavEntityType) - a bare-bones collection-based grid to display EAV entity types;

## Features

### Grid features
- Autodetection of columns
- Search
- Pagination
- Sorting columns
- Custom cell templates
- Inline editing
- Custom columns
- ... (more docs coming soon)

### Grid provider handlers
- `repository`
- ~~`collection`~~ (coming soon)
- ~~`array`~~ (coming soon)

### Form features
- Block-based form fields
- Form actions
- ... (more docs coming soon)

## Building your first Loki admin module

### Create an admin module
First of all, build your own Magento module with the following base elements:

- `etc/module.xml` module defition file;
- `registration.php` module registration file;
- `etc/adminhtml/routes.xml` to define your own route in the admin;
- Backend controller class that renders a page via the XML layout;
- `etc/acl.xml` file for proper permissions;
- Optionally `etc/adminhtml/menu.xml` file to bring users to your backend page;
- Optionally `composer.json` for installing this module via composer;

And last but not least, an XML layout file that allows adding content to your specific page. This is where we kick
off the Loki story. Below, we assume this file to be called `view/adminhtml/layout/example.xml`.

We assume that you are a Magento developer and that you are able to get this far.

### Adding XML layout
Within the layout file `view/adminhtml/layout/example.xml`, add the following:

```xml
<?xml version="1.0"?>
<page xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:module:View/Layout:etc/page_configuration.xsd">
    <update handle="loki_admin_components_grid"/>

    <body>
        <referenceContainer name="content">
            <block
                name="loki_admin_example.grid"
                template="Yireo_LokiAdminComponents::grid.phtml">
                <arguments>
                    <argument name="namespace" xsi:type="string">product_listing</argument>
                    <argument name="provider" xsi:type="string">Magento\Catalog\Api\ProductRepositoryInterface</argument>
                    <argument name="provider_handler" xsi:type="string">repository</argument>
                </arguments>
            </block>
        </referenceContainer>
    </body>
</page>
```

There are perhaps other things that you want to add to the page, like a `title`. But that's not needed for this scope.

With the handle `loki_admin_components_grid`, the behaviour for admin grids is imported.

As you can see, you simply define your own grid block, while reusing the PHTML template of the Loki module. The `provider` needs to point
a class. In this example, it is inserted as a string, because the product repository class does not implement the required
`ArgumentInterface`. A provider could be either `repository`, `collection` or `array`.

### Transforming the grid block into a Loki Component
Next, we will need to transform the grid block into a Loki Component, allowing for magic to happen on the JavaScript-level (using
Alpine.js) and the PHP-level. For this, we create a file `etc/loki_components.xml` with the following content:

```xml
<?xml version="1.0" encoding="UTF-8" ?>
<components xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:module:Yireo_LokiComponents:etc/loki_components.xsd">
    <component
        name="loki_admin_example.grid"
        viewModel="Yireo\LokiAdminComponents\Component\Grid\GridViewModel"
        repository="Yireo\LokiAdminComponents\Component\Grid\GridRepository">
    </component>
</components>
```

The default behaviour detects columns by looking at the first item that your provider provides. Either this includes too many fields, too few or the labels are
not good enough. For this, it is normal to create your own ViewModel class, extending it from `Yireo\LokiAdminComponents\Component\Grid\GridViewModel`. 

In most cases, you do not need to extend the repository class. If you want to do this, **let us know** first via an GitHub Issue.

### Done
If you refresh the cache, you should now have a workable grid.
