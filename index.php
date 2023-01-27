<?php

/* Welcome to phplayground!
 This playground allows you to try out PHP from your browser.
 It is a free tool, please don't break it! 😊 */

interface IActionInsert{
  function getInsertFields(): array;
}

interface IActionList{
  function listAll();
  function filter(string $query, int $start, int $limit);
}

interface IConnectDb{
  function connect();
  function disconnect();
  function execute(string $sqlQuery, array $params);
}

interface ISqlInsert{
  function makeSqlInsert(array $fields, string $table);
}

abstract class DbConnection implements IConnectDb{
  protected $host;
  protected $dbName;
  protected $username;
  protected $password;
}

class MySqlConnection extends DbConnection implements ISqlInsert{
  public static $instance;

  public static function getInstance(){
    if(self::$instance == null)
      self::$instance = new MySqlConnection();
    return self::$instance;
  }
  
  private function _construct(){
    $this->host = "127.0.0.1";
    $this->dbName = "database";
    $this->username = "username";
    $this->password = "password";
  }
  
  function connect(){
    echo "CONNECTED TO $this->host";
  }

  function disconnect(){
    echo "DISCONNECTED TO $this->host";
  }

  function execute(string $sqlQuery, array $params = array()){
    return "EXECUTING >>>> $sqlQuery\n";
  }

  function insert(DbObject $object){
    return "INSERTING >>> ";
  }

  function makeSqlInsert(array $fields, string $table){
    $strFieldsParams = implode(", ", $fields);
    $fieldsParams = array_map(function($field){
      return ":$field";
    }, $fields);

    $strFieldsParamsV = implode(", ", $fieldsParams);
    
    $result = "INSERT INTO $table($strFieldsParams) VALUES ($strFieldsParamsV);";

    return $result;
  }
}

class DbManager{
  public static function insert(DbObject $objInsertable, DbConnection $connection){
    $fields=$objInsertable->getInsertFields();
    $sqlQuery = $connection->makeSqlInsert(self::getFields($objInsertable), $objInsertable->getTableName());
    return $connection->execute($sqlQuery, $fields);
  }

  public static function getFields(DbObject $objInsertable): array{
    $fields=$objInsertable->getInsertFields();
    $fieldsParams = array();

    foreach($fields as $field=>$value){
      array_push($fieldsParams, $field);
    }

    return $fieldsParams;
  }
}

abstract class DbObject implements IActionInsert, IActionList{
  public $table;

  public function getTableName(){
    return $this->table;
  }

  public function __construct($table){
    $this->table = $table;
  }

  public function listAll(){
    
  }

  public function filter(string $query, int $start, int $limit){
    
  }
}

class Producto extends DbObject{
  public $id;
  public $descripcion;
  
  public function __construct(){
    parent::__construct("Producto");
  }

  public function getInsertFields(): array{
    return array(
      "id" => $this->id,
      "descripcion" => $this->descripcion
    );
  }
}

class Categoria extends DbObject{
  public $id;
  public $nombre;
  
  public function __construct(){
    parent::__construct("Categoria");
  }

  public function getInsertFields(): array{
    return array(
      "id" => $this->id,
      "nombre" => $this->nombre
    );
  }
}

$connection = MySqlConnection::getInstance();

$producto = new Producto();
$producto->id = 1;
$producto->nombre = "Lavadora";

echo DbManager::insert($producto, $connection);

$categoria = new Categoria();
$categoria->id = 1;
$categoria->nombre = "Linea blanca";

echo DbManager::insert($categoria, $connection);
