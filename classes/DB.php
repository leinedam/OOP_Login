<?php

class DB{

  // an static method is going to store an
  // instance of the database, so instead of creating a connection
  // on each page when we need to access the DB we only use one instance,
  // this is way more efficient
  private static $_instance = null;
  private $_pdo, //store the pdo
          $_query, //last query executed
          $_error = false,
          $_results,
          $_count = 0; // count of the results, if there are any


  //DB constructor function
  private function __construct(){
    try{
      $this->_pdo =  new PDO('mysql:host=' . Config::get('mysql/host') . ';dbname='. Config::get('mysql/db') , Config::get('mysql/username') , Config::get('mysql/password') );
    //  echo 'Connected';
    }catch(PDOException $e){
      die($e->getMessage());
    }
  }

  public static function getInstance(){
    if(!isset(self::$_instance)){
      self::$_instance = new DB();
    }
    return self::$_instance;
  }

  public function query($sql, $params = array()){
    $this->_error = false; // we need to reset the _error in order to prevent displaying an error from a previous query
    if($this->_query = $this->_pdo->prepare($sql)){
      //echo 'Success';
      //store the query
      $x = 1;
      if(count($params)){
        foreach($params as $param){
          $this->_query->bindValue($x, $param); // asign the value of first param to $x
          $x++;
          }
       }
        //now we execute the stored query
        if($this->_query->execute()){
          //echo 'Success';
          $this->_results = $this->_query->fetchAll(PDO::FETCH_OBJ);
          $this->_count = $this->_query->rowCount();
        }else{
          $this->_error = true;
        }
    }
    return $this;
  }

  // helper functions to speed up querys
  public function action($action, $table, $where = array()){

    if(count($where) === 3){
      $operators = array('=','>','<','>=','<=');

      $field    = $where[0];
      $operator = $where[1];
      $value    = $where[2];

      if(in_array($operator, $operators)){
        //$sql = "SELECT * FROM users WHERE username = 'Alex'";
        $sql = "{$action} FROM {$table} WHERE {$field} {$operator} ? ";

        if(!$this->query($sql, array($value))->error()){
          return $this;
        }
      }
    }
    return false;
  }

  public function get($table, $where){

    return $this->action('SELECT *', $table, $where);

  }

  public function delete($table, $where){

      return $this->action('DELETE', $table, $where);

  }

 // insert data in DB
  public function insert($table, $fields = array()){

      $keys = array_keys($fields);
      $values = null;
      $x = 1;

      foreach($fields as $field){
        $values .= '?';
        if($x < count($fields)){
          $values .= ', ';
        }
        $x++;
      }

      $sql = "INSERT INTO {$table}(`" . implode('`, `', $keys) . "`) VALUES ({$values})";

      if(!$this->query($sql, $fields)->error()){
        return true;
      }

    return false;
  }

  public function update($table, $id, $fields){
    $set = '';
    $x = 1;

    foreach($fields as $name => $value ){
      $set .= "{$name} = ?";
      if($x < count($fields)){
        $set .= ', ';
      }
      $x++;
    }

    $sql = "UPDATE {$table} SET {$set}  WHERE id = {$id}";

    if(!$this->query($sql, $fields)->error()){
      return true;
    }
    return false;

  }


  public function results(){
    return $this->_results;
  }

  //funtcion to return only the first result
  public function first(){
    return $this->results()[0];
  }

  public function error(){
    return $this->_error;
  }

  public function count(){
    return $this->_count;
  }

}
