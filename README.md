# Laminas-Whoops, integrated [whoops](https://github.com/filp/whoops) in Laminas

-----

![Whoops!](http://i.imgur.com/0VQpe96.png)

**whoops** is an error handler base/framework for PHP. Out-of-the-box, it provides a pretty
error interface that helps you debug your web projects, but at heart it's a simple yet
powerful stacked error handling system.

# Table of Contents

* [Module installation](#module-installation)
* [Features](#features)
  * [Render View Manager - Twig Support](#render-view-manager---twig-support)
  * [Module Visibility Manager](#module-visibility-manager)
* [License](#license)



# Module installation
  1. `cd my/project/directory`
  2. create a `composer.json` file with following contents:

     ```json
     {
         "require-dev": {
             "ppito/laminas-whoops": "^2.0"
         }
     }
     ```
  3. install composer via `curl -s http://getcomposer.org/installer | php` (on windows, download
     http://getcomposer.org/installer and execute it with PHP)
  4. run `php composer.phar install`
  5. open `my/project/directory/configs/modules.config.php` and add the following key :

     ```php
     'WhoopsErrorHandler', // must be added as the first module
     ```
  6. optional : copy `config/module.config.php` in `my/project/directory/config/autoload/laminas-whoops.local.php`
  7. optional : edit `my/project/directory/config/autoload/laminas-whoops.local.php`

# Features

### Render View Manager - Twig Support 

By default this module use the simple php render, but you can now specify your favorite render.

##### Usage :
`Twig render` has been supported, you just need to change the `template_render` configuration:
```php
'template_render' => 'laminas_whoops/twig_error',
```

### Module Visibility Manager 

It is now possible to manage the module loading by implement your own rules.
For example, the module can be loaded only for the admin users or only for dev&preprod environments.

##### Usage :
* Create your own class by implement the interface [VisibilityServiceInterface](src/Service/VisibilityServiceInterface.php) (or the abstract [VisibilityServiceAbstract](src/Service/VisibilityServiceAbstract.php))
    * @see example [VisibilityService.visibility-example.php](src/Service/VisibilityService.visibility-example.php).
* Change the `visibility_service_name` configuration to specify the name of your class. 
    * @see example [module.config.visibility-example.php](config/module.config.visibility-example.php).
    ```php
    'visibility_service_name' => Application\Service\VisibilityService::class,
    ```

### Ignored Exceptions

By default, this module will route all exceptions to Whoops; however, you can create a list of
exception classes that will be ignored by Whoops.

##### Usage:
Set the `ignored_exceptions` configuration to an array of class names:
```php
'ignored_exceptions' => [My\Exception::class, My\OtherException::class]
```


# License

**ppito/laminas-whoops** is licensed under the MIT License - See the [LICENSE](LICENSE.md) file for details.

