<?php
namespace application\router;
use org\weemvc\core\Router as Router;
use org\weemvc\Pager as Pager;
// require_once './org/weemvc/core/router.php';
// require_once './org/weemvc/pager.php';

class Announce extends Router{

  public function save(){
    $userModel = $this->_controller->getDAO('UserModel');
     if(!isset($_POST['title']) || !isset($_POST['link'])){
      echo json_encode(array('ret' => 0, 'message' => 'title or link is null'));
      Pager::output(998, 'title or link is null', $model->error(), $this);

    }else if(!$userModel->checkSession()){
      echo json_encode(array('ret' => 0, 'message' => 'you need to login'));
      Pager::output(998, 'you need to login', $model->error(), $this);

    }else{
      //get
      $model = $this->_controller->getDAO('AnnounceModel');
      if($_POST['id'] >= 0){
        $result = $model->updateById($_POST['id'],$_POST['title'],$_POST['products'],$_POST['description'],$_POST['link'],$_POST['forTotal'],$_POST['forQuantity'],$_POST['images']);
          $data = array('isNew' => false, 'id' => intval($_POST['id']));
      }else{
        $result = $model->add($_POST['title'],$_POST['products'],$_POST['description'],$_POST['link'],$_POST['forTotal'],$_POST['forQuantity'],$_POST['images']);
        $data = array('isNew' => true, 'id' => $result);
      }
      Pager::output($result ? 0 : 999, $data, $model->error(), $this);
    }
  }

  public function delete(){
    $userModel = $this->_controller->getDAO('UserModel');
    if(!isset($_POST['id'])){
      echo json_encode(array('ret' => 0, 'message' => 'id is null'));
    }else if(!$userModel->checkSession()){
      echo json_encode(array('ret' => 0, 'message' => 'you need to login'));
    }else{
      //get
      $model = $this->_controller->getDAO('AnnounceModel');
      $result = $model->deleteById($_POST['id']);
      $obj = array('id' => $_POST['id']);
      Pager::output($result ? 0 : 999, $obj, $model->error(), $this);
    }
  }

  public function get(){
    $this->_controller->prepareDatabase();
    // var_dump($_GET);
    // var_dump($_POST);
    //get
    $model = $this->_controller->getDAO('AnnounceModel');
    $list = $model->get();
    Pager::output(0, $list, $model->error(), $this);
  }
}
