<?php
/**!
 * @package   ReceiptAPI
 * @filename  upload.php
 * @version   1.0
 * @autor     Díaz Urbaneja Víctor Eduardo Diex <diazvictor@tutamail.com>
 * @date      22.11.2023 01:03:17 -04
 */

class api_v1_upload_controller extends controller {

  public function execute() {
    /*esta variable recoje los parametros de la url,
     *es un array y el indice 0 indica el primer parametro*/
    $parameters = ($this->router->parameters) ? $this->router->parameters : false;

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $this->model->save($_POST);
      return;
    }

    $this->model->notFound();
  }

}
