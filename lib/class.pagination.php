<?php
/*
             ___            ____               
            |_ _|___ _ __  / ___|___  _ __ ___ 
             | |/ __| '_ \| |   / _ \| '__/ _ \
             | |\__ \ |_) | |__| (_) | | |  __/
            |___|___/ .__/ \____\___/|_|  \___|
                    |_|                        
Copyright (c) 2014  Díaz  Víctor  aka  (Máster Vitronic)
Copyright (c) 2021  Díaz  Víctor  aka  (Máster Vitronic)
<vitronic2@gmail.com>   <mastervitronic@vitronic.com.ve>
*/

class pagination {

    /**
     * Recurso de la db.
     *
     * @var resource
     * @access private
     */
    private $db;

    /**
     * La pagina
     *
     * @var integer
     * @access private
     */
    private $page = 1;

    /**
     * el numero de registros
     *
     * @var integer
     * @access private
     */    
    private $records;

    /**
     * el limite por pagina
     *
     * @var integer
     * @access private
     */  
    private $limit;

    /**
     * el template
     *
     * @var array
     * @access private
     */  
    private $template;

    /**
     * el numero de paginas
     *
     * @var integer
     * @access private
     */  
    private $pages;

    /**
     * el offset
     *
     * @var integer
     * @access private
     */  
    private $offset;

    /**
     * el la pagina anterior
     *
     * @var integer
     * @access private
     */  
    private $prev_page;

    /**
     * el la pagina siguiente
     *
     * @var integer
     * @access private
     */  
    private $next_page;

    /**
     * la cantidad de enlaces a mostrar
     *
     * @var integer
     * @access private
     */  
    private $show_links;

    /**
     * El mensaje
     *
     * @var string
     * @access private
     */
    private $error;

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
        //$this->close();
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
            //case 'page'     : $this->page    = $value; break;
            //case 'records'  : $this->records = $value; break;
            //case 'limit'    : $this->limit   = $value; break;
            //default: trigger_error('Unknown variable: '.$key);
        }
    }

    /**
     * Setea la pagina actual, si no se le pasan
     * parametros sera seteado en la pagina 1
     * @author Máster Vitronic
     * @param page integer el numero de pagina
     * @access public
     */
    public function set_page($page) {
        $this->page = (isset($page) and intval($page)) ? $page : 1;
    }

    /**
     * Retorna el total de paginas
     *
     * @author Máster Vitronic
     * @return integer el numero de paginas
     * @access public
     */
    public function get_pages( ){
        return ceil($this->records/$this->limit);
    }

    /**
     * Retorna la pagina siguiente
     *
     * @author Máster Vitronic
     * @return integer the next page
     * @access public
     */
    public function get_netx_page() {
        return ($this->records==0)?1:($this->page + 1);
    }

    /**
     * Retorna la pagina anterior
     *
     * @author Máster Vitronic
     * @param integer $timeout el timeout
     * @return radius instance of $this (for fluent interfaces)
     * @access public
     */
    public function get_prev_page(){
        return ($this->records==0)?1:($this->page - 1);
    }

    /**
     * Inicializa el paginador
     *
     * @author Máster Vitronic
     * @access public
     */
    public function initialized($config) {
        if(is_array($config)==false){
            return false;
        }
        $config = (object) $config;
        $this->records      = $config->records;
        $this->limit        = $config->limit;
        $this->show_links   = $config->show_links;
        if ( isset($config->template) == false ) {
            $this->template	= [];
        }else{
            $this->template	= $config->template;
        }
        $this->offset	= ($this->limit*($this->page-1));
        $this->pages	= $this->get_pages();
        $this->next_page= $this->get_netx_page();
        $this->prev_page= $this->get_prev_page();
        if ( $this->page >= $this->pages ) {
            $this->next_page = 1;
        }
        if ( $this->page == 1 ) {
            $this->prev_page = 1;
        }
        if ( $this->offset > $this->records ) {
            $this->offset = $this->records; //--@FIXME, esto esta mal
        }
        if ( $this->page > $this->pages ) {
            $this->page = $this->pages;
        }
    }

    /**
     * Retorna los links de paginas
     *
     * @author Máster Vitronic
     * @access public
     */
    public function get_links() {
        if (isset($this->template['link'])==false) {
            return false;
        }
        if(($this->page-$this->show_links) >= 1) {
            $begin = ($this->page-$this->show_links);
            $ending= ($this->page+$this->show_links);
        }else{
            $begin = 1;
            $ending=  ($this->records==0) ? 1 : ($this->show_links*2);
        }
        $links = [];
        $current = '';
        $page = 0;
        for ($page=$begin; $page<=$ending; $page++){
            if($page == $this->page or $this->page==0){
                $current = isset($this->template['current']) ? $this->template['current'] : '';
            }
            $template = sprintf($this->template['link'],$current,$page,$page,$page);
            $current='';
            //array_push($links,$template);
            $links[] = ['link'=>$template];/*Para Mustache*/
            if($page == $this->pages) {
                break;
            }
        }
        return $links;
    }

    /**
     * Retorna toda la informacion sobre la paginacion en curso
     *
     * @author Máster Vitronic
     * @access public
     */
    public function get_pagination() {
        return [
            'page'	    => $this->page,
            'pages'	    => $this->pages,
            'prev_page' => $this->prev_page,
            'next_page' => $this->next_page,
            'links'	    => $this->get_links(),
            'limit'	    => $this->limit,
            'offset'	=> $this->offset,
            'records'   => $this->records,
            //'begin'     => ($this->offset),
            //'ending'    => ($this->records-$this->offset)
        ];
    }


}