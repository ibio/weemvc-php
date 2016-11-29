<?php

class Pager{

  /**
   * $code: 
   * 0 - success
   * 1000 - core error
   * other - failed reason
   */
  public static function output($code, $data, $exception, $target = none){
    //TODO: dirname
    $where = null;
    if(isset($target)){
      $where = get_class($target);
    }
    $array = array(
      'code'      => intval($code), 
      'data'      => $data, 
      'exception' => $exception, 
      'where' => $where, 
      );
    echo json_encode($array);
  }

  public static function log($data, $path = './weemvc.log'){
    try {
      if(file_exists($path)) {
        $fp = fopen($path, 'a');
      }else{
        $fp = fopen($path, 'w');
      }
      // NOTICE: must use double quotation mark and \r\n together ...
      $str = var_export($data, true) . "\r\n";
      fwrite($fp, $str);
      fclose($fp);
    }catch(Exception $e) {
      self::output(1000, null, "log exception: {$e->getMessage()}", $this);
    }
  }

}