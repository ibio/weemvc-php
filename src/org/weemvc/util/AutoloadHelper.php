<?php
namespace org\weemvc\util;

class AutoloadHelper {

  public static function register(){
    spl_autoload_register(function ($path) {
      // http://stackoverflow.com/questions/11255095/cannot-access-self-when-no-class-scope-is-active
      // why not self:: --> because this is a closure
      $class = AutoloadHelper::getPathFromNamespace($path);
      // http://stackoverflow.com/questions/22147261/how-to-prevent-spl-autoload-register-from-loading-native-php-classes      
      if(file_exists($class)){
        require_once($class);
      }
    });
  }

  public static function getPathFromNamespace($name){
    // TODO: you can extend this in sub class
    return './' . str_replace('\\', '/', $name) . '.php';
  }

}

// register
AutoloadHelper::register();