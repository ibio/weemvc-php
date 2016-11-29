<?php
namespace application\command;
use org\weemvc\core\Command;

class IntervalTask extends Command{

  public function execute($file){
    if($this->checkInterval($file)){
      // $this->updateCheckTime($file);
      $queueTask = $this->_controller->getPlugin('QueueTask');
      // $result = $queueTask->getEnabledItems();
      // if(is_array($result)){
      //   $queueTask->consumeItems($result);
      //   foreach($result as $item){
      //     // send command
      //     $this->_controller->sendWee($item['command'], json_decode($item['arguments'], true));
      //   }
      // }
    }
  }

  protected function checkInterval($file){
  	// $handle = fopen($file, 'r');
	  // $time = fread($handle, filesize($file));
	  // fclose($handle);
	  // $time = intval($time);
	  // return time() >= ($time + CHECK_INTERVAL_TASK_TIME);
    return true;
  }

  protected function updateCheckTime($file){
  	$handle = fopen($file, 'w');
	  fwrite($handle, time());
	  fclose($handle);
  }
}
