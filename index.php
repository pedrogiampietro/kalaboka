<?php 
session_start();
require_once("vendor/autoload.php");

ini_set('display_errors',1);
ini_set('display_startup_erros',1);
error_reporting(E_ALL);


use \Slim\Slim;
use \kalaboka\Page;
use \kalaboka\PageAdmin;
use \kalaboka\Model\User;

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

$app->get('/admin/', function() {
    
    User::verifyLogin();
    $page = new PageAdmin();
    $page->setTpl("index");
  });

  $app->get('/admin/login', function() {
    $page = new PageAdmin([
      "header"=>false,
      "footer"=>false
    ]);
    $page->setTpl("login");
  });

  $app->post('/admin/login', function() {
    User::login($_POST["login"], $_POST["password"]);
    header("Location: /admin");
    exit;
  });

$app->get('/admin/logout', function(){
	User::logout();
	header("Location: /admin/login");
	exit;
	
});



$app->run();
 ?>