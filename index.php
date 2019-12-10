<?php 
require_once("vendor/autoload.php");

use \Slim\Slim;
use \kalaboka\Page;
use \kalaboka\Pageadmin;

$app = new Slim();
$app->config('debug', true);

/* 
$app->get('/', function() {
    
    $sql = new kalaboka\DB\Sql();
    $results = $sql->select("select * from tb_users");
    echo json_encode($results);
    
}); 
*/

$app->get('/', function() {
    
    $page = new Page();
    $page->setTpl("index");
    
});

$app->get('/admin', function() {
    
    $page = new PageAdmin();
    $page->setTpl("index");
    
});

$app->run();
 ?>