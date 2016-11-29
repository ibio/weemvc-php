<?php
namespace org\weemvc\core;
use org\weemvc\Pager;
/**
 * Base command class
 */
abstract class Command{
  // why protected? http://php.net/manual/en/language.oop5.visibility.php
  protected $_controller;

  /**
   * Whenever a controller is created, open a database connection too. 
   * The idea behind is to have ONE connection that can be used by 
   * multiple models (there are frameworks that open one connection per model).
   */
  public function __construct(){
    $app = Application::getInstance();
    $this->_controller = $app->getController();
  }

  public function execute($data){
    Pager::output(1002, null, 'default/execute', $this);
  }

}
