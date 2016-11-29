<?php
namespace application\router;
use org\weemvc\core\Router as Router;
use org\weemvc\Pager as Pager;
// require_once './org/weemvc/core/router.php';
// require_once './org/weemvc/pager.php';

class Announce extends Router{

  public function save($get, $post){
    $userModel = $this->_controller->getDAO('UserModel');
     if(!isset($post['title']) || !isset($post['link'])){
      echo json_encode(array('ret' => 0, 'message' => 'title or link is null'));
      Pager::output(998, 'title or link is null', $model->error(), $this);

    }else if(!$userModel->checkSession()){
      echo json_encode(array('ret' => 0, 'message' => 'you need to login'));
      Pager::output(998, 'you need to login', $model->error(), $this);

    }else{
      //get
      $model = $this->_controller->getDAO('AnnounceModel');
      if($post['id'] >= 0){
        $result = $model->updateById($post['id'],$post['title'],$post['products'],$post['description'],$post['link'],$post['forTotal'],$post['forQuantity'],$post['images']);
          $data = array('isNew' => false, 'id' => intval($post['id']));
      }else{
        $result = $model->add($post['title'],$post['products'],$post['description'],$post['link'],$post['forTotal'],$post['forQuantity'],$post['images']);
        $data = array('isNew' => true, 'id' => $result);
      }
      Pager::output($result ? 0 : 999, $data, $model->error(), $this);
    }
  }

  public function delete($get, $post){
    $userModel = $this->_controller->getDAO('UserModel');
    if(!isset($post['id'])){
      echo json_encode(array('ret' => 0, 'message' => 'id is null'));
    }else if(!$userModel->checkSession()){
      echo json_encode(array('ret' => 0, 'message' => 'you need to login'));
    }else{
      //get
      $model = $this->_controller->getDAO('AnnounceModel');
      $result = $model->deleteById($post['id']);
      $obj = array('id' => $post['id']);
      Pager::output($result ? 0 : 999, $obj, $model->error(), $this);
    }
  }

  public function get($get, $post){
    $this->_controller->prepareDatabase();
    // var_dump($get);
    // var_dump($post);
    //get
    $model = $this->_controller->getDAO('AnnounceModel');
    $list = $model->get();
    Pager::output(0, $list, $model->error(), $this);
  }
}
