<?php
include_once '../Shield/Shield.php';

$app = new Shield\Shield();

$app->get('/',function() use ($app){

    $app->view->set('test','<a href="">foodles</a>');
    $app->filter->add('test','email');

    echo 'in: '.$app->input->get('test').'<br/>';
    echo 'in1: '.$app->input->get('test1').'<br/>';

    return $app->view->render('index1 [test]');
});
$app->get('/test',function(){
    echo 'test';
});

$app->run();

?>