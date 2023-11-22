<?php
/**!
 * @package   ReceiptAPI
 * @filename  file.php
 * @version   1.0
 * @autor     Díaz Urbaneja Víctor Eduardo Diex <diazvictor@tutamail.com>
 * @date      21.11.2023 22:13:17 -04
 */

class file_model extends model {

  private $sql;
  private $err;

  public function notFound() {
    $this->borrow('notFound')->show();
  }

  public function view($get){
    $this->sql = "select * from apirec_recibo_adjunto where id_recibo=%d";
    if ( ($file = $this->db->execute($this->sql, $get[1])) === false) {
      $this->view->add_message( $this->db->error[2] );
    }

    header('X-Powered-By: Guachi Cloud');
    $path = $file[0]['ruta'];
    $attachment = $path;

    if ( isset($path) and is_file($attachment) === true ) {
      header('Content-Type: ' . mime_content_type($attachment));
      header('Expires: 0');
      header('Cache-Control: must-revalidate');
      header('Pragma: public');
      readfile($attachment);
      return;
    }

    $this->notFound();
    return false;
  }

  public function get($name_in_system) {
    $this->sql = "select * from apirec_recibo_adjunto where id_recibo=%d";
    if ( ($file = $this->db->execute($this->sql, $name_in_system)) === false) {
      $this->view->add_message( $this->db->error[2] );
    }

    header('X-Powered-By: Guachi Cloud');
    $path = $file[0]['ruta'];
    $attachment = $path;

    if ( isset($path) and is_file($attachment) === true ) {
      header('Content-Type: ' . mime_content_type($attachment));
      header('Content-Disposition: attachment; filename="'.$namefile.'"' );
      header('Expires: 0');
      header('Cache-Control: must-revalidate');
      header('Pragma: public');
      header('Content-Length: ' . filesize($attachment) );
      readfile($attachment);
      return;
    }

    $this->notFound();
    return false;
  }

}
