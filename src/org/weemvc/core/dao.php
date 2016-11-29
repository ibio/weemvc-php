<?php
namespace org\weemvc\core;
use org\weemvc\Pager;
use org\weemvc\util\lx_externalinput_clean as InputCleaner;

//Abstract DAO
abstract class DAO{
  protected $tableName;
  //$key: field name; $value: type
  protected $fields;

  public function error(){
		$info = null;
		if(isset($this->db)){
			$info = $this->db->errorInfo();
		}
    return $info;
  }

	protected function assembleDateBase($db, $tableName, $fields){
    $this->tableName = $tableName;
    $this->fields = $fields;
		//
    try {
      $this->db = $db;
    } catch (PDOException $e) {
      Pager::output(1000, null, "new PDO exception: {$e->getMessage()}", $this);
      exit;
    }
	}

  protected function insert($data, $ignoreXSS = array()){
    $list = array();
    $values = array();
    //filter xss
    foreach ($data as $key => $value){
      //ignore XSS
      if(isset($ignoreXSS[$key])){
        $value = trim($value);
        $list[$key] = addcslashes($value, "\n, \r, \t, \$, \', \", \\");
      }else{
        $list[$key] = $this->filterXSS($value);
      }
    }
    $input = $this->formatInput($list);
    $sql = "INSERT INTO `" . DB_PREFIX . $this->tableName . "` (" . join(',', $input['fields']) . ") VALUES(" . join(",", $input['values']) . ")";
    $query = $this->db->prepare($sql);
    // fetchAll() is the PDO method that gets all result rows, here in object-style because we defined this in
    // libs/controller.php! If you prefer to get an associative array as the result, then do
    // $query->fetchAll(PDO::FETCH_ASSOC); or change libs/controller.php's PDO options to
    // $options = array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC ...
    $query->execute();
    return intval($this->db->lastInsertId());
  }

  protected function delete($where, $limit = 1){
    if(isset($where)){
      $where = ' WHERE ' . $where;
    }else{
      $where = '';
    }
    if(isset($limit)){
      $limit = ' LIMIT '. $limit;
    }else{
      $limit = '';
    }
    $sql = "DELETE FROM `".DB_PREFIX . $this->tableName . "`" . $where . $limit;
    $query = $this->db->prepare($sql);
    return $query->execute();
  }

  protected function query($fields = null, $where = null, $by = null, $start = null, $length = null){
    $result = null;
    if(!isset($fields)){
      $fields = '*';
    }
    if(isset($where)){
      $where = ' WHERE ' . $where;
    }else{
      $where = '';
    }
    if(isset($by)){
      $by = ' ORDER BY ' . $by;
    }else{
      $by = '';
    }
    if(isset($start) && isset($length)){
      $limit = ' LIMIT '. intval($start) . ',' . intval($length);
    }else if(isset($start)){
      $limit = ' LIMIT '. intval($start);
    }else if(isset($length)){
      $limit = ' LIMIT '. intval($length);
    }else{
      $limit = '';
    }
    $sql = "SELECT " . $fields . " FROM `".DB_PREFIX . $this->tableName . "`" . $where . $by . $limit;
    $query = $this->db->prepare($sql);
    $query->execute();
    if ($length == 1){
      $result = $this->formatOutput($query->fetch());
    }else{
      if($query->rowCount() > 0) {
        $result = array();
        foreach($query->fetchAll() as $row) {
          array_push($result, $this->formatOutput($row));
        }
      }
    }
    return $result;
  }

  protected function count($fields = null, $where = null){
    if(!isset($fields)){
      $fields = '*';
    }
    if(isset($where)){
      $where = ' WHERE ' . $where;
    }else{
      $where = '';
    }
    $sql = "SELECT COUNT(" . $fields . ") FROM `" . DB_PREFIX . $this->tableName . "`" . $where;
    $query = $this->db->prepare($sql);
    $query->execute();
    return  intval($query->fetchColumn());
  }

  protected function update($data, $where, $ignoreXSS = array()){
    $list = array();
    $sets = array();
    //filter xss
    foreach ($data as $key => $value){
      if(isset($ignoreXSS[$key])){
        $value = trim($value);
        $list[$key] = addcslashes($value, "\n, \r, \t, \$, \', \", \\");
      }else{
        $list[$key] = $this->filterXSS($value);
      }
    }
    foreach ($this->fields as $key => $value){
      if(isset($list[$key])){
        //type
        switch($value){
          //int types
          case 'INT':
          case 'TINYINT':
          case 'SMALLINT':
          case 'MEDIUMINT': 
          case 'BIGINT':
            array_push($sets, $key . '=' . intval($list[$key]));
            break;
          //float types
          case 'FLOAT':
          case 'DOUBLE':
          case 'DECIMAL':
            array_push($sets, $key . '=' . floatval($list[$key]));
            break;
          //Date and Time Types
          case 'DATE':
          case 'DATETIME':
          case 'TIMESTAMP':
          case 'TIME':
          case 'YEAR':
            array_push($sets, $key.'=now()');
            break;
          //String Types
          case 'CHAR':
          case 'VARCHAR':
          case 'BLOB':
          case 'TEXT':
          case 'TINYBLOB':
          case 'TINYTEXT':
          case 'MEDIUMBLOB':
          case 'MEDIUMTEXT':
          case 'LONGBLOB':
          case 'LONGTEXT':
          case 'ENUM':
            array_push($sets, $key."='".$list[$key]."'");
            break;
        }
      }
    }
    $sql = "UPDATE `" . DB_PREFIX . $this->tableName . "` SET " . join(',', $sets) . " WHERE " . $where;
    $query = $this->db->prepare($sql);
    return $query->execute();
  }

  protected function formatInput($list){
    $fields = array();
    $values = array();
    //$key: field name; $value: type
    foreach ($this->fields as $key => $value){
      if(isset($list[$key])){
        array_push($fields, "`" . $key . "`");
        //type
        switch($value){
          //int types
          case 'INT':
          case 'TINYINT':
          case 'SMALLINT':
          case 'MEDIUMINT': 
          case 'BIGINT':
            array_push($values, intval($list[$key]));
            break;
          //float types
          case 'FLOAT':
          case 'DOUBLE':
          case 'DECIMAL':
            array_push($values, floatval($list[$key]));
            break;
          //Date and Time Types
          case 'DATE':
          case 'DATETIME':
          case 'TIMESTAMP':
          case 'TIME':
          case 'YEAR':
            array_push($values, $list[$key]);
            break;
          //String Types
          case 'CHAR':
          case 'VARCHAR':
          case 'BLOB':
          case 'TEXT':
          case 'TINYBLOB':
          case 'TINYTEXT':
          case 'MEDIUMBLOB':
          case 'MEDIUMTEXT':
          case 'LONGBLOB':
          case 'LONGTEXT':
          case 'ENUM':
            array_push($values, "'" . $list[$key] . "'");
            break;
        }
      }
    }
    return array('fields' => $fields, 'values' => $values);
  }

  protected function formatOutput($row){
    $item = array();
    if(is_array($row)){
      foreach($row as $key => $value){
        //type
        switch($this->fields[$key]){
          //int types
          case 'INT':
          case 'TINYINT':
          case 'SMALLINT':
          case 'MEDIUMINT': 
          case 'BIGINT':
            $item[$key] = intval($value);
            break;
          //float types
          case 'FLOAT':
          case 'DOUBLE':
          case 'DECIMAL':
            $item[$key] = floatval($value);
            break;
          //Date and Time Types
          case 'DATE':
          case 'DATETIME':
          case 'TIMESTAMP':
          case 'TIME':
          case 'YEAR':
            $item[$key] = $value;
            break;
          //String Types
          case 'CHAR':
          case 'VARCHAR':
          case 'BLOB':
          case 'TEXT':
          case 'TINYBLOB':
          case 'TINYTEXT':
          case 'MEDIUMBLOB':
          case 'MEDIUMTEXT':
          case 'LONGBLOB':
          case 'LONGTEXT':
          case 'ENUM':
            $item[$key] = $value;
            break;
        }
      }
    }
    return $item;
  }

  protected function filterXSS($str){
    $str = trim($str);
    $str = addcslashes($str, "\n, \r, \t, \$, \', \", \\");
    return InputCleaner::basic($str);
  }
}
