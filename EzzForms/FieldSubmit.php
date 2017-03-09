<?php
namespace EzzForms;

/**
 * Class FieldSubmit
 * @package EzzForms
 */
class FieldSubmit extends FormField {

    /**
     * @var bool
     */
    protected $isInputField = false;

    /**
     * FieldSubmit constructor.
     * @param null $default
     */
    public function __construct($id='',$default=null) {
        parent::__construct('', $default );
    }

    /**
     * @param string $extra
     * @return string
     */
    public function render($extra='') {
        return '<input type="submit" '.parent::renderAttributes($extra).' value="'.escape($this->fieldValue).'"/>'.PHP_EOL;
    }

    /**
     * @param $text
     * @return string
     */
    public function label($text) {
        return '';
    }
}
