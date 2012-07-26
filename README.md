Shield : A Security-Minded Microframework
===============

In my efforts to learn more about security best practices in PHP, I noticed that most of the PHP 
frameworks out there left it up to the developer to correctly handle input/output/etc themselves.
Unfortunately, this has been a sticking point in PHP apps, so I decided to work on a microframework
that was designed with security in mind.

This project is under a MIT license.

### Features:
- Output filtering on all values (preventing XSS)
- Logging on all actions
- Input filtering functionality for accessing all superglobal information
- Uses PHP's own filtering for data sanitization
- Encrypted session handling (DES w/salt)

Requires
---------------
* PHP 5.3.x
* mcrypt extension (for sessions)

The Code
---------------
I'm a big fan of the Slim microframework, so anyone that's used that will feel at home with Shield.
Here's some example code of it in use:

```php
<?php
include_once '../Shield/Shield.php';
$app = new Shield\Shield();

$app->get('/',function(){
    echo 'website root! woo!';
});

$app->run();

```

The above example is super simple - all it does is handle (thanks to the included .htaccess file)
the request for the root level route "/" as a GET request. When it matches the route, it executes
the closure callback and echos out "website root! woo!" Easy right?

Let's take a look at something a bit more complicated to introduce you to a few other handy tools
at your disposal:

```php
<?php
include_once '../Shield/Shield.php';
$app = new Shield\Shield();

$app->get('/',function() use ($app){

    $app->filter->add('test','email');

    echo 'from the URL: '.$app->input->get('test').'<br/>';

    $app->view->set('test','<a href="">foodles</a>');
    return $app->view->render('index1 [test]');
});

$app->run();

```

First off, there's one key difference between this example and the first one. In this example we 
pass in the `$app` object itself so we have access to some special features. Here's a quick overview:

* `$app->view`: An instance of the View object that can be used to do some more complex view handling
* `$app->filter`: A filtering object that lets you add and execute filters on the given data
* `$app->input`: A feature to pull in values from the PHP superglobals ($_GET, $_POST, etc)
* `$app->log`: A logging instance (what the framework uses too)
* `$app->config`: Access to the configuration options, reading and writing

There's also one other thing that could help in more complex development - the DI container. The framework
makes heavy use of a Dependency Injection Container (DIC) to work with its resources. This is exposed 
back to the user as well, so you can access `$app->di` and use it to manage your own object instances as well.

Documentation
-----------------
### Shield
The `Shield` class is the main class you'll use and really only has a handful of methods:
* `run()`: execute the application, no parameters
* Each of the routing methods like `get()` and `post()`. Two parameters: route and closure/callback

### Config
Access the values loaded from the configuration file or set/read your own.
* `set($keyName,$value)`: Set a configuration value
* `get($keyName)`: Get a configuration value
* `load($path)`: Load the values from the path into the app (overwrites), default looks for "config.php"
* `getConfig()`: Get all of the config options as an array
* `setConfig($configArr)`: Set the array of options to the configuration (overwrites)

## Di
Access to the dependency injection container (getting & setting)
* `register($obj,$alias)`: Register an object in the container, `$alias` is optional. Uses classname as name
if not defined
* `get($name)`: Get the object with the given name from the container

### Filter
Filter values based on filter types (supported are: email, striptags). Filters are applied when `get()` is called.
* `add($fieldName,$type)`: Add a filter of the `$type` when the `$fieldName` is fetched
* `filter($fieldName,$value)`: Looks for the filter(s) on the object and executes them in order (FIFO) on the `$value`

*NOTE:* If no filters are specified, it will execute a "strip_tags" on the data by default.

### Input
Pull values from the PHP superglobals (filtered)
* `get($name)`: Pull from the $_GET, `$name` is name of variable
* `post($name)`: Pull from the $_POST, `$name` is name of the variable
* `request($name)`: Pull from the $_REQUEST, `$name` is name of the variable
* `files($name)`: Pull from the $_FILES, `$name` is the name of the variable
* `server($name)`: Pull from the $_SERVER, `$name` is the name of the variable
* `set($type,$name,$value)`: Push a `$value` into the property `$name` of `$type` ('session','get','post',etc)

*NOTE:* Superglobals are *unset* following a creation of an Input object.

### Log
Logging to a file
* `log($msg,$level)`: Message to log to the file, `$level` is optional (default "info")

### View
Handle output to the page
* `set($name,$value)`: Sets a variable into the view to be replaced in a template
* `render($content)`: Renders and returns the content, any variables set to the object are replaced using the notation "[name]"

*NOTE:* All values are escaped/filtered by default to prevent XSS. This can be overridden if desired.

Contact
--------------
Chris Cornutt <ccornutt@phpdeveloper.org>
@enygma

