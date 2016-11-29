<?php
namespace application\model\dao;
use org\weemvc\core\DAO;

class AnnounceModel extends DAO{
  /**
   * Every model needs a database connection, passed to the model
   * @param object $db A PDO database connection
   */
  function __construct($db) {
    $fields = array(
      'id' => 'INT',
      'title' => 'VARCHAR',
      'products' => 'VARCHAR',
      'description' => 'TEXT',
      'link' => 'VARCHAR',
      'for_total' => 'INT',
      'for_quantity' => 'INT',
      'images' => 'VARCHAR',
      'date' => 'DATETIME'
    );
    $this->assembleDateBase($db, 'announce', $fields);
  }

  public function add($title, $products, $description, $link, $forTotal, $forQuantity, $images){
    $fields = array(
      'title' => $title,
      'products' => $products,
      'description' => $description,
      'link' => $link,
      'for_total' => $forTotal,
      'for_quantity' => $forQuantity,
      'images' => $images,
      'date' => 'now()'
    );
    return $this->insert($fields, array('link' => true));
  }

  public function updateById($id, $title, $products, $description, $link, $forTotal, $forQuantity, $images){
    $id = $this->filterXSS($id);
    $fields = array(
      'title' => $title,
      'products' => $products,
      'description' => $description,
      'link' => $link,
      'for_total' => $forTotal,
      'for_quantity' => $forQuantity,
      'images' => $images
    );
    return $this->update($fields, '`id`=' . $id, array('link' => true));
  }

  public function get(){
    return $this->getItems($this->query('*', null, '`id` DESC'));
  }

  public function deleteById($id){
    $id = $this->filterXSS($id);
    return $this->delete('`id`=' . $id);
  }

  protected function getItems($list){
    $result = array();
    foreach($list as $row) {
      $item = $row;
      if(isset($row['for_total'])){
          $item['forTotal'] = $row['for_total'];
          unset($row['for_total']);
      }
      if(isset($row['for_quantity'])){
          $item['forQuantity'] = $row['for_quantity'];
          unset($row['for_quantity']);
      }
      array_push($result, $item);
    }
    return $result;
  }

}
