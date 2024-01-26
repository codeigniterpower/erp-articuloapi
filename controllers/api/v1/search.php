<?php
/**!
 * @package   ReceiptAPI
 * @filename  search.php
 * @version   1.0
 * @autor     Díaz Urbaneja Víctor Eduardo Diex <diazvictor@tutamail.com>
 * @date      23.11.2023 21:14:35 -04
 */

class api_v1_search_controller extends controller {

  public function execute() {
    /*esta variable recoje los parametros de la url,
     *es un array y el indice 0 indica el primer parametro*/
    $parameters = ($this->router->parameters) ? $this->router->parameters : false;

    if ($parameters and  $parameters[0]) {
      if (isset($parameters[1]) and  $parameters[1] == 'page' and
          valid_input($parameters[2], VALIDATE_NUMBERS, VALIDATE_NONEMPTY)
        ) {
        $this->model->get($parameters[0], $parameters[2]);
        return;
      }

      $this->model->get($parameters[0]);
      return;
    }
    $this->model->notFound();
  }

}
