<?php
require 'flight/Flight.php';
require 'util.php';

Flight::route('/', function(){
    echo 'hello world!';
});

Flight::route("/users", function(){
  $users = util::exec("SELECT * FROM tbl_users;");

  header("Content-type: text/json");
  echo json_encode($users);
  // echo "<pre>".print_r($users, true);
});

Flight::route("GET /search/@entity/*", function($entity, $route){
  header("Content-type: text/json");

  $tables = [
    "users" => "tbl_users",
    "countries" => "tbl_countries"
  ];

  if(!isset($tables[$entity])){
    die(json_encode([
        "code" => "1",
        "message" => "entity not found"
      ]));
  }

  // initial statement
  $statement = "SELECT * FROM `{$tables[$entity]}`";

  $conditionals = explode("/", $route->splat);

  // print_r($conditionals);

  // conditional statement
  if(count($conditionals) > 0 && count($conditionals) % 2 == 0){
    $statement .= " WHERE";
    for ($i = 0; $i < count($conditionals); $i+=2) {
      $column = util::sanitize($conditionals[$i]);
      $match = util::sanitize($conditionals[$i+1]);

      if($i > 1){
        $statement .= " AND";
      }

      $statement .= " `{$tables[$entity]}`.`{$column}` LIKE \"%{$match}%\"";
    }
  }

  // close statement
  $statement .= ";";

  // echo $statement;
  $results = util::exec($statement);

  echo json_encode([
      "code" => 0,
      "message" => "query executed nicely",
      "entities" => $results
      // "statement" => $statement
    ]);
}, true);

/**
 * test data
 * username: cris
 * password: 1234
 */
Flight::route('POST /auth', function(){
  header("Content-type: text/json");
  $authenticated = false;

  $username = Flight::request()->data->username;
  $password = Flight::request()->data->password;
  $password = md5($password);

  $rows = util::exec("SELECT * FROM tbl_users WHERE `username`=:username AND `password`=:password;",
      [ "username" => $username, "password" => $password ]);

  if(count($rows)){
    $authenticated = true;
  }

  if(!$authenticated){
    echo json_encode(array(
      "code" => 1,
      "message" => "invalid username or password"
    ));
  }else{
    echo json_encode(array(
      "code" => 0,
      "message" => "login successful"
    ));
  }
});


Flight::start();
?>
