# Puzzle Admin Page Bundle
**=========================**

Puzzle bundle for managing admin 

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the following command to download the latest stable version of this bundle:

`composer require webundle/puzzle-admin-page`

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
{
    $bundles = array(
    // ...

    new Puzzle\Admin\PageBundle\PuzzleAdminPageBundle(),
                    );

 // ...
}

 // ...
}
```

### Step 3: Register the Routes

Load the bundle's routing definition in the application (usually in the `app/config/routing.yml` file):

# app/config/routing.yml
```yaml
puzzle_admin:
        resource: "@PuzzleAdminPageBundle/Resources/config/routing.yml"
```

### Step 4: Configure Dependency Injection

Then, enable management bundle via admin modules interface by adding it to the list of registered bundles in the `app/config/config.yml` file of your project under:

```yaml
# Puzzle Client Page
puzzle_admin_page:
    title: page.title
    description: page.description
    icon: page.icon
    roles:
        page:
            label: 'ROLE_PAGE'
            description: page.role.default
```

### Step 5: Configure navigation menu

Then, enable management bundle via admin modules interface by adding it to the list of registered bundles in the `app/config/config.yml` file of your project under:

```yaml
# Client Admin
puzzle_admin:
    ...
    modules_availables: '...,page'
    navigation:
    	nodes:
    		page:
                label: 'cms.base'
                translation_domain: 'admin'
                attr:
                    class: 'icon-file-text'
                parent: ~
                user_roles: ['ROLE_PAGE', 'ROLE_ADMIN']
                tooltip: 'contact.tooltip'
            page_list:
                label: 'cms.page.base'
                translation_domain: 'admin'
                path: 'admin_page_list'
                sub_paths: ['admin_page_create', 'admin_page_update', 'admin_page_show']
                parent: page
                user_roles: ['ROLE_PAGE', 'ROLE_ADMIN']
                tooltip: 'cms.page.tooltip'
            page_template:
                label: 'cms.template.base'
                translation_domain: 'admin'
                path: 'admin_page_template_list'
                sub_paths: ['admin_page_template_create', 'admin_page_template_update', 'admin_page_template_show']
                parent: page
                user_roles: ['ROLE_PAGE', 'ROLE_ADMIN']
                tooltip: 'cms.template.tooltip'
```