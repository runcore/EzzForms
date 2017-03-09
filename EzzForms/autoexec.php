<?php
/**
 * Light and powerful forms for embedding
 *
 * features:
 *  CSRF protection
 *  Default values for fields
 *  Validation of values
 *  Custom validation error texts
 *
 * @package EzzForms
 * @created 01.01.2017
 * @author runcore
 */
namespace EzzForms;

// todo: server, custom template engine
// todo: client, support for jquery
// todo: server, support ajax-request validation
// todo: client, support for bootstrap 3
// todo: client, support for angular
// todo: client, support for client validation

spl_autoload_register(function ($class) {
    $class = str_replace('_',DIRECTORY_SEPARATOR, $class);
    include $class . '.php';
});


// SYNTAX SUGAR

/**
 * @param $formName
 * @param null $action
 * @param null $method
 * @return Form
 */
function form($formName, $action=null, $method=null) {
    return new Form($formName, $action, $method);
}

function text($name) {
    return new FieldText($name);
}

function textarea($name) {
    return new FieldTextarea($name);
}

function password($name) {
    return new FieldPassword($name);
}

function select($name) {
    return new FieldSelect($name);
}

function checkbox($name) {
    return new FieldCheckbox($name,[],[]);
}

function radio($name) {
    return new FieldRadio($name,[],[]);
}

function file($name) {
    return new FieldFile($name);
}

function hidden($name) {
    return new FieldHidden($name);
}

function submit($label) {
    return new FieldSubmit('',$label);
}

/**
 * @param $a
 * @param int $f
 */
function pr($a, $f=0) {
    echo '<pre>';
    print_r($a);
    echo '</pre>';
    if ($f) exit;
}

/**
 * @param $s
 * @return string
 */
function escape($s) {
    $s=trim($s);
    return ''==$s?'':htmlspecialchars($s, ENT_QUOTES);
}

function array_escape($a) {
    return is_array($a) ? array_map(__FUNCTION__, $a): (is_string($a) ? escape($a) : $a);
}