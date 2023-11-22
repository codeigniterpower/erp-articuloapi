<?php



class logout_controller extends controller {
  public function execute() {
    header('Content-Type: application/json; charset=utf-8');
    if ($this->auth->isLogged()) {
        if($this->auth->logOut()){
          print(json_encode(['msg'=>'Ciao']));
        }
    } else {
      print(json_encode(['msg'=>'Usted no esta logeado']));
    }
  }
}
