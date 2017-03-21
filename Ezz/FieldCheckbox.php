<?php
namespace Ezz;

/**
 * Class FieldCheckbox
 * @package Ezz
 */
class FieldCheckbox extends FormFieldMulti {
    /**
     * @param $id
     * @param $value
     * @param $extra
     * @return string
     */
    protected function renderOption($id, $value, $extra) {
        return parent::_renderOption($id, $value, $extra, 'checkbox');
    }
}
