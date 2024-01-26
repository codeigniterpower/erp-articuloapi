<?php
/**!
 * @package   ReceiptAPI
 * @filename  upload.php
 * @version   1.0
 * @autor     Díaz Urbaneja Víctor Eduardo Diex <diazvictor@tutamail.com>
 * @date      22.11.2023 01:03:17 -04
 */

class api_v1_upload_model extends model {

  private $sql;
  private $err;

  public function notFound() {
    $this->borrow('notFound')->show();
  }

  /**
   * Crea el directorio en el cloud
   * @return string
   */
  private function create_cloud_dir() {
    $dir = sprintf('%scloud/%s/', ROOT, date('Y'));
    mkdir($dir, 0777, true); // TODO: este framework no maneja la cantidad de errores por getion de permisos
    return $dir ;
  }

  /**
   * Decodifica un base64 y lo guarda en el filesystem
  */
  private function save_file($b64,$filepath) {
    $ifp = fopen( $filepath, 'wb' ); // excelente truco
    $data = explode(',', $b64);
    if (isset($data[1])){
      fwrite( $ifp, base64_decode($data[1]));
    } else {
      fwrite( $ifp, base64_decode($data[0]));
    }
    fclose($ifp);
  }

  /**
   * Crea el ID loco de PICCORO posta no es loco.. con esto identificas varias cosas, fecha, donde cuando 
   *    ah y de paso se ordena solo ya que nunca dara unnumero menor a menos este el sistema trampeado
   * @return string YYYYMMDDHHmmss
   */
  private function mkid() {
    return date('YmdHis');
  }

  private function save_ok($post) {
    $result = true; /*esto es true a menos que algo salga mal*/
    $validator   = new validator(); /*inicializo la clase de validacion*/
    /*los campos a validar en este arreglo, creo que se explica solos*/
    $validations = [
      'cod_item' => [
        'type'      => 'string',
        "required"  => true
      ],
      'cod_items_description' => [
        'type'      => 'string',
        "required"  => true
      ],
      'cod_tipo' => [
        'type'      => 'string',
        "required"  => true
      ],
      'is_set' => [
        'type'      => 'enum',
        'values'    => ['0', '1'],
        "required"  => true
      ],
      'is_available' => [
        'type'      => 'enum',
        'values'    => ['0', '1'],
        "required"  => true
      ],
      'is_managed' => [
        'type'      => 'enum',
        'values'    => ['0', '1'],
        "required"  => true
      ],
      'is_activo' => [
        'type'      => 'enum',
        'values'    => ['0', '1'],
        "required"  => true
      ],
      'fecha_manufactura' => [
        'type'      => 'string',
        "required"  => true
      ],
      'pic_item_bin_main' => [
        'type'      => 'string',
        "required"  => false
      ]
    ];

    $keys = [
      "cod_item",
      "cod_items_description",
      "is_managed",
      "fecha_manufactura",
    ];
    /*esto es una edicion por que cod_item existe*/
    if (isset($post['cod_item'])) {
      $validations = $validations + [
        'cod_item' => [
          'type'         => 'string',
          'required'     => true,
          'maxlen'       => 80,
          'minlen'       => 1,
        ]
      ];
      $exist = $this->db->exist(
        'apiobj_items', $keys,
        $post, 'cod_item', $post['cod_item']
      );
    } else {
      $exist = $this->db->exist(
        'apiobj_items', $keys, $post
      );
    }

    if ($exist) {
      $this->err = "El campo " . $exist . " ya existe.";
      $result = false;
    }

    /*hago todas las validaciones*/
    $check = $validator->execute($validations);
    if ($check[0] === false ) {
      $this->err = $check[1];
      $result = false;
    }

    return $result;
  }

  public function save($post) {
    $update = false;
    $this->err  = false;
    $permission = $this->auth->getPermission('upload', $_SESSION['id_user']);
    header('Content-Type: application/json; charset=utf-8');

    if (isset($post['cod_item']) and intval($post['cod_item'])) {
      $update = true;
      if (is_false($permission['update'])){
        $this->err = 'Sin permisos para actualizar';
      }
    } else {
      if (is_false($permission['write'])) {
        $this->err = 'Sin permisos de escritura';
      }
    }

    if ($this->save_ok($post) === false || $this->err) {
      print(json_encode([
        'ok'        => false,
        'msg'       => $this->err,
        'cod_item' => false
      ]));
      return;
    }

    /*los campos a guardar*/
    $keys = [
      "cod_item",
      "cod_items_description",
      "cod_tipo",
      "is_available",
      "is_managed",
      "is_activo",
      "fecha_manufactura",
      "fecha_remocion"
    ]; // @TODO: No se que son los *flags*

    /*inicio la transaccion*/
    if ($this->db->query("begin") == false) {
      $this->notFound();
      return false;
    }

    if ($update) {
      $cod_item = $post['cod_item'];
      /*hago el update*/
      if ($this->db->update("apirec_recibo", $cod_item, 'cod_item', $post, $keys) === false) {
        $this->db->query("rollback");
        $this->notFound();
        return false;
      }
    } else {
      $post["cod_item"] = $this->mkid();
      /*hago el insert*/
      if ($this->db->insert("apirec_recibo", $post, $keys) === false) {
        $this->db->query("rollback");
        $this->notFound();
        return false;
      }

      $cod_item = $post["cod_item"];
    }

    if (!empty($post["pic_item_bin_main"])) {
      $this->db->delete('apiobj_items_picture_main', 'cod_item', $cod_item);
      $updir = $this->create_cloud_dir();
      $filepath = $updir .  $cod_item;

      $keys = ["cod_item", "pic_item_bin_main", "pic_item_path_main"];
      $values = [
        "cod_item" => $cod_item,
        "pic_item_bin_main"   => $post["pic_item_bin_main"],
        "pic_item_path_main"      => $filepath
      ];

      if ($this->db->insert("apiobj_items_picture_main", $values, $keys) === false) {
        $this->db->query("rollback");
        $this->notFound();
        return false;
      }

      $this->save_file($post["pic_item_bin_main"], $filepath);
    }

    /*finalmente hago el commit y retorno*/
    if ($this->db->query("commit") != false) {
      print(json_encode([
        'ok'        => true,
        'msg'       => 'Guardado con exito!',
        'cod_item' => $cod_item
      ]));
      return;
    }

    print(json_encode([
      'ok'        => false,
      'msg'       => 'Err: Will Robinson!',
      'cod_item' => false
    ]));
  }

}
