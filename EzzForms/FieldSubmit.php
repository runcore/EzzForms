<?php
namespace EzzForms;

/**
 * Class FieldSubmit
 * @package EzzForms
 */
class FieldSubmit extends FormField {
    protected $isInputField = false;

    public function render($extra='') {
        return '<input type="submit" '.parent::renderAttributes($extra).' value="'.escape($this->fieldValue).'"/>'.PHP_EOL;
    }

    public function label($text) {
        return '';
    }
}
