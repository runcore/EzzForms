<?php
namespace Ezz;

/**
 * Class FieldSubmit
 * @package Ezz
 */
class FieldSubmit extends FormField {

    protected $isInputField = false;

    /**
     * FieldSubmit constructor.
     * @param null $default
     */
    public function __construct($id='',$default=null) {
        parent::__construct($id, $default );
    }

    /**
     * @param string $extra
     * @return string
     */
    public function render($extra='') {
        $value = (!empty($this->fieldValue) ? ' value="'.escape($this->fieldValue).'"' : '');
        return '<input type="submit" '.parent::renderAttributes($extra).' '.$value.'/>'.PHP_EOL;
    }

    /**
     * @param $text
     * @return string
     */
    public function label($tex,$extra='') {
        return '';
    }
}
