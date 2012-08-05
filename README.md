Shield : A Security-Minded Microframework
===============
[![Build Status](https://secure.travis-ci.org/enygma/shieldframework.png?branch=master)](http://travis-ci.org/enygma/shieldframework)

In my efforts to learn more about security best practices in PHP, I noticed that most of the PHP 
frameworks out there left it up to the developer to correctly handle input/output/etc themselves.
Unfortunately, this has been a sticking point in PHP apps, so I decided to work on a microframework
that was designed with security in mind.

This project is under a MIT license.

[shieldframework.com](http://shieldframework.com)

Disclaimer
----------------
*Please note:* This framework is a work in progress and is serving as a resource to learn more
about PHP and web application security. Use of this framework will *not* provide the perfect
security for your application, nor should it be considered an ultimate resource for security 
best practices.

### Features:
- Output filtering on all values (preventing XSS)
- Logging on all actions
- Input filtering functionality for accessing all superglobal information
- Uses PHP's own filtering for data sanitization
- Encrypted session handling (RIJNDAEL_256/MCRYPT_MODE_CBC, uses IV)
- Custom cookie handling (including httpOnly)
- Customized error handling to avoid exposing filesystem information
- Basic templating/view system
- IP-based access control
- Session fixation prevention

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

The `$type` parameter for the `add()` method can either be a string for the filter type or it can be a \Closure that will
be given the value of the field as a parameter - for example:

```php
<?php

$app->filter->add('myField', function($value) {
    return 'returned: '.$value;
});
```

You must be sure to return from this closure, otherwise the filtering will return null.


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

### Template
A basic templating engine included in the framework. By default it looks for a file named with the string given (in views/) or falls back to a `str_replace` method treating it as a string.
* `render($template)`: Either the name of the template file (no .php) or the string to use as a template

*NOTE:* If you choose to use the string as a template (no file), you must use the "[varName]" notation to get the values to substitute. Values can be set directly to the template instance (ex. `$app->view->template->test = 'foo';`)

Configuration
--------------
An optional `config.php` file can be placed in the same root as your front controller (probably `index.php`) so
it can be found by the framework. This configuration file is a PHP array returned with your settings. These values 
can be accessed through the `$di->get('Config')->get()` method call. Here's an example config:

```php
<?php
return array(
    'log_path' => '/tmp'
);
```

Additionally, you can use a "dotted notation" to find configuration options. So, for example, to find the value below:

```php
<?php
return array(
    'foo' => array(
        'bar' => array(
            'baz' => 'testing this'
        )
    )
);
```

You can use `$app->config->get('foo.bar.baz');` to get the value "testing this".

### Available Config options
* `log_path`: Set the default logging path
* `session.path`: Set the path on the local filesystem to save the session files to
* `session.key`: Customize the key used for the session encryption
* `session.lock`: Enable/disable session locking (binds session to the IP+User Agent to help prevent fixation)
* `allowed_hosts`: Array of hosts allowed to make requests (whitelisting)

How To Contribute
--------------
First off, thanks for considering submitting changes for the project - help is always appreciated!
If you're going to contribute to the project, here's a few simple steps to follow:

* When contributing, please make a branch on your clone of the repo and commit your changes there (
    this makes it *much* simpler when the time comes to merge)
* Submit a pull request with good detail on what changed - reading through code is fun, but a summary 
    is better
* Contact information is below - feel free to email or send a message on github if you have questions!

Shield and the OWASP "Top Ten"
--------------
One of the "gold standards" in the web application security community is the infamous ["Top Ten"](http://owasptop10.googlecode.com/files/OWASP%20Top%2010%20-%202010.pdf) list of common security issues that web apps have. Shield, being the nice framework that it is, tries to help protect you and your app from these problems. Here's how:

* A1: Injection - All user input is filtered with at least one filter (including all PHP superglobals).
* A2: Cross-Site Scripting - Before any information is accessed it is passed through at least one filter. Additionally, you can provide custom filtering via closures.
* A3: Broken Authentication & Session Management - All session information is encrypted as it is stored using a Rijdael (256) method with an initialization vector.
* A4: Insecure Direct Object References - Currently there's no permissioning system (and no auth system) in the framework.
* A5: Cross-Site Request Forgery - Currently not prevented.
* A6: Security Misconfiguration - The framework checks different PHP configuration settings to ensure that common security issues are mitigated.
* A7: Insecure Cryptographic Storage - As previously mentioned, the only storage the framework does - sessions - stores the values encrypted.
* A8: Failure to Restrict URL Access - Included in the framework is the ability to restrict based on IP. More fine-grained restriction is coming soon.
* A9: Insufficient Transport Layer Protection - The framework currently does not prevent the use of HTTP over HTTPS.
* A10: Unvalidated redirects & Forwards - The framework does not provide a mechanism for redirecting/forwarding.

Contact
--------------
Chris Cornutt <ccornutt@phpdeveloper.org>

[@enygma](http://twitter.com/enygma)


