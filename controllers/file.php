<?php
/**!
 * @package   ReceiptAPI
 * @filename  file.php
 * @version   1.0
 * @autor     Díaz Urbaneja Víctor Eduardo Diex <diazvictor@tutamail.com>
 * @date      21.11.2023 22:13:17 -04
 */

class file_controller extends controller {

  public function execute() {
    $parameters = ($this->router->parameters) ? $this->router->parameters : false;
    $allowed = ['view','get'];

    if (in_array($parameters[0], $allowed) ) {
      if ($parameters[0] === 'view') {
        if (isset($parameters[1])) {
          $this->model->view($parameters);
          return;
        }
      }

      if ($parameters[0] === 'get') {
          if(isset($parameters[1])){
          $this->model->get($parameters[1]);
          return;
        }
      }
    }

    header('Content-Type: application/json; charset=utf-8');
    $this->model->notFound();
  }

}
