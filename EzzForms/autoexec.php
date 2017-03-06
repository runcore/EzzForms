<?php
/**
 * Light and powerful forms for embedding
 *
 * features:
 *  CSRF protection
 *  Default values for fields
 *  Validation of values
 *
 * @package EzzForms
 * @created 01.01.2017
 * @author runcore
 */
namespace EzzForms;

spl_autoload_register(function ($class) {
    $class = str_replace('_',DIRECTORY_SEPARATOR, $class);
    include $class . '.php';
});


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
