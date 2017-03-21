<?php
namespace Ezz;

/**
 * Class FormField
 * @package Ezz
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
     * Value of field
     * @var null
     */
    protected $fieldValue;

    /**
     * All available options of field
     * @var array
     */
    protected $options = [];

    /**
     * Simple array of values, without optgroup
     * @var array
     */
    protected $optionsAsSimpleArray = [];

    /**
     * Default value of field
     * @var null
     */
    protected $fieldDefaultValue;

    /**
     * Server-side field validator
     * @var FieldValidatorServer
     */
    protected $fieldValidator;

    /**
     * Client-side field validator
     * @var FieldValidatorClient
     */
    protected $clientFieldValidator;

    /**
     * Extra attributes of field
     * @var string
     */
    protected $extra = '';

    /**
     * Parent form ID. Used to encapsulate fields in a form
     * @var string
     */
    protected $formId = 'form';

    // FLAGS

    /**
     * Field supports user input
     * @var bool
     */
    protected $isInputField = true;

    /**
     * Field is a file type
     * @var bool
     */
    protected $isFileField = false;

    /**
     * Field is a hidden type
     * @var bool
     */
    protected $isHiddenField = false;

    /**
     * Field can contain several values
     * @var bool
     */
    protected $isMultiValue = false;

    // VALIDATIONS

    /**
     * Validation rules of field
     * @var array
     */
    protected $validationRules = [];

    /**
     * Validation errors of field
     * @var array
     */
    protected $validationErrors = [];

    // METHODS

    /**
     * FormField constructor.
     * @param $id
     * @param null $default
     * @param null $validation
     */
    public function __construct($id, $default=null, $rules=null) {
        $this->fieldId = $id;
        $this->fieldName = $id;

        if ( !is_null($default) ) {
            $this->fieldDefaultValue = $default;
            $this->fieldValue = $default;
        }

        if ( !is_null($rules) ) {
            $this->setValidationRules($rules);
        }
    }

    /**
     * @param $fieldsValues
     * @return array
     */
    public function validate( Array $fieldsValues ) {
        if ( is_object( $this->fieldValidator ) ) {
            $this->validationErrors = $this->fieldValidator->validate($this->getName(), $this->getValue(), $fieldsValues);
        }
        return $this->validationErrors;
    }

    /**
     * @return array
     */
    public function getValidationErrors() {
        return $this->validationErrors;
    }

    public function errors($extra='') {
        $errors = $this->getValidationErrors();
        return '<p '.$extra.'>'.(sizeof($errors)? join('<br />',$errors) :'' ).'</p>';
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
     * @param $formId
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

    protected function setOptions( $options ) {
        $this->options = $options;
        //
        $this->optionsAsSimpleArray = [];
        foreach($this->options as $k=>$option) {
            if (is_array($option)) {
                foreach($option as $id => $subValue) {
                    $this->optionsAsSimpleArray[] = $id;
                }//foreach
            } else {
                $this->optionsAsSimpleArray[] = $k;
            }
        }//foreach
    }

    /**
     * @param $rules
     */
    protected function setValidationRules($rules) {
        $this->fieldValidator = new FieldValidatorServer( $rules );
        $this->validationRules = $this->fieldValidator->getRules();
//        $this->fieldValidator = new FieldValidatorServer($validation);
    }

    /**
     * @return bool
     */
    public function isInputField() {
        return $this->isInputField;
    }

    /**
     * @return bool
     */
    public function isHiddenField() {
        return $this->isHiddenField;
    }

    /**
     * @return bool
     */
    public function isFileField() {
        return $this->isFileField;
    }

    /**
     * @param $text
     * @return string
     */
    public function label($text, $extra='') {
        $isRequired = isset($this->validationRules['required']);
        return '<label for="'.$this->getId().'" '.(!empty($extra)?$extra:'').'>'.$text.($isRequired?'<b>*</b>':'').'</label>';
    }

    /**
     * @param string $extra
     * @return array|string
     */
    protected function renderAttributes( $extra='' ) {
        $out = [];
        if (!empty($this->fieldName)) {
            if ($this->isMultiValue) {
                $out[] = 'name="'.$this->formId.'['. $this->fieldName.'][]"';
            } else {
                $out[] = 'name="'.$this->formId.'['. $this->fieldName.']"';
            }
        }
        if (!empty($this->fieldId)) {
            $out[] = 'id="'.$this->formId.'_'.$this->fieldId.'"';
        }
        if (isset($this->validationRules['maxlen'])) {
            $out[] = 'maxlength="' . abs(intval($this->validationRules['maxlen'])) . '"';
        }
        if (!empty($this->extra)) {
            $out[] = $this->extra;
        }
        if (!empty($extra)) {
            $out[] = $extra;
        }

        return trim(join(' ',$out));
    }

    // PUBLIC SYNTAX SUGAR

    /**
     * Set rules of validation
     * @param $rules
     * @return $this
     */
    public function rules($rules) {
        $this->setValidationRules($rules);
        return $this;
    }

    /**
     * Set options of field
     * @param $options
     * @return $this
     */
    public function options($options) {
        $this->setOptions( $options );
        return $this;
    }

    /**
     * Set default values of field
     * @param $def
     * @return $this
     */
    public function def($def) {
        $this->fieldDefaultValue = $def;
        $this->fieldValue = $def;
        return $this;
    }

    // ABSTRACT

    /**
     * @param string $extra
     * @return mixed
     */
    abstract public function render($extra='');

}
