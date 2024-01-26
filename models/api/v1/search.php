<?php
/**!
 * @package   ReceiptAPI
 * @filename  search.php
 * @version   1.0
 * @autor     PICCORO Lenz MCKAY <mckaygerhard@gmail.com>
 * @date      20.01.2024 21:14:35 -04
 */

class api_v1_search_model extends model {

  private $sql;
  private $err;

  public function notFound() {
    $this->borrow('notFound')->show();
  }

  public function get($search = NULL, $page = 1) {
    $where = ' where 1=1  and '
              .'(' /* this filter is bad, the or and and are mutually exclusive */
              .' cod_item like %s or '
              .' des_items_description like %s or '
              .')';
    $and = ' ';
    $s = "%$search%";
    $values = [$s,$s,$s,$s];
    $limit = 5;
    if($search)
    $this->sql = 'SELECT 
                        count(cod_item) AS total
                    FROM 
                        apiobj_items
                '.$where.$and;
    $records = intval($this->db->execute($this->sql,$values)[0]['total']);
    $this->sql = 'SELECT 
                        count(cod_item) AS total
                    FROM 
                        apiobj_items_set
                '.$where.$and;
    $records += intval($this->db->execute($this->sql,$values)[0]['total']);
    $page = intval($page);
    $offset = ($limit * ($page - 1));
    $pages = ceil ($records / $limit);
    $pagination = [
      "total_records" => $records,
      "total_pages"   => $pages,
      "per_page"      => $limit,
      "current_page"  => $page,
      "next_page"     => ($records == 0) ? 1 : ($page + 1),
      "prev_page"     => ($records == 0) ? 1 : ($page - 1)
    ];
    $return = TRUE;

    $this->sql = '
                SELECT
                    i.cod_item as cod_item,
                    i.cod_items_description,
                    (SELECT d.des_items_description LIMIT 1 OFFSET 0) as des_items_description,
                    i.cod_tipo,
                    0 as is_set,
                    COALESCE(TRUE, i.is_available) as is_available,
                    COALESCE(TRUE, i.is_managed) as is_managed,
                    COALESCE(TRUE, i.is_activo) as is_activo,
                    i.fecha_manufactura,
                    i.fecha_remocion,
                    p.pic_item_bin_main,
                    p.pic_item_path_main,
                    p.pic_item_data_main
                FROM apiobj_items AS i
                    LEFT JOIN apiobj_items_description AS d
                    on d.cod_items_description = i.cod_items_description
                    left join apiobj_items_picture_main AS p
                    on p.cod_item = i.cod_item
                    WHERE 1=1 '. $where .  $and .'
                UNION
                SELECT 
                    s.cod_set as cod_item,
                    s.cod_items_description,
                    (SELECT d.des_items_description LIMIT 1 OFFSET 0) as des_items_description,
                    s.cod_tipo,
                    1 as is_set,
                    1 as is_available,
                    COALESCE(TRUE, s.is_managed) as is_managed,
                    COALESCE(TRUE, s.is_activo) as is_activo,
                    s.fecha_manufactura,
                    s.fecha_remocion,
                    p.pic_item_bin_main,
                    p.pic_item_path_main,
                    p.pic_item_data_main
                FROM
                    apiobj_items_set AS s
                    LEFT JOIN apiobj_items_description AS d
                    on d.cod_items_description = s.cod_items_description
                    left join apiobj_items_picture_main AS p
                    on p.cod_item = s.cod_item
                    WHERE 1=1 '. $where .  $and .'
                ORDER BY cod_item DESC, des_items_description
                LIMIT '.$limit.' OFFSET '.$offset;
    if (($results = $this->db->execute($this->sql, '', $values)) === false) {
      $this->notFound();
      $results = '';
      $return = FALSE;
    }

    /*retorno la data*/
    header('Content-Type: application/json; charset=utf-8');
    print( json_encode([
      'ok'          => $return,
      'data'        => $results,
      'pagination'  => $pagination
    ]));
  }

}
