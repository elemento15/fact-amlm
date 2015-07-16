<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class BaseController extends CI_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->model($this->modelName,'model',true);
  }

  public function read() {
    $params = array(
      'order'  => isset($_POST['sort'])   ? $_POST['sort'] : null,
      'start'  => isset($_POST['start'])  ? $_POST['start']  : 0,
      'length' => isset($_POST['length']) ? $_POST['length'] : 0,
      'search' => isset($_POST['search']) ? $_POST['search']['value'] : null,
      'filter' => isset($_POST['filter']) ? $_POST['filter'] : array()
    );

    $recs = $this->model->findAll($params);
    echo json_encode(array(
      'recordsFiltered' => $recs['filtered'],
      'recordsTotal'    => $recs['total'],
      'data'            => $recs['data']
    ));
  }

  public function create() {
    $data = $this->model->create();
    if ($data) {
      $resp = array('success' => true, 'data' => $data);
    } else {
      $resp = array('success' => false, 'msg' => $this->model->getError());
    }
    echo json_encode($resp);
  }

  public function find() {
    $id = $_POST['id'];

    $data = $this->model->find($id);
    if ($data) {
      $resp = array('success' => true, 'data' => $data);
    } else {
      $resp = array('success' => false, 'msg' => $this->model->getError());
    }
    echo json_encode($resp);
  }

  public function save() {
    if (!$id = $this->model->save($_POST['data'])) {
      $resp = array('success' => false, 'msg' => $this->model->getError());
    } else {
      $resp = array('success' => true, 'id' => $id);
    }
    echo json_encode($resp);
  }

  public function remove() {
    $id = $_POST['id'];
    if (!$this->model->remove($id)) {
      $resp = array('success' => false, 'msg' => $this->model->getError());
    } else {
      $resp = array('success' => true);
    }
    echo json_encode($resp);
  }

}

?>