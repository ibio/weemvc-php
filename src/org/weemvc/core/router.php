<?php
namespace org\weemvc\core;
use org\weemvc\Pager;

/**
 * Router class
 * Every router in the 'router/' should extend this one.
 */
class Router extends Command{

  /**
   * Whenever a controller is created, uses $controller->prepareDatabase(); to
   * open a database connection too. 
   * The idea behind is to have ONE connection that can be used by 
   * multiple models (there are frameworks that open one connection per model).
   */

  // default
  public function index($get, $post){
    Pager::output(1001, null, 'router/index', $this);
  }

}
