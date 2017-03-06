<?php
namespace EzzForms;

/**
 * Class FieldText
 * @package EzzForms
 */
class FieldText extends FormField {
    public function render($extra='') {
        return '<input type="text" '.parent::renderAttributes($extra).' value="'. escape($this->fieldValue).'"/>'.PHP_EOL;
    }
}

