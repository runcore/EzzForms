<?php
namespace Ezz;

/**
 * Class FieldRadio
 * @package Ezz
 */
class FieldRadio extends FormFieldMulti {
    /**
     * @param $id
     * @param $value
     * @param $extra
     * @return string
     */
    protected function renderOption($id, $value, $extra) {
        return parent::_renderOption($id, $value, $extra, 'radio');
    }
}
