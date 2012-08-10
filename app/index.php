<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Shield : A Security-Minded Microfrmaework</title>
        <link type="text/css" 
            href="http://twitter.github.com/bootstrap/assets/css/bootstrap.css"
            rel="stylesheet" type="text/css" media="screen"
            />
    </head>
    <body>
    <div class="container">
    <h1>Shield Framework Examples</h1>
    <div class="navbar">
        <div class="navbar-inner">
            <div class="container">
                <ul class="nav">
                    <li class="active"><a href="/">Home</a></li>
                    <li><a href="/filters?test=notvalid@email&test1=Me">Filtering</a></li>
                    <li><a href="/cfg">Custom Config</a></li>
                </ul>
            </div>
        </div>
    </div>

    <br/>
    <?php
    include_once '../Shield/Shield.php';

    $app = new Shield\Shield();

    /**
     * A basic route assigning values to the view
     */
    $app->get('/', function() use ($app){

        $app->view->set(array(
            'fake' => '<b>link text</b>',
            'test' => 'foodles'
        ));
        return $app->view->render('
            <h3>Basic Routing & Escaping</h3>
            <p>
                This route sets two values to the view "fake" & "test". The "fake"
                value has HTML in it and is, by default, filtered.
            </p>
            <br/><b>Output</b><hr/>
            index1: <a href="#[test]">[fake]</a>
        ');
    });

    /**
     * Setting up filters on data (desides the default)
     */
    $app->get('/filters', function() use ($app){

        $app->view->set(array(
            'test'  => $app->input->get('test'),
            'test1' => $app->input->get('test1')
        ));

        $app->filter->add(array(
            'test'  => 'email',
            'test1' => function($value){ 
                return 'custom filter: '.$value;
            }
        ));

        return $app->view->render('
            <h3>Built-in & Custom Filters</h3>
            <p>
                There are several built-in filtering types including "email", "integer" and "url"
                to help you sanitize your data.<br/>
                You can also add custom filters using PHP\'s closures.
            </p>
            <br/><b>Output</b><hr/>
            TEST1: [test1]<br/>
            TEST:  [test]
        ');
    });

    /**
     * Shows how to use a custom configuration with a route
     * NOTE: This page is in an HTML template, so this XML won't render correctly
     * (but you get the idea....)
     */
    $app->get('/cfg', function() use ($app){

        $app->view->set('output','my output');

        return $app->view->render('
            <?xml version="1.0">
            <test>
                <value>[output]</value>
            </test>
        ');

    }, array(
        'view.content-type' => 'text/xml'
    ));

    /** Execute the application */
    $app->run();
    ?>
    </div>
    </body>
</html>