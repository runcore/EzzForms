<?php
namespace EzzForms;

/**
 * Class Ezz
 * @package EzzForms
 */
class Ezz {

    /**
     * @var Ezz
     */
    protected static $_instance;

    /**
     * @param $formName
     * @return Form
     */
    public static function form($formName) {
        $form = new Form($formName);
        return $form;
    }

    /**
     * @param $name
     * @return FieldText
     */
    public static function text($name) {
        $field = new FieldText($name);
        return $field;
    }

    /**
     * @param $name
     * @return FieldPassword
     */
    public static function password($name) {
        $field = new FieldPassword($name);
        return $field;
    }

    /**
     * @param $name
     * @return FieldSelect
     */
    public static function select($name) {
        $field = new FieldSelect($name);
        return $field;
    }


}
