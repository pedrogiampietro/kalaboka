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

$app->get("/", function() {
    
  $page = new Page();

  $page->setTpl("index");

});

$app->get("/admin", function() {
    
  User::verifyLogin();

  $page = new PageAdmin();

  $page->setTpl("index");

});

$app->get("/admin/login", function() {

  $page = new PageAdmin([
    "header"=>false,
    "footer"=>false
  ]);

  $page->setTpl("login");

});

$app->post("/admin/login", function() {

  User::login($_POST["login"], $_POST["password"]);

  header("Location: /admin");
  exit;

});

$app->get("/admin/logout", function() {

  User::logout();

  header("Location: /admin/login");
  exit;

});

$app->get("/admin/users", function() {

  User::verifyLogin();

  $users = User::listAll();

  $page = new PageAdmin();

  $page->setTpl("users", array(
    "users"=>$users
  ));

});

$app->get("/admin/users/create", function() {

  User::verifyLogin();

  $page = new PageAdmin();

  $page->setTpl("users-create");

});

$app->get("/admin/users/:iduser/delete", function($iduser) {

  User::verifyLogin();  

  $user = new User();

  $user->get((int)$iduser);

  $user->delete();

  header("Location: /admin/users");
  exit;

});

$app->get("/admin/users/:iduser", function($iduser) {

  User::verifyLogin();

  $user = new User();

  $user->get((int)$iduser);

  $page = new PageAdmin();

  $page->setTpl("users-update", array(
    "user"=>$user->getValues()
  ));

});

$app->post("/admin/users/create", function() {

  User::verifyLogin();

  $user = new User();

  $_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;

  $_POST['despassword'] = User::getPasswordHash($_POST['despassword']);

  $user->setData($_POST);

  $user->save();

  header("Location: /admin/users");
  exit;

});

$app->post("/admin/users/:iduser", function($iduser) {

  User::verifyLogin();

  $user = new User();

  $_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;

  $user->get((int)$iduser);

  $user->setData($_POST);

  $user->update();  

  header("Location: /admin/users");
  exit;

});

$app->get("/admin/forgot", function() {

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot");	

});

$app->post("/admin/forgot", function(){

	$user = User::getForgot($_POST["email"]);

	header("Location: /admin/forgot/sent");
	exit;

});

$app->get("/admin/forgot/sent", function(){

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot-sent");	

});

$app->get("/admin/forgot/reset", function(){

	$user = User::validForgotDecrypt($_GET["code"]);

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot-reset", array(
		"name"=>$user["desperson"],
		"code"=>$_GET["code"]
	));

});

$app->post("/admin/forgot/reset", function(){

	$forgot = User::validForgotDecrypt($_POST["code"]);	

	User::setFogotUsed($forgot["idrecovery"]);

	$user = new User();

	$user->get((int)$forgot["iduser"]);

  $password = User::getPasswordHash($_POST["password"]);

	$user->setPassword($password);

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot-reset-success");

});


$app->run();

 ?>