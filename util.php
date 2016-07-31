<?php
class util{
  static $db_prepared = false;
  static $pdo = null;

  static function prepare(){
    if(self::$db_prepared){
      return;
    }

    self::$db_prepared = true;

    $host = '127.0.0.1';
    $db   = 'db_api';
    $user = 'root';
    $pass = '';
    $charset = 'utf8';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $opt = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    self::$pdo = new PDO($dsn, $user, $pass, $opt);
  }

  static function exec($statement, $params = []){
    self::prepare();

    $stmt = self::$pdo->prepare($statement);
    $stmt->execute($params);

    $rows = $stmt->fetchAll();

    return $rows;
  }

  static function sanitize($string){
    // $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

    return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
  
    // return $sanitized;
  }
}