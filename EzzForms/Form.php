<?php
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
     * @var array
     */
    protected $validationErrors = [];

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
            /**
             * @var FormField $field
             */
            $errors = $field->validate();
            if ( sizeof($errors) ) {
                $valid = false;
                $this->validationErrors[$fieldId] = $errors;
            }
        }
        pr($this->validationErrors);
        return $valid;
    }

    public function getFormId() {
        return $this->formId;
    }
}
