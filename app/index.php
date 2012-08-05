<?php
include_once '../Shield/Shield.php';

$app = new Shield\Shield();

$app->get('/', function() use ($app){

    /** Add some View values */
    $app->view->set('fake','<b>link text</b>');
    $app->view->set('test', 'foodles');

    /** Add a filter of type "email" */
    $app->filter->add('test', 'email');

    /** Adding a filter with a closure **/
    $app->filter->add('test1', function($value){ 
        echo 'my value: '.$value; return $value; 
    });

    /** GETs from the URL, "?test=foo"
        this will be empty because there's an "email" filter on it */
    echo "?test : {$app->input->get('test')}\n";

    /** GETs from the URL, "&test1=foo" */
    echo "&test1: {$app->input->get('test1')}\n";

    echo 'config: '; var_export($app->config->get('test.foo')); echo '<br/><br/>';
    
    /** Render the template output */
    return $app->view->render('index1: <a href="#[test]">[fake]</a><br/><br/>');
});

/** Define a "/test" route */
$app->get('/test', function(){
    echo 'This is the Test route';
});


echo "<pre>";
$app->run();
echo "</pre>";