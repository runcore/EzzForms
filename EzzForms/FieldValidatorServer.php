<?php
namespace EzzForms;

/**
 * Class FieldValidator
 * @package EzzForms
 */
class FieldValidatorServer {

    // todo: need add support of custom errors

    const RULE_NAME_REQUIRED = 'required';
    const RULE_NAME_MINLEN   = 'minlen';
    const RULE_NAME_MAXLEN   = 'maxlen';
    const RULE_NAME_MIN      = 'min';
    const RULE_NAME_MAX      = 'max';
    const RULE_NAME_REGEXP   = 'regexp';
    const RULE_NAME_INT      = 'int';
    const RULE_NAME_FLOAT    = 'float';
    const RULE_NAME_DECIMAL  = 'decimal';
    const RULE_NAME_IPV4     = 'ipv4';
    const RULE_NAME_IPV6     = 'ipv6';
    const RULE_NAME_URL      = 'url';
    const RULE_NAME_EMAIL    = 'email';
    const RULE_NAME_TIME     = 'time';
    const RULE_NAME_DATE     = 'date';
    const RULE_NAME_DATETIME = 'datetime';
    const RULE_NAME_TIMEDATE = 'timedate';
    const RULE_NAME_EQUALTO  = 'equalto';

    /**
     * @var array
     */
    public static $defaultRules = [
        self::RULE_NAME_REQUIRED => 'Обязательное поле'
        ,self::RULE_NAME_MINLEN  => 'Длина поля от %d символов'
        ,self::RULE_NAME_MAXLEN  => 'Длина поля до %d символов'
        ,self::RULE_NAME_MIN     => 'Минимальное значение поля %s'
        ,self::RULE_NAME_MAX     => 'Максимальное значение поля %s'
        ,self::RULE_NAME_REGEXP  => 'Некорректный формат поля'
        ,self::RULE_NAME_INT     => 'Ожидается целое число'
        ,self::RULE_NAME_FLOAT   => 'Ожидается дробное число'
        ,self::RULE_NAME_DECIMAL => 'Ожидается сумма в денежном формате'
        ,self::RULE_NAME_IPV4    => 'Ожидается корректный IPv4 адрес'
        ,self::RULE_NAME_IPV6    => 'Ожидается корректный IPv6 адрес'
        ,self::RULE_NAME_URL     => 'Ожидается корректный URL адрес'
        ,self::RULE_NAME_EMAIL   => 'Ожидается корректный Email адрес'
        ,self::RULE_NAME_TIME    => 'Ожидается время в формате HH:MI:SS'
        ,self::RULE_NAME_DATE    => 'Ожидается корректная дата в формате DD.MM.YYYY'
        ,self::RULE_NAME_DATETIME => 'Ожидается корректная дата в формате DD.MM.YYYY HH:MI:SS'
        ,self::RULE_NAME_TIMEDATE => 'Ожидается корректная дата в формате HH:MI:SS DD.MM.YYYY'
        ,self::RULE_NAME_EQUALTO  => 'Значение отличается от поля %s'
    ];

    const ERROR_DATE_INVALID = 'Несуществующая дата';

    const REGEXP_DECIMAL  = "/^\\d+(\\.\\d{1,2})$/";
    const REGEXP_TIME     = "/^([0-1]\\d|2[0-3])(:[0-5]\\d){2}$/";
    const REGEXP_DATE     = "/^(?:(0[1-9]|[1-2]\\d|3[01])\\.(0[13-9]|1[012])|(0[1-9]|[1-2]\\d)\\.(02|1[0-2]))\\.((?:19|20)\\d\\d)$/";
    const REGEXP_DATETIME = "/^(?:(0[1-9]|[1-2]\\d|3[01])\\.(0[13-9]|1[012])|(0[1-9]|[1-2]\\d)\\.(02|1[0-2]))\\.((?:19|20)\\d\\d) ([0-1]\\d|2[0-3])(:[0-5]\\d){2}$/";
    const REGEXP_TIMEDATE = "/^([0-1]\\d|2[0-3])(:[0-5]\\d){2} (?:(0[1-9]|[1-2]\\d|3[01])\\.(0[13-9]|1[012])|(0[1-9]|[1-2]\\d)\\.(02|1[0-2]))\\.((?:19|20)\\d\\d)$/";

    /**
     * @var array
     */
    protected $rules = [];

    /**
     * ServerFieldValidator constructor.
     * @param $rules
     */
    public function __construct( $rules ) {
        if (!is_null($rules)) {
            $this->parseRules($rules);
        }
    }

    public function getRules() {
        return $this->rules;
    }

    /**
     * @param $rules
     * @example 'rule rule:params' OR| ['rule0', 'rule1 rule2:params1', 'rule1'=>'params1']
     */
    protected function parseRules($rules) {
        if (is_array($rules)) {
            foreach($rules as $key=>$rule) {
                if (is_numeric($key)) { // rule is string
                    $this->parseRules($rule);
                } else { // rule is array cell
                    $this->rules[ trim($key) ] = trim($rule);
                }
            }//foreach
        } else if (is_string($rules)) {
            $rules = trim( preg_replace('/\s{2,}/',' ', strval($rules) ) ); // remove trash
            foreach(explode(' ', $rules) as $rule) {
                $rule=trim($rule);
                if (empty($rule)) {
                    continue;
                }
                if ( $rule!='' && strpos($rule,':')===false ) { // w/o value
                    $this->rules[$rule] = true;
                } else {
                    list($ruleName, $ruleVal) = explode(':', $rule,2);
                    if ( !empty($ruleName) && !empty($ruleVal) ) {
                        $this->rules[trim($ruleName)] = trim($ruleVal);
                    }
                }
            }//foreach
        }
    }

    /**
     * @param $fieldName
     * @param $fieldValue
     * @param $fieldsValues
     * @return array
     * @throws Exception
     */
    public function validate($fieldName, $fieldValue, $fieldsValues) {
        $errors = [];
        //pr($this->rules);
        // validate keys of available rules
        if (sizeof($this->rules)>0) {
            $diff = array_diff( array_keys($this->rules), array_keys(self::$defaultRules) );
            if ( count($diff)>0 ) { // exists unknown rules!
                foreach($diff as $v) {
                    throw new Exception('Unknown rules: '.strtoupper($v).' for '.strtoupper($fieldName), 1 );
                }//foreach
            }
        }

        // validate!
        foreach($this->rules as $ruleName=>$rule ) {
            $methodName = 'validate_'.$ruleName;
            if ( !method_exists($this,$methodName) ) {
                throw new Exception('Unknown rule method : '.$methodName, 1 );
            }

            $error = $this->$methodName( $fieldValue, $rule, $fieldsValues );
            if (!empty($error)) {
                $errors[$ruleName] = $error;
            }
        }//foreach

        return $errors;
    }

    /**
     * @param $value
     * @return mixed
     */
    protected function validate_required( $value ) {
        if ( !isset($value) || $value==='') {
            return self::$defaultRules[ self::RULE_NAME_REQUIRED ];
        }
        return null;
    }

    /**
     * @param $value
     * @param $rule
     * @return string
     */
    protected function validate_minlen( $value, $rule ) {
        $minlen = intval($rule);
        $fieldLen = mb_strlen( $value );
        if ( $minlen>0 && $fieldLen<$minlen ) {
            return sprintf(self::$defaultRules[ self::RULE_NAME_MINLEN ], $minlen);
        }
        return null;
    }

    /**
     * @param $value
     * @param $rule
     * @return string
     */
    protected function validate_maxlen( $value, $rule ) {
        $maxlen = intval($rule);
        $fieldLen = mb_strlen( $value );
        if ( $maxlen>0 && $fieldLen>$maxlen ) {
            return sprintf(self::$defaultRules[ self::RULE_NAME_MAXLEN ], $maxlen);
        }
        return null;
    }

    /**
     * @param $value
     * @param $rule
     * @return string
     */
    protected function validate_min( $value, $rule ) {
        $minVal   = intval($rule);
        $fieldVal = intval($value);
        if ( $minVal>0 && $fieldVal<$minVal ) {
            return sprintf(self::$defaultRules[ self::RULE_NAME_MIN ], $minVal);
        }
        return null;
    }

    /**
     * @param $value
     * @param $rule
     * @return string
     */
    protected function validate_max( $value, $rule ) {
        $maxVal = intval($rule);
        $value  = intval($value);
        if ( $maxVal>0 && $value>$maxVal ) {
            return sprintf(self::$defaultRules[ self::RULE_NAME_MAX ], $maxVal);
        }
        return null;
    }

    /**
     * @param $value
     * @param $rule
     * @return mixed
     */
    protected function validate_regexp( $value, $rule ) {
        if ( filter_var($value, FILTER_VALIDATE_REGEXP,['options'=>['regexp'=>$rule]] )===false )  {
            return self::$defaultRules[ self::RULE_NAME_REGEXP ];
        }
        return null;
    }

    /**
     * @param $value
     * @return mixed
     */
    protected function validate_int( $value ) {
        if ( filter_var($value, FILTER_VALIDATE_INT  )===false ) {
            return self::$defaultRules[ self::RULE_NAME_INT ];
        }
        return null;
    }

    /**
     * @param $value
     * @return mixed
     */
    protected function validate_float( $value ) {
        if ( filter_var($value, FILTER_VALIDATE_FLOAT)===false ) {
            return self::$defaultRules[ self::RULE_NAME_FLOAT ];
        }
        return null;
    }

    /**
     * @param $value
     * @return mixed
     */
    protected function validate_decimal( $value ) {
        if ( filter_var($value, FILTER_VALIDATE_FLOAT)===false
            || filter_var($value, FILTER_VALIDATE_REGEXP, ['options'=>['regexp'=>self::REGEXP_DECIMAL]] )===false
        ) {
            return self::$defaultRules[ self::RULE_NAME_DECIMAL ];
        }
        return null;
    }

    /**
     * @param $value
     * @return mixed
     */
    protected function validate_ipv4( $value ) {
        if ( filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 )===false ) {
            return self::$defaultRules[ self::RULE_NAME_IPV4 ];
        }
        return null;
    }

    /**
     * @param $value
     * @return mixed
     */
    protected function validate_ipv6( $value ) {
        if ( filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 )===false ) {
            return self::$defaultRules[ self::RULE_NAME_IPV6 ];
        }
        return null;
    }

    /**
     * @param $value
     * @return mixed
     */
    protected function validate_url( $value ) {
        if ( filter_var($value, FILTER_VALIDATE_URL  )===false ) {
            return self::$defaultRules[ self::RULE_NAME_URL ];
        }
        return null;
    }

    /**
     * @param $value
     * @return mixed|null
     */
    protected function validate_email( $value ) {
        if ( filter_var($value, FILTER_VALIDATE_EMAIL)===false ) {
            return self::$defaultRules[ self::RULE_NAME_EMAIL ];
        }
        return null;
    }

    /**
     * @param $value
     * @return mixed
     */
    protected function validate_time( $value ) {
        if ( filter_var($value, FILTER_VALIDATE_REGEXP,['options'=>['regexp'=>self::REGEXP_TIME]] )===false ) {
            return self::$defaultRules[ self::RULE_NAME_TIME ];
        }
        return null;
    }

    /**
     * @param $value
     * @return bool
     */
    protected function _isValidDate($value) {
        // but need validate DATE part! (ex. 29.02.2001)
        $dt = trim(preg_replace('/\d+:\d+:\d+/','',$value)); // remove TIME
        list($dd,$mm,$yyyy) = explode('.', $dt);
        if ( !checkdate( (int)$mm, (int)$dd, (int)$yyyy) ) {
            return false;
        }
        return true;
    }

    /**
     * @param $value
     * @return mixed|null|string
     */
    protected function validate_date( $value  ) {
        if ( filter_var($value, FILTER_VALIDATE_REGEXP,['options'=>['regexp'=>self::REGEXP_DATE]])===false ) {
            return self::$defaultRules[self::RULE_NAME_DATE];
        } else if ( !$this->_isValidDate($value) ) {
            return self::ERROR_DATE_INVALID;
        }
        return null;
    }

    /**
     * @param $value
     * @return mixed|null|string
     */
    protected function validate_datetime( $value ) {
        if ( filter_var($value, FILTER_VALIDATE_REGEXP,['options'=>['regexp'=>self::REGEXP_DATETIME]] )===false ) {
            return self::$defaultRules[ self::RULE_NAME_DATETIME ];
        } else if ( !$this->_isValidDate($value) ) {
            return self::ERROR_DATE_INVALID;
        }
        return null;
    }

    /**
     * @param $value
     * @return mixed|null|string
     */
    protected function validate_timedate( $value ) {
        if ( filter_var($value, FILTER_VALIDATE_REGEXP,['options'=>['regexp'=>self::REGEXP_TIMEDATE]] )===false ) {
            return self::$defaultRules[ self::RULE_NAME_TIMEDATE ];
        } else if ( !$this->_isValidDate($value) ) {
            return self::ERROR_DATE_INVALID;
        }
        return null;
    }

    /**
     * @param $value
     * @param $rule
     * @param $fieldsValues
     * @return null|string
     */
    protected function validate_equalto( $value, $rule, $fieldsValues ) {
        //$equalFieldValue = isset($fieldsValues[$rule]) ? $fieldsValues[$rule] : '';
        if ( !empty($value) && isset($fieldsValues[$rule]) && $value!=$fieldsValues[$rule] ) {
            return sprintf( self::$defaultRules[ self::RULE_NAME_EQUALTO ], $rule );
        }
        return null;
    }

}
