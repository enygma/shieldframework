<?php
include_once '../Shield/Shield.php';

$app = new Shield\Shield();

$app->get('/', function() use ($app){

    $app->view->set('test', 'foodles');
    $app->filter->add('test', 'email');
    
    echo "?test : {$app->input->get('test')}\n";;
    echo "&test1: {$app->input->get('test1')}\n";
    
    return $app->view->render('index1: <a href="#[test]">[fake]</a>');
});
$app->get('/test', function(){
    echo 'test';
});
echo "<pre>";
$app->run();
echo "</pre>";