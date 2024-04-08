<?php
/*
            ____                  _     _
           / ___|_   _  __ _  ___| |__ (_)
          | |  _| | | |/ _` |/ __| '_ \| |
          | |_| | |_| | (_| | (__| | | | |
           \____|\__,_|\__,_|\___|_| |_|_|
Copyright (c) 2014  Díaz  Víctor  aka  (Máster Vitronic)
Copyright (c) 2018  Díaz  Víctor  aka  (Máster Vitronic)
Copyright (c) 2024  PICCORO Lenz McKAY, you know who i am! ¬_¬

use as
<ul>
<?php
    echo "<li>";
    echo $object->Name;
    echo "</li>";}
?>
</ul>


*/

/*camelCase  :-) */
#[\AllowDynamicProperties]
class view {

    private $css            = '' ;
    private $js             = '' ;
    private $author         = '' ;
    private $title          = '' ;
    private $description    = '' ;
    private $html           = '' ;
    private $meta                ;
    public  $tpl                 ;
    private $router              ;
    private $messages       = [] ;
    private $dataview       = [] ;
    public  $cache               ;
    private $view_file      = '' ;
    private $http_status    = 200;

    /**
     * Instancia para el patrón de diseño singleton (instancia única)
     * @var object instancia
     * @access private
     */
    private static $instancia = null;

    private function __construct($data = NULL) {
        if(is_array($data))
            $this->dataview = $data;
        $this->router   = router::iniciar();
        $this->view_file = $this->router->module;
    }

    public function __destruct() {

    }

    /**
     * Inicia la instancia de la clase
     * @return object
     */
    public static function iniciar($data = NULL) {
        if(is_array($data))
            array_merge($this->dataview, $data);
        if (!self::$instancia instanceof self) {
            self::$instancia = new self;
        }
        return self::$instancia;
    }

    /**
     * Método magico __clone
     */
    public function __clone() {
        trigger_error("Operación Invalida:" .
                " clonación no permitida", E_USER_ERROR);
    }

    /**
     * Método magico __wakeup
     */
    public function __wakeup() {
        trigger_error("Operación Invalida:" .
                " deserializar no esta permitido " .
                get_class($this) . " Class. ", E_USER_ERROR);
    }

    /* Magic method get
     *
     * @access public
     */
    public function __get($key) {
        switch ($key) {
            case 'meta'     : return $this->meta;
            case 'js'       : return $this->js;
            case 'css'      : return $this->css;
            case 'messages' : return $this->messages;
            case 'values'   : return $this->values;
            case 'title'    : return $this->title;
            case 'http_status': return $this->http_status;
            case 'description': return $this->description;
        }
        return null;
    }

    /* Magic method set
     *
     * @access public
     */
    public function __set($key,$value) {
        switch ($key) {
            case 'meta'     :  $this->meta      = $value; break;
            case 'messages' :  $this->messages  = $value; break;
            case 'values'   :  $this->values    = $value; break;
            case 'author'   :  $this->author    = $value; break;
            case 'title'    :  $this->title     = $value; break;
            case 'description':$this->description = $value; break;
            case 'http_status':$this->http_status = $value; break;
            default: trigger_error('Unknown variable: '.$key);
        }
    }

    /* Add message to message buffer
     *
     * INPUT:  string format[, string arg,...]
     * OUTPUT: -
     * ERROR:  -
     */
    public function add_message($message) {
        if (func_num_args() == 0) {
            return;
        }
        $args = func_get_args();
        $format = array_shift($args);

        array_push($this->messages,['message' => vsprintf($format, $args)]);
    }

    /**
     * addContent
     *
     */
    public function addContent($dataview) {
        if(is_array($dataview)){
            foreach ($dataview as $key => $value) {
                $this->values[$key] = $value;
                $this->$key = $value;
            }
        }
    }

    /**
     * getContent 
     *
     */
    public function getContent() {
        return $this->values;
    }

    /**
     * getMessages
     *
     */
    public function getMessages() {
        return $this->messages;
    }

    /**
     * loadTemplate
     *
     * wraper a mustache loadTemplate
     *
     * @access public
     */
    public function loadTemplate($tpl) {
        $theme = ($this->router->type_module === 'private') ? private_theme : public_theme ;
        $template = $this->router->type_module .DS. $theme . DS . $tpl;
        return $this->tpl->loadTemplate($template);
    }

    /**
     * addCss
     *
     * Añade los css a usar en el metadata
     *
     * @access public
     */
    public function addCss($css) {
         $this->css .= "\t\t" .'<link rel="stylesheet"           href="'.$css.'">' . PHP_EOL;
    }

    /**
     * addJs
     *
     * Añade los js a usar
     *
     * @access public
     */
    public function addJs($js) {
         $this->js .= "\t\t" .'<script src="'.$js.'"></script>' . PHP_EOL;
    }

    /**
     * set
     *
     * setea todo
     *
     * @access public
     * @return string
     */
    public function set() {
        $this->meta =    '<title>'.$this->title.'</title>' . PHP_EOL
                ."\t\t" .'<meta charset="utf-8">' . PHP_EOL
                ."\t\t" .'<meta http-equiv="X-UA-Compatible" content="IE=edge">' . PHP_EOL
                .$this->css
                ."\t\t" .'<meta name="viewport"            content="width=device-width, initial-scale=1.0">' . PHP_EOL
                ."\t\t" .'<meta name="generator"           content="Guachi (Lightweight and very simple php framework) v'.GUACHI_VERSION.'">' . PHP_EOL
                ."\t\t" .'<meta name="description"         content="'.$this->description.'">' . PHP_EOL
                ."\t\t" .'<meta name="author"              content="'.$this->author.'">' . PHP_EOL
                ."\t\t" .'<meta name="module"              content="'.$this->router->module.'">';
        $this->js   = trim($this->js);
    }

    /**
     * load
     *
     * Carga el html
     *
     * @access private
     * @return string
     */
    public function load($html) {
        $this->html = $html;
    }

    /**
     * generate
     *
     * Escupe el html
     *
     * @access public
     * @return string
     */
    public function renders() {
        error_log(DIR_VIEWS . _DIR_ . $this->view_file, 0);
        if( file_exists(DIR_VIEWS . _DIR_ . $this->view_file))
            require DIR_VIEWS . _DIR_ . $this->view_file;
    }

}
