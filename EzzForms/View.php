<?php
namespace EzzForms;


class View {

    protected $_vars = [];

    protected $_template;

    protected $isEscape = true;

    protected $debug = false;

    public function __construct() {

    }

    /**
     * @param $template
     */
    public function setTemplate($templateFileName) {
        $this->_template = $templateFileName;
    }

    /**
     * @param $name
     * @param null $data
     * @return $this
     */
    public function set($name, $data=null) {
        if (is_array($name)) {
            $this->_vars = array_merge($this->_vars, $name);
        } else {
            $this->_vars[$name] = $data;
        }
        return $this;
    }

    /**
     * @param $name
     * @param Form $data
     */
    public function setForm($name, Form $data) {
        $this->set($name, $data);
    }

    /**
     * @param $template
     * @return string
     */
    public function fetch($templateFileName=null) {
        if (!is_null($templateFileName) && !empty($templateFileName) && is_file($templateFileName)) {
            $this->_template = $templateFileName;
        }
        $template = $this->_template;
        //
        if (file_exists($template)) {
            extract( $this->isEscape ? array_escape($this->_vars) : $this->_vars );
            ob_start();
            {
                if ($this->debug) {
                    include $template;
                } else {
                    $old_er = error_reporting( E_ALL & ~E_NOTICE | E_STRICT );
                    include $template;
                    error_reporting( $old_er );
                }
            }
            return ob_get_clean();
        } else {
            throw new Exception('Template not found: '.$template);
        }
    }



}