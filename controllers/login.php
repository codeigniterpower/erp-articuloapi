<?php

/*Controlador del modulo entrada*/

class login_controller extends controller {
  public function execute() {
    header("Status: 401");
    header('Content-Type: application/json; charset=utf-8');
    print( json_encode(['msg'=>'Invalid credentials'])   );
  }
}
