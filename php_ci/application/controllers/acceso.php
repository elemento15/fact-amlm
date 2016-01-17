<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Acceso extends CI_Controller {

  public function __construct() {
    parent::__construct();
  }

  public function login() {
    $user = $_POST['user'];
    $pass = $_POST['pass'];

    if ($user == 'alomeli' && $pass == 'clase1975') {
      session_start();
      $_SESSION['username'] = $user;

      $response = array('success' => true);
    } else {
      $response = array('success' => false, 'msg' => 'Usuario o password incorrecto');
    }

    echo json_encode($response);
  }

  public function logout() {
    session_start();
    session_destroy();

    echo json_encode(array('success' => true));
  }

}

?>