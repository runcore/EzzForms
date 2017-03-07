<?php
namespace EzzForms;

/**
 * Class FieldSubmit
 * @package EzzForms
 */
class FieldSubmit extends FormField {
    protected $isInputField = false;

    /**
     * FieldSubmit constructor.
     * @param null $default
     */
    public function __construct($id='',$default=null) {
        parent::__construct('', $default );
        //$this->fieldId = $id;
        //$this->fieldName = $id;
        $this->fieldDefaultValue = $default;
        $this->fieldValue = $default;
    }

    public function render($extra='') {
        return '<input type="submit" '.parent::renderAttributes($extra).' value="'.escape($this->fieldValue).'"/>'.PHP_EOL;
    }

    public function label($text) {
        return '';
    }
}
