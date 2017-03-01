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
            $this->isFormSubmit = (
                !empty( $_SESSION[$this->formTokenId] )
                &&
                ( $_SESSION[$this->formTokenId] == $this->formMethod[$this->formId]['__ID__'] )
            );
            unset( $_SESSION[$this->formTokenId] );
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
                    if ( isset($this->formMethod[ $field->getId() ]) ) {
                        $field->setValue($this->formMethod[$field->getId()]);
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

        /**
         * @var FormField $field
         */
        foreach($this->formFields as $field) {
            $out .= $field->label( $field->getName() );
            $out .= $field->render();
            $out .= '<br />';
        }//foreach

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
     * @return bool
     */
    public function isValid() {
        $valid = true;
        foreach( $this->formFields as $fieldId=>$field ) {
//            if (!$e->isValid()) {
//                $valid = false;
//            }
        }
        return $valid;
    }

    public function getFormId() {
        return $this->formId;
    }


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
     * @var null
     */
    protected $fieldValidation;

    /**
     * @var string
     */
    protected $extra = '';

    /**
     * @var string
     */
    protected $formId = 'form';

    protected $isFileUploadFlag = false;

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
        $this->fieldValue   = $default;
        $this->fieldValidation = $validation;
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
        $this->value = $value;
    }

    /**
     * @return null
     */
    public function getValue() {
        return $this->fieldValue;
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
            $out[] = 'name="'.$this->formId.'['. $this->fieldName.']"';
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
 * Class FieldSubmit
 * @package EzzForms
 */
class FieldSubmit extends FormField {
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
    public $size = 1;
    public $options = [];

    public function __construct($id, Array $default = null, Array $options = null, $size=1) {
        parent::__construct($id, $default, null);
//        if ( !is_array($this->fieldValue) ) {
//            $this->fieldValue = [ $this->fieldValue ];
//        }
        $this->options = $options;
        $this->size = $size;
    }

    public function render($extra='') {
        if (!is_array($this->fieldValue)) {
            $this->value = [$this->fieldValue];
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

class FieldFile extends FormField {

    protected $isFileUploadFlag = true;

    public function render($extra='') {
        return '<input type="file" ' . parent::renderAttributes($extra) . ' multiple="multiple" />';
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
    echo '<pre>', print_r($a), '</pre>';
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
