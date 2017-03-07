<?php
namespace EzzForms;

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

    protected $options = [];

    /**
     * @var null
     */
    protected $fieldDefaultValue;

    /**
     * @var FieldValidatorServer
     */
    protected $fieldValidator;

    /**
     * @var FieldValidatorClient
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

    protected $validationRules;
    protected $errors;

    /**
     * FormField constructor.
     * @param $id
     * @param null $default
     * @param null $validation
     */
    public function __construct($id, $default=null, $validation=null) {
        $this->fieldId = $id;
        $this->fieldName = $id;

        //$this->fieldDefaultValue = null;
        //$this->fieldValue = null;

        //$this->fieldValidator = new FieldValidatorServer( $validation );
        //$this->validationRules = $this->fieldValidator->getRules();
//        $this->clientFieldValidator = new FieldValidatorClient( $validation );
    }

    /**
     * @param $fieldsValues
     * @return array
     */
    public function validate( $fieldsValues ) {
        if ( is_object( $this->fieldValidator ) ) {
            $this->errors = $this->fieldValidator->validate($this->getName(), $this->getValue(), $fieldsValues);
        }
        return $this->errors;
    }

    /**
     * @return mixed
     */
    public function getErrors() {
        return $this->errors;
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




    /**
     * @param $def
     * @return $this
     */
    public function def($def) {
        $this->fieldDefaultValue = $def;
        $this->fieldValue = $def;
        return $this;
    }

    /**
     * @param $validation
     * @return $this
     */
    public function validation($validation) {
        $this->fieldValidator = new FieldValidatorServer( $validation );
        $this->validationRules = $this->fieldValidator->getRules();
//        $this->fieldValidator = new FieldValidatorServer($validation);
        return $this;
    }

    /**
     * @param $options
     * @return $this
     */
    public function options($options) {
        $this->options = $options;
        return $this;
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
        $isRequired = isset($this->validationRules['required']);
        return '<label for="'.$this->getId().'">'.$text.($isRequired?'<b>*</b>':'').'</label>';
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

}
