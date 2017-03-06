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

/**
 * Class Form
 * @package EzzForms
 */
abstract class Form {

    /**
     * @var string
     */
    protected $formName = '';

    /**
     * @var string
     */
    protected $formId = '';

    /**
     * @var string
     */
    public $formAction	= '';

    /**
     * @var string
     */
    public $formMethodName	= 'POST';

    /**
     * @var array
     */
    public $formMethod	= [];

    /**
     * @var bool
     */
    protected $isFileUploadEnabled = false;

    /**
     * @var bool
     */
    protected $isFormSubmit = false;

    /**
     * @var bool
     * @uses $_SESSION
     */
    protected $isCsrfProtectionEnabled = true;

    /**
     * Array with all form fields
     * @var array
     */
    protected $formFields = [];

    /**
     * CSRF or just form submit token
     * @var string
     */
    protected $formToken;

    /**
     * @var string
     */
    protected $formTokenId = '';

    /**
     * Form constructor.
     * @param string $name
     * @param string $action
     * @param string $methodName
     */
    protected function __construct($name='', $action='', $methodName='post') {
        $this->formName	= $name ? $name : '';
        $this->formId	= $name ? $name : '';
        $this->formAction = $action ? $action : '';
        // token
        $this->formToken = sha1(microtime(1));
        $this->formTokenId = $this->formId . '_ID';
        // method
        $methodName = strtolower($methodName);
        $this->formMethodName = in_array($methodName,['get','post']) ? $methodName : 'post';
        $this->formMethod = $this->formMethodName=='post' ? $_POST : $_GET;
        // is submit form?
        $this->isFormSubmit = !empty( $this->formMethod[$this->formId]['__ID__'] );
        if ( $this->isFormSubmit && $this->isCsrfProtectionEnabled ) {
            if ( !empty( $_SESSION[$this->formTokenId] ) ) {
                if ( $_SESSION[$this->formTokenId] == $this->formMethod[$this->formId]['__ID__'] ) {
                    $this->isFormSubmit = true;
                }
                unset( $_SESSION[$this->formTokenId] );
            }
        }
    }

    /**
     *
     */
    public function __destruct() {
        if ( $this->isCsrfProtectionEnabled ) {
            $_SESSION[ $this->formTokenId ] = $this->formToken;
        }
    }


    /**
     * Add field|fields to form
     * @param array|FormField $params
     * @throws \Exception
     */
    public function add( $params ) {
        if ( is_array($params) ) {
            foreach($params as $field) {
                $this->add( $field );
            }//foreach
        } else {
            // Add one field
            if ( $params instanceof FormField ) {
                $field = $params;
                $field->setFormId( $this->getFormId() );
                // Set value from GET|POST
                if ( $this->isFormSubmit ) {
                    if ( isset($this->formMethod[$this->formId][ $field->getId() ]) ) {
                        $field->setValue( $this->formMethod[$this->formId][$field->getId()] );
                    } else {
                        $field->setValue( [] );
                    }
                }
                if ( $field->getFileUploadFlag() ) {
                    $this->isFileUploadEnabled = true;
                }
                $this->formFields[$field->getId()] = $field;
            } else {
                throw new \Exception('Expected child of FormField class');
            }
        }
    }

    /**
     * @param string $extra
     * @return string
     */
    public function openTag($extra='') {
        $inline = ' name="'.$this->formName.'" id="'.$this->formId.'" action="'.$this->formAction.'" method="'.$this->formMethodName.'"';
        $inline .= $this->isFileUploadEnabled ? ' enctype="multipart/form-data"' : '';
        $inline .= !empty($extra) ? ' '.trim($extra) : '';
        //
        return '<form '.trim($inline).'>'.PHP_EOL
            .'<input type="hidden" name="'.$this->formName.'[__ID__]" value="'.$this->formToken.'"/>'.PHP_EOL;
    }

    /**
     * @param string $comment
     * @return string
     */
    public function closeTag($comment='') {
        return $comment.'</form>';
    }

    /**
     * @return string
     */
    public function render() {
        $out = '';
        $out .= $this->openTag();
        $out .= '<table>';
        $hiddens = [];

        /**
         * @var FormField $field
         */
        foreach($this->formFields as $field) {
            if ($field instanceof FieldHidden) {
                $hiddens[] = $field->render();
                continue;
            }
            $out .= '<tr><td>';
            $out .= $field->label( $field->getName() );
            $out .= '</td><td>';
            $out .= $field->render();
            $out .= '</td><td>';
//            $out .= $field->getError();
            $out .= '</td></tr>';
        }//foreach

        $out .= '</table>';
        $out .= join(' ', $hiddens);
        $out .= $this->closeTag();
        return $out;
    }

    /**
     * @return bool
     */
    public function isSubmit() {
        return $this->isFormSubmit;
    }

    /**
     * @return array
     */
    public function getValues() {
        $values = [];
        foreach($this->formFields as $fieldId=>$field) {
            $value = $field->getValue();
            if ($value) {
                $values[$fieldId] = $value;
            }
        }//foreach
        return $values;
    }

    /**
     * @return bool
     */
    public function isValid() {
        $valid = true;
        $this->validateErrors = [];
        foreach( $this->formFields as $fieldId=>$field ) {
            $errors = $field->validate();
            if ( sizeo($errors)) {
                $valid = false;
                $this->validateErrors[$fieldId] = $errors;
            }
        }
        pr($this->validateErrors);
        return $valid;
    }

    public function getFormId() {
        return $this->formId;
    }
}


/**
 * Class FieldValidator
 * @package EzzForms
 */
class ServerFieldValidator {

    public static $defaultRules = [
        'require' 	=> 'Обязательное поле'
        ,'minlen'	=> 'Длина поля от %s символов'
        ,'maxlen'	=> 'Длина поля до %s символов'
        ,'min'		=> 'Минимальное значение поля %s'
        ,'max'		=> 'Максимальное значение поля %s'
        ,'regex'	=> 'Некорректный формат поля'
        ,'int'		=> 'Ожидается целое число'
        ,'float'	=> 'Ожидается дробное число'
        ,'decimal'	=> 'Ожидается сумма в денежном формате'
        ,'ipv4'		=> 'Ожидается корректный IPV4 адрес'
        ,'ipv6'		=> 'Ожидается корректный IPV6 адрес'
        ,'url'		=> 'Ожидается корректный URL адрес'
        ,'email'	=> 'Ожидается корректный Email адрес'
        ,'time'     => 'Ожидается время в формате HH:MI:SS'
        ,'date'     => 'Ожидается корректная дата в формате DD.MM.YYYY'
        ,'datetime' => 'Ожидается корректная дата в формате DD.MM.YYYY HH:MI:SS'
        ,'timedate' => 'Ожидается корректная дата в формате HH:MI:SS DD.MM.YYYY'
        ,'equalto'	=> 'Значение отличается от поля %s'
    ];

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

    /**
     * @param $rules example: 'rule rule:params' OR| ['rule0', 'rule1 rule2:params1', 'rule1'=>'params1']
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
                if ( strpos($rule,':')===false ) { // w/o value
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

    public function validate($fieldName, $fieldValue) {
        // validate keys of available rules
        if (sizeof($this->rules)>0) {
            $diff = array_diff( array_keys($this->rules), array_keys(self::$defaultRules) );
            if ( count($diff)>0 ) { // exists unknown rules!
                foreach($diff as $v) {
                    throw new Exception('Unknown rules: '.strtoupper($v).' for '.strtoupper($fieldName), 1 );
//                    err(__CLASS__, 'Unknown rules: '.strtoupper($v).' for '.strtoupper($name) );
                }//foreach
            }
        }

    }

}


/**
 * Class ClientFieldValidator
 * @package EzzForms
 */
class ClientFieldValidator {

}


/**
 * Class FormField
 * @package EzzForms
 */
abstract class FormField {

    /**
     * @var string
     */
    protected $fieldId  = '';

    /**
     * @var string
     */
    protected $fieldName  = '';

    /**
     * @var null
     */
    protected $fieldValue;

    /**
     * @var null
     */
    protected $fieldDefaultValue;

    /**
     * @var ServerFieldValidator
     */
    protected $fieldValidator;

    /**
     * @var ClientFieldValidator
     */
    protected $clientFieldValidator;

    /**
     * @var string
     */
    protected $extra = '';

    /**
     * @var string
     */
    protected $formId = 'form';

    /**
     * @var bool
     */
    protected $isFileUploadFlag = false;

    /**
     * @var bool
     */
    protected $isInputField = true;

    /**
     * @var bool
     */
    protected $isMultiValue = false;

    /**
     * FormField constructor.
     * @param $id
     * @param null $default
     * @param null $validation
     */
    public function __construct($id, $default=null, $validation=null) {
        $this->fieldId = $id;
        $this->fieldName = $id;
        $this->fieldDefaultValue = $default;
        $this->fieldValue = $default;
        $this->fieldValidator = new ServerFieldValidator( $validation );
//        $this->clientFieldValidator = new ClientFieldValidator( $validation );
    }

    public function validate() {

    }

    /**
     * @return string
     */
    public function getId() {
        return $this->fieldId;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->fieldName;
    }

    /**
     * @param $formName
     */
    public function setFormId($formId) {
        $this->formId = $formId;
    }

    /**
     * @param $value
     */
    public function setValue($value) {
        if ($this->isInputField) {
            $this->fieldValue = $value;
        }
    }

    /**
     * @return null
     */
    public function getValue() {
        if ($this->isInputField) {
            return $this->fieldValue;
        }
        return null;
    }

    /**
     * @return null
     */
    public function getDefault() {
        return $this->fieldDefaultValue;
    }

    /**
     * @return bool
     */
    public function getFileUploadFlag() {
        return $this->isFileUploadFlag;
    }

    /**
     * @param $text
     * @return string
     */
    public function label($text) {
        return '<label for="'.$this->getId().'">'.$text.'</label>';
    }

    /**
     * @param string $extra
     * @return mixed
     */
    abstract function render($extra='');

    /**
     * @param string $extra
     * @return array|string
     */
    protected function renderAttributes( $extra='' ) {
        $out = [];
        //$v = getParams($this->validator);
        if (!empty($this->fieldName)) {
            if ($this->isMultiValue) {
                $out[] = 'name="'.$this->formId.'['. $this->fieldName.'][]"';
            } else {
                $out[] = 'name="'.$this->formId.'['. $this->fieldName.']"';
            }
            //$out[] = 'name="'.$this->formId.'['. $this->fieldName.']"';
            //$out[] = 'name="'.$this->getNameAttribute().'"';
        }
        if (!empty($this->fieldId)) {
            $out[] = 'id="'.$this->formId.'_'.$this->fieldId.'"';
        }
        if (isset($this->fieldValidation['maxlen'])) {
            $out[] = 'maxlength="' . abs(intval($this->fieldValidation['maxlen'])) . '"';
        }
        if (!empty($this->extra)) {
            $out[] = $this->extra;
        }
        if (!empty($extra)) {
            $out[] = $extra;
        }
        return trim(join(' ',$out));
    }

    /**
     *
     */
    public function error() {
        // TODO:!
    }
}

/**
 * Class FormFieldMulti
 * @package EzzForms
 */
abstract class FormFieldMulti extends FormField {

    protected $separator = '<br />';
    protected $options = [];
    protected $isMultiValue = true;

    /**
     * FormFieldMulti constructor.
     * @param $id
     * @param array|null $default
     * @param array|null $options
     * @param int $size
     */
    public function __construct($id, Array $default = null, Array $options = null, $size=1) {
        parent::__construct($id, $default, null);

        $this->options = $options;
    }

    /**
     * @param $values
     */
    public function setValue($values) {
        if (!is_array($values)) {
            $values = [$values];
        }
        // Remove fake ID from input values (ie those that are not in $options array)
        $validIds = [];
        foreach($this->options as $k=>$v) {
            if (is_array($v)) {
                foreach ($v as $id => $subValue) {
                    $validIds[] = $id;
                }//foreach
            } else {
                $validIds[] = $k;
            }
        }
        // get only valid IDs
        $intersect = array_intersect($values, $validIds);

        parent::setValue( $intersect );
    }

    /**
     * @param string $extra
     * @return string
     * @throws \Exception
     */
    public function render($extra='') {
        if (empty($this->options)) {
            throw new \Exception('Expected array of options');
        }
        $out = array();
        foreach ($this->options as $k => $value) {
            if (is_array($value)) {
                $out[] = '<b>' . escape($k) . ':</b>';
                foreach ($value as $id => $subValue) {
                    $out[] = $this->renderOption($id, $subValue, $extra);
                }//foreach
            } else {
                $out[] = $this->renderOption($k, $value, $extra);
            }
        }//foreach
        return join($this->separator.PHP_EOL, $out);
    }

    /**
     * @param $id
     * @param $value
     * @param $extra
     * @param $type
     * @return string
     */
    protected function _renderOption($id, $value, $extra, $type) {
        return sprintf('<label><input type="%s" value="%s" %s %s> %s</label>'.PHP_EOL
            ,$type
            ,$id
            ,$this->renderAttributes($extra)
            ,(in_array($id, $this->fieldValue) ? 'checked="checked"' : '')
            ,escape($value)
        );
    }

    /**
     * @param $id
     * @param $value
     * @param $extra
     * @return mixed
     */
    protected abstract function renderOption($id, $value, $extra);
}


/**
 * Class FieldSubmit
 * @package EzzForms
 */
class FieldSubmit extends FormField {
    protected $isInputField = false;

    public function render($extra='') {
        return '<input type="submit" '.parent::renderAttributes($extra).' value="'.escape($this->fieldValue).'"/>'.PHP_EOL;
    }

    public function label($text) {
        return '';
    }
}

/**
 * Class FieldText
 * @package EzzForms
 */
class FieldText extends FormField {
    public function render($extra='') {
        return '<input type="text" '.parent::renderAttributes($extra).' value="'. escape($this->fieldValue).'"/>'.PHP_EOL;
    }
}

/**
 * Class FieldPassword
 * @package EzzForms
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

/**
 * Class FieldHidden
 * @package EzzForms
 */
class FieldHidden extends FormField {
    /**
     * @param string $extra
     * @return string
     */
    public function render($extra='') {
        return '<input type="hidden" ' . parent::renderAttributes($extra) . ' value="' . escape($this->fieldValue) . '"/>';
    }
}

/**
 * Class FieldTextarea
 * @package EzzForms
 */
class FieldTextarea extends FormField {
    public function render($extra='') {
        return '<textarea '.parent::renderAttributes($extra).' >'.escape($this->fieldValue).'</textarea>'.PHP_EOL;
    }
}

/**
 * Class FieldSelect
 * @package EzzForms
 */
class FieldSelect extends FormField {

    protected $size = 1;
    protected $options = [];
    protected $isMultiValue = true;

    /**
     * FieldSelect constructor.
     * @param $id
     * @param array|null $default
     * @param array|null $options
     * @param int $size
     */
    public function __construct($id, Array $default = null, Array $options = null, $size=1) {
        parent::__construct($id, $default, null);

        $this->options = $options;
        $this->size = $size;
    }

    /**
     * @param string $extra
     * @return string
     */
    public function render($extra='') {
        if (!is_array($this->fieldValue)) {
            $this->fieldValue = [$this->fieldValue];
        }
        $out = '<select '.$this->renderAttributes($extra).' size="'.$this->size.'"'.($this->size>1?' multiple="multiple"':'').'>'.PHP_EOL;
        if ( is_array($this->options) ) {
            foreach ($this->options as $optionId => $optionValues) {
                if (is_array($optionValues)) { // OPTGROUP
                    $out .= '<optgroup label="' . $optionId . '">'.PHP_EOL;
                    foreach ($optionValues as $subOptionId => $subOptionValue) { // OPTION
                        $out .= '<option value="' . $subOptionId . '"' . (in_array($subOptionId, $this->fieldValue) ? ' selected="selected"' : '') . '>' . escape($subOptionValue) . '</option>'.PHP_EOL;
                    }
                    $out .= '</optgroup>'.PHP_EOL;
                } else { // OPTION
                    $out .= '<option value="' . $optionId . '"' . (in_array($optionId, $this->fieldValue) ? ' selected="selected"' : '') . '>' . escape($optionValues) . '</option>'.PHP_EOL;
                }
            }//foreach
        }
        $out .= '</select>'.PHP_EOL;
        return $out;
    }
}


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


/**
 * Class FieldRadio
 * @package EzzForms
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


/**
 * Class FieldFile
 * @package EzzForms
 */
class FieldFile extends FormField {

    /**
     * @var bool
     */
    protected $isFileUploadFlag = true;

    /**
     * max size of file, in Bytes
     * @var int
     */
    protected $maxFileSize = 2000000;


    public function __construct($id, $maxFileSize = null) {
        parent::__construct($id, null, null);

        if ( !empty($maxFileSize) && is_numeric($maxFileSize) ) {
            $this->maxFileSize = $maxFileSize;
        }
    }

    /**
     * @param $text
     * @return string
     */
    public function label($text) {
        return '';
    }

    /**
     * @param string $extra
     * @return string
     */
    public function render($extra='') {
        return '<input type="file" ' . parent::renderAttributes($extra) . '/>'
		.'<input type="hidden" name="MAX_FILE_SIZE" value="' . $this->maxFileSize . '"/>';
    }
}


////////////////////////////////////////////////////////////
// Functions helpers
////////////////////////////////////////////////////////////

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
