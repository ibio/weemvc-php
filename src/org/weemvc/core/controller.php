<?php
require_once './org/weemvc/pager.php';
require_once './org/weemvc/core/application.php';

class Controller{
  /**
   * @var null Database Connection
   */
  private $_db = null;

  public function __construct(){
    // NOTICE: do not do it automatically for every call
    // $this->openDatabaseConnection();
  }

  public function sendWee($commandName, $data){
    $command = $this->getCommand($commandName);
    if(isset($command)){
      $command->execute($data);
    }else{
      Pager::output(1000, null, "$commandName does not exist}", $this);
    }
  }

  public function prepareDatabase(){
    if(!isset($_db)){
      $this->openDatabaseConnection();
    }
  }

  /**
   * Load the model with the given name.
   * getDAO("SongModel") would include models/songmodel.php and create the object in the controller, like this:
   * $songs_model = $this->getDAO('SongsModel');
   * Note that the model class name is written in "CamelCase", the model's filename is the same in lowercase letters
   * @param string $modelName The name of the model
   * @return object model
   */
  public function getDAO($modelName){
    $model = null;
    $classPath = Application::$DAO_PATH . strtolower($modelName);
    $filePath = "{$classPath}.php";
    if(file_exists($filePath)){
      require_once $filePath;
      if(class_exists($modelName)){
        try{
          $model = new $modelName($this->_db);
        }catch(Exception $e) {
          Pager::output(1000, null, "new DAO exception: {$e->getMessage()}", $this);
          exit;
        }
      }
    }
    // return new model (and pass the database connection to the model)
    return $model;
  }

  public function getPlugin($pluginName){
    $plugin = null;
    $classPath = Application::$PLUGIN_PATH . strtolower($pluginName);
    $filePath = "{$classPath}.php";
    if(file_exists($filePath)){
      require_once $filePath;
      if(class_exists($pluginName)){
        try{
          $plugin = new $pluginName($this->_db);
        }catch(Exception $e) {
          Pager::output(1000, null, "new plugin exception: {$e->getMessage()}", $this);
          exit;
        }
      }
    }
    return $plugin;
  }

  private function getCommand($commandName){
    $command = null;
    $classPath = Application::$COMMAND_PATH . strtolower($commandName);
    $filePath = "{$classPath}.php";
    if (file_exists($filePath)) {
      require_once $filePath;
      if(class_exists($commandName)){
        try{
          $command = new $commandName();
        }catch(Exception $e) {
          Pager::output(1000, null, "new command exception: {$e->getMessage()}", $this);
          exit;
        }
      }
    }
    return $command;
  }
    
  /**
   * Open the database connection with the credentials from application/config/config.php
   */
  private function openDatabaseConnection(){
    // set the (optional) options of the PDO connection. in this case, we set the fetch mode to
    // "objects", which means all results will be objects, like this: $result->user_name !
    // For example, fetch mode FETCH_ASSOC would return results like this: $result["user_name] !
    // @see http://www.php.net/manual/en/pdostatement.fetch.php
    $options = array(
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, 
      PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING
    );

    // generate a database connection, using the PDO connector
    // @see http://net.tutsplus.com/tutorials/php/why-you-should-be-using-phps-pdo-for-database-access/
    try {
      $this->_db = new PDO(DB_TYPE . ':host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS, $options);
    } catch (Exception $e) {
      Pager::output(1000, null, "new PDO exception: {$e->getMessage()}", $this);
      exit;
    }  
  }

}
