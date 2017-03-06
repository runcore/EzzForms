<?php
namespace EzzForms;

/**
 * Class FieldTextarea
 * @package EzzForms
 */
class FieldTextarea extends FormField {
    public function render($extra='') {
        return '<textarea '.parent::renderAttributes($extra).' >'.escape($this->fieldValue).'</textarea>'.PHP_EOL;
    }
}
