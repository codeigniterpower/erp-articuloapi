<?php
/*
             ___            ____               
            |_ _|___ _ __  / ___|___  _ __ ___ 
             | |/ __| '_ \| |   / _ \| '__/ _ \
             | |\__ \ |_) | |__| (_) | | |  __/
            |___|___/ .__/ \____\___/|_|  \___|
                    |_|                        
Copyright (c) 2014  Díaz  Víctor  aka  (Máster Vitronic)
Copyright (c) 2018  Díaz  Víctor  aka  (Máster Vitronic)
<vitronic2@gmail.com>   <mastervitronic@vitronic.com.ve>
*/

class files {

    /**
     * Recurso de la db.
     *
     * @var resource
     * @access private
     */
    private $db;

    /**
     * El mime tipe esperado
     *
     * @var array
     * @access private
     */
    private $type_expected = [];

    /**
     * El id del usuario que sera dueño del archivo
     *
     * @var int
     * @access private
     */
    private $id_owner;

    /**
     * indica si sera afectado tambien el la db 
     *
     * @var boolean
     * @access private
     */
    private $in_db = true;

    /**
     * El mensaje
     *
     * @var string
     * @access private
     */
    private $error;

    /**
     * El ambito 
     *
     * @var string
     * @access private
     */
    private $scope;

    /**
     * El recurso hashids
     *
     * @var resource
     * @access private
     */
    private $hashids;

    /**
     * El file_path
     *
     * @var string
     * @access private
     */
    const file_path = ROOT . 'cloud/';
    
    /**
     * El nombre en el sistema de ficheros
     *
     * @var string
     * @access private
     */
    private $name_in_system = null;
    
    /**
     * Cuando son ficheros multiples, aqui un array con todos los nombres
     *
     * @var array
     * @access private
     */
    private $names_in_system = [];

    /**
     * El nombre en el arhivo
     *
     * @var string
     * @access private
     */
    private $file_name;

    /**
     * El nombre temporal del archivo
     *
     * @var string
     * @access private
     */
    private $tmp_name;

    /**
     * El tipo mime 
     *
     * @var string
     * @access private
     */
    private $mime_type;

    /**
     * El tamaño del archivo
     *
     * @var string
     * @access private
     */
    private $file_size;

    /**
     * El id del campo subido $_FILES['foto'] seria foto
     *
     * @var string
     * @access private
     */
    private $file_id;

    /**
     * No redimencionar la imagen
     *
     * @var string
     * @access private
     */
    private $no_rzs = false;

    /**
     * El id del campo subido $_FILES['foto'] seria foto
     *
     * @var string
     * @access private
     */
    private $observation = null;

    /**
     * El id del campo subido $_FILES['foto'] seria foto
     *
     * @var string
     * @access private
     */
    private $folder_name = null;

    /**
     * El id del campo subido $_FILES['foto'] seria foto
     *
     * @var string
     * @access private
     */
    private $protected = 'f';

    /**
     * El id del campo subido $_FILES['foto'] seria foto
     *
     * @var string
     * @access private
     */
    private $private = 'f';

    /**
     * Instancia para el patrón de diseño singleton (instancia única)
     * @var object instancia
     * @access private
     */
    private static $instancia = null;

    /**
     * __construct
     *
     * Constructor de la clase
     *
     * @access public
     *
     */
    private function __construct() {
        global $db;
        $this->db  = $db;
    }

    /**
     * __destruct
     *
     * Destructor, destruye automaticamente la clase.
     *
     * @access public
     */
    public function __destruct() {

    }

    /**
     * Inicia la instancia de la clase
     * @return object
     */
    public static function iniciar() {
        if (!self::$instancia instanceof self) {
            self::$instancia = new self;
        }
        return self::$instancia;
    }

    /**
     * Método magico __clone
     */
    public function __clone() {
        trigger_error('Operación Invalida:' .
                ' clonación no permitida', E_USER_ERROR);
    }

    /**
     * Método magico __wakeup
     */
    public function __wakeup() {
        trigger_error('Operación Invalida:' .
                ' deserializar no esta permitido ' .
                get_class($this) . " Class. ", E_USER_ERROR);
    }

    /* Magic method get
     *
     * @access public
     */
    public function __get($key) {
        switch ($key) {
            case 'name_in_system'   : return $this->name_in_system;
            case 'names_in_system'  : return $this->names_in_system;
            case 'file_path'        : return self::file_path;
            case 'error'            : return $this->error;
        }
        return null;
    }

    /* Magic method set
     *
     * @access public
     */
    public function __set($key,$value) {
        switch ($key) {
            case 'scope'            : $this->scope          = $value; break;
            case 'in_db'            : $this->in_db          = $value; break;
            case 'no_rzs'           : $this->no_rzs         = $value; break;
            case 'id_owner'         : $this->id_owner       = $value; break;
            case 'file_id'          : $this->file_id        = $value; break;
            case 'name_in_system'   : $this->name_in_system = $value; break;
            case 'type_expected'    : $this->type_expected  = $value; break;
            case 'observation'      : $this->observation    = $value; break;
            case 'folder_name'      : $this->folder_name    = $value; break;
            case 'protected'        : $this->protected      = $value; break;
            case 'private'          : $this->private        = $value; break;
            default: trigger_error('Unknown variable: '.$key);
        }
    }

    public static function reArrayFiles(&$file_post) {
        $file_ary = array();
        $multiple = is_array($file_post['name']);

        $file_count = $multiple ? count($file_post['name']) : 1;
        $file_keys = array_keys($file_post);

        for ($i=0; $i<$file_count; $i++){
            foreach ($file_keys as $key){
                $file_ary[$i][$key] = $multiple ? $file_post[$key][$i] : $file_post[$key];
            }
        }

        return $file_ary;
    }

    /**
     * get_name_in_system
     * retorna el name_in_system del archivo
     * 
     *
     * @author Máster Vitronic
     * @return string  
     * @error  false
     * @access public
     */
    private function get_name_in_system(){
        if(isset($this->name_in_system) === false){
            return false;
        }
        $hashids = new lib\Hashids\Hashids( $this->scope );
        return   $hashids->encode($this->name_in_system);        
    }

    /**
     * save_in_disk
     * Guarda un archivo en el disco
     * 
     *
     * @author Máster Vitronic
     * @return bool
     * @access public
     */
    private function save_in_disk() {

        if(!is_dir(self::file_path)){
            mkdir(self::file_path, 0777, true);
        }

        if ( isset($this->name_in_system) ) {

            if (($ext = strrchr($this->file_name, ".")) === false) {
                $this->error = gettext('ERROR: Archivo sin extensión, no permitido.');
                return false;
            }

            if($this->type_expected){
                if(in_array( $this->mime_type , $this->type_expected ) === false ){
                    $this->error = gettext('ERROR: Tipo de archivo no permitido.');
                    return false;
                }
            }else{
                if (in_array(substr($ext, 1), config_array(allowed_uploads)) == false) {
                    $this->error = gettext('ERROR: Tipo de archivo no permitido.');
                    return false;
                }
            }

            /*@TODO esto debe ser seteable tambien via db setting*/
            if ( $this->file_size > max_size_upload ) {
                $this->error = gettext('ERROR: El tamaño del archivo supera lo permitido.');
                return false;
            }

            /*el comportamiento esperado es que esto sobreescriba si el archivo existe*/
            if ( (move_uploaded_file($this->tmp_name,self::file_path . $this->name_in_system)) == false ) {
                $this->error = gettext('ERROR: no se pudo almacenar el archivo.');
                return false;
            }

            /*No redimencionar la imagen*/
            if ($this->no_rzs==true){
                return true;
            }

            /*redimensiono la imagen y comprimo la imagen*/
            if ( resizeImage(self::file_path . $this->name_in_system, 800, 600) ) {
                $query = 'update cloud.files '
                            .'set file_size=%d, mime_type=%s '
                            .'where id_file=%d';
                $values = [
                    filesize(self::file_path . $this->name_in_system),
                    mime_content_type(self::file_path . $this->name_in_system),
                    $this->name_in_system
                ];
                $this->db->execute($query,$values);
            }

            return true;
        }

        $this->error = gettext('ERROR: Debe proveer un identificador del archivo (name_in_system).');
        return false;
    }

    /**
     * save
     * guarda un archivo
     *
     *
     * @author Máster Vitronic
     * @return boolean
     * @access public
     * @TODO , esto guardara multiples files, pero solo retornara el ultimo id guardado
     */
    public function save() {
        $result = true;
        $files = $this->reArrayFiles($_FILES[$this->file_id]);
        $no_name = ($this->name_in_system) ? false : true;
        foreach ($files as $file) {
            if(isset($file['name']) == false ){
                $this->error = gettext('ERROR: No se encontro ningun archivo para guardar.');
                $result = false;
                break;
            }
            $this->tmp_name  = $file['tmp_name'];
            if($this->in_db === true){
                $this->file_name = $file['name'];
                $this->mime_type = $file['type'];
                $this->file_size = $file['size'];
                $values=[
                    $this->id_owner,
                    self::file_path,
                    ($no_name) ? null : $this->name_in_system,
                    $this->file_name,
                    $this->mime_type,
                    $this->file_size,
                    $this->observation,
                    $this->folder_name,
                    $this->protected,
                    $this->private
                ];
                $query = "select cloud.save_file(%d, %s, %s, %s, %s, %s, %s, %s, %s, %s)";
                if ( ($id_file = $this->db->execute($query,$values)) === false) {
                    $this->error = $this->db->error[2];
                    $result = false;
                    break;
                }
                $this->name_in_system = $id_file[0]['save_file'];//hash('sha256',$id_file[0]['save_file']);
                array_push($this->names_in_system, $id_file[0]['save_file']);
            }
            if($this->save_in_disk() === false){
                $result = false;
                break;
            }
        }
        return ($result) ? $this->name_in_system : false;
    }

    /**
     * delete
     * borra un archivo
     *
     *
     * @author Máster Vitronic
     * @return string
     * @access public
     */
    public function delete() {
        if($this->in_db === true){
            $query="delete from cloud.files where id_file=%d";
            $this->db->query($query, $this->name_in_system) ;
        }
        unlink(self::file_path . $this->name_in_system);
    }

    /**
     * getMimeType
     * retorna el tipo mime de un archivo
     *
     *
     * @author Máster Vitronic
     * @return string
     * @access private
     */
    private function getMimeType() {
        return finfo_file(finfo_open(FILEINFO_MIME_TYPE),self::file_path . $this->name_in_system);
    }

}
