<?php
namespace EzzForms;

/**
 * Class FieldCheckbox
 * @package EzzForms
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
