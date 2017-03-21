<?php
namespace Ezz;

/**
 * Class FieldPassword
 * @package Ezz
 */
class FieldPassword extends FormField {
    /**
     * @param string $extra
     * @return string
     */
    public function render($extra='') {
        return '<input type="password" ' . parent::renderAttributes($extra) . ' value="' . escape($this->fieldValue) . '"/>';
    }
}
