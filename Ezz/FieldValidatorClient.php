<?php
namespace Ezz;

/**
 * Class ClientFieldValidator
 * @package Ezz
 */
class FieldValidatorClient extends FieldValidatorServer {

    /**
     * http://www.formvalidator.net/#file-validators_size
     * @var array
     *
     */
    protected $jqueryValidator = [
         self::RULE_NAME_REQUIRED => 'data-validation="required"'
        ,self::RULE_NAME_MINLEN   => 'data-validation="length" data-validation-length="min%d"'
        ,self::RULE_NAME_MAXLEN   => 'data-validation="length" data-validation-length="max%d"'
        ,self::RULE_NAME_MIN      => 'data-validation="number" data-validation-allowing="range[%d;1000000000]" '
        ,self::RULE_NAME_MAX      => 'data-validation="number" data-validation-allowing="range[-1000000000;%d]" '
        ,self::RULE_NAME_INT      => 'data-validation="number"'
        ,self::RULE_NAME_FLOAT    => 'data-validation="number" data-validation-allowing="float"'
        ,self::RULE_NAME_DECIMAL  => 'data-validation="custom" data-validation-regexp="^\d+(\.\d{1,2})?$"'
        ,self::RULE_NAME_IPV4     => 'data-validation="custom" data-validation-regexp="^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$"'
        ,self::RULE_NAME_IPV6     => 'data-validation="custom" data-validation-regexp="^(?:[A-F0-9]{1,4}:){7}[A-F0-9]{1,4}$"'
        ,self::RULE_NAME_REGEXP   => 'data-validation="custom" data-validation-regexp="^([a-z]+)$"'
        ,self::RULE_NAME_URL      => 'data-validation="url"'
        ,self::RULE_NAME_EMAIL    => 'data-validation="email"'
        ,self::RULE_NAME_TIME     => 'data-validation="time" data-validation-help="HH:mm"'
        ,self::RULE_NAME_DATE     => 'data-validation="date" data-validation-format="dd.mm.yyyy"'
        ,self::RULE_NAME_DATETIME => 'data-validation="date,time" data-validation-format="dd.mm.yyyy HH:mm"'
        ,self::RULE_NAME_TIMEDATE => 'data-validation="date,time" data-validation-format="HH:mm dd.mm.yyyy"'
        ,self::RULE_NAME_EQUALTO  => 'data-validation="confirmation" data-validation-confirm="%s"'
    ];

}

