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

abstract class DbConnection implements IConnectDb{
  protected $host;
  protected $dbName;
  protected $username;
  protected $password;
}

class MySqlConnection extends DbConnection{
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
    return "INSERTING >>> "
  }
}

class DbManager{
  public static function insert(DbObject $objInsertable){
    $connection = MySqlConnection::getInstance();
    $fields=$objInsertable->getInsertFields();
    echo self::makeSQLInsertQuery($objInsertable, $connection);
  }

  public static function makeSQLInsertQuery(DbObject $objInsertable, DbConnection $connection): string{
    $fields=$objInsertable->getInsertFields();
    $fieldsParams = array();

    foreach($fields as $field=>$value){
      array_push($fieldsParams, $field);
    }

    $strFieldsParams = implode(", ", $fieldsParams);
    $fieldsParams = array_map(function($field){
      return ":$field";
    }, $fieldsParams);

    $strFieldsParamsV = implode(", ", $fieldsParams);
    
    $result = "INSERT INTO $objInsertable->table($strFieldsParams) VALUES ($strFieldsParamsV);";

    return $connection->execute($result, $fields);
  }
}

abstract class DbObject implements IActionInsert, IActionList{
  public $table;

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

$producto = new Producto();
$producto->id = 1;
$producto->nombre = "Lavadora";

DbManager::insert($producto);

$categoria = new Categoria();
$categoria->id = 1;
$categoria->nombre = "Linea blanca";

DbManager::insert($categoria);
