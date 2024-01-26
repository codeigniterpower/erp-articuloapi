<?php
/**!
 * @package   ReceiptAPI
 * @filename  lists.php
 * @version   1.0
 * @autor     Díaz Urbaneja Víctor Eduardo Diex <diazvictor@tutamail.com>
 * @date      22.11.2023 02:33:35 -04
 */

class api_v1_lists_controller extends controller {

  public function execute() {
    /*esta variable recoje los parametros de la url,
     *es un array y el indice 0 indica el primer parametro*/
    $parameters = ($this->router->parameters) ? $this->router->parameters : false;

    if (
      $parameters[0] and $parameters[0] == 'page' and
      valid_input($parameters[1], VALIDATE_NUMBERS, VALIDATE_NONEMPTY)
    ) {
      $this->model->get($parameters[1]);
      return;
    }

    $this->model->get();
  }

}
