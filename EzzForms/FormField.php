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
        $this->fieldValidator = new FieldValidatorServer( $validation );
//        $this->clientFieldValidator = new FieldValidatorClient( $validation );
    }

    /**
     *
     */
    public function validate() {
        $errors = $this->fieldValidator->validate( $this->getName(), $this->getValue() );
        return $errors;
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
