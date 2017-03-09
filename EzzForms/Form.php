<?php
namespace EzzForms;

/**
 * Class Form
 * @package EzzForms
 */
class Form {

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
     * Array of GET,POST depending on the $this->formMethod
     * @var array
     */
    public $formMethod	= [];

    /**
     * All form fields
     * @var array
     */
    protected $formFields = [];

    /**
     * CSRF or Just form submit token
     * @var string
     */
    protected $formToken;

    /**
     * Name of token field
     * @var string
     */
    protected $formTokenId = '';

    /**
     * All validation errors grouped by field id
     * @var array
     */
    protected $validationErrors = [];

    /**
     * @var string
     */
    protected $templateFileName = '';

    /**
     * @var View $view
     */
    protected $view;

    // FLAGS -----------------------------------------------------------------------------------------------------------

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
     * @var bool
     */
    protected $isFileUploadEnabled = false;


    /**
     * Form constructor.
     * @param string $name
     * @param string $action
     * @param string $methodName
     */
    public function __construct($name='', $action='', $methodName=null) {
        $this->formName	= $name ? $name : '';
        $this->formId	= $name ? $name : '';
        $this->setAction($action);
        // token
        $this->formToken = sha1(microtime(1));
        $this->formTokenId = $this->formId . '_ID';
        // method
        $this->setMethodName($methodName);
    }

    /**
     * @param $action
     */
    protected function setAction($action) {
        $this->formAction = $action ? $action : '';
    }

    /**
     * @param $methodName
     */
    protected function setMethodName($methodName) {
        if (!is_null($methodName)) {
            $methodName = strtolower($methodName);
            $this->formMethodName = in_array($methodName, ['get', 'post']) ? $methodName : 'post';
            $this->formMethod = $this->formMethodName == 'post' ? $_POST : $_GET;
            // is submit form?
            $this->isFormSubmit = !empty($this->formMethod[$this->formId]['__ID__']);
            if ($this->isFormSubmit && $this->isCsrfProtectionEnabled) {
                if (!empty($_SESSION[$this->formTokenId])) {
                    if ($_SESSION[$this->formTokenId] == $this->formMethod[$this->formId]['__ID__']) {
                        $this->isFormSubmit = true;
                    }
                    unset($_SESSION[$this->formTokenId]);
                }
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
                if ( $field->isFileField() ) {
                    $this->isFileUploadEnabled = true;
                }
                $this->formFields[$field->getId()] = $field;
            } else {
                throw new \Exception('Expected child of FormField class');
            }
        }
    }

    public function fields(Array $fields) {
        $this->add($fields);
        return $this;
    }

    /**
     * @param string $extra
     * @return string
     */
    public function openTag($extra='') {
        $inline = ' name="'.$this->formName.'" id="'.$this->formId.'" action="'.$this->formAction.'" method="'.$this->formMethodName.'"';
        $inline .= $this->isFileUploadEnabled ? ' enctype="multipart/form-data"' : '';
        $inline .= !empty($extra) ? ' '.trim($extra) : '';
        $hiddens = $this->renderHiddenFields();
        //
        return '<form '.trim($inline).'>'.PHP_EOL
            .'<input type="hidden" name="'.$this->formName.'[__ID__]" value="'.$this->formToken.'"/>'.PHP_EOL
            .$hiddens
        ;
    }

    /**
     * @param string $comment
     * @return string
     */
    public function closeTag() {
        return '</form>';
    }

    /**
     * @return string
     */
    public function render() {
        if ( !empty($this->templateFileName) && is_file($this->templateFileName) ) {
            return $this->renderTemplate();
        }
        return $this->renderDefault();
    }

    /**
     *
     */
    protected function renderTemplate() {
        //if (is_null($this->view)) {
            //$this->view = new View();
        //}
        //$this->view->setTemplate( $this->templateFileName );
        //$this->view->setForm('form', $this );
        //$this->view->set('fields', $this->formFields );

        $_ENV['fields'] = $this->formFields;

        $html = file_get_contents( $this->templateFileName );
        //
        $pattern = "/\{(?P<part>[^\:]+)(\:(?P<name>[^\: \}]+))?(\:\"(?P<title>[^\"]+)\")?(?P<extra>[^\}]+)?\}/";
        $html = preg_replace_callback($pattern, function($matches){
            $out = '';
            $part  = !empty($matches['part'])  ? $matches['part']  : '';
            $name  = !empty($matches['name'])  ? $matches['name']  : '';
            $title = !empty($matches['title']) ? $matches['title'] : '';
            $extra = !empty($matches['extra']) ? $matches['extra'] : '';
            //pr([$part,$name,$title,$extra]);

            // Fields
            $fields = $_ENV['fields'];
            //pr($fields);
            if (!empty($name) && isset($fields[$name])) {
                $field = $fields[$name];

                /**
                 * @var FormField $field
                 */
                if ($part=='label') {
                    $out .= $field->label($title, $extra);
                } else if ($part=='field') {
                    //pr([$part,$name,$title,$extra]);
                    $out .= $field->render($extra);
                } else if ($part=='error') {
                    $out .= $field->errors($extra);
                }
            } else {
                // Form
                if ($part == 'formOpen') {
                    $out .= $this->openTag($extra);
                } else if ($part == 'formClose') {
                    $out .= $this->closeTag();
                }
            }
            return $out;
        }, $html);

        return $html;

    }

    /**
     * @return string
     */
    public function renderDefault() {
        $out = '<style>td.error{color:red;font-size:0.9em}</style>';
        $out .= $this->openTag();
        //$out .= $this->renderHiddenFields();
        $out .= '<table>';

        foreach($this->formFields as $field) {
            /**
             * @var FormField $field
             */
            if ( $field->isHiddenField() ) {
                continue;
            }
            $errors = $field->getValidationErrors();
            $out .= '<tr>'
                .'<td>' . $field->label( $field->getName() ) . '</td>'
                .'<td>' . $field->render() . '</td>'
                .'<td class="error">' . (sizeof($errors)? join('<br />',$errors) :'' ) . '</td>'
                .'</tr>'
            ;
        }//foreach
        $out .= '</table>';
        $out .= $this->closeTag();

        return $out;
    }

    /**
     * @return string
     */
    public function renderHiddenFields() {
        $out = '';
        foreach($this->formFields as $field) {
            /**
             * @var FormField $field
             */
            if ( $field->isHiddenField() ) {
                $out .= $field->render().PHP_EOL;
            }
        }//foreach

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
        foreach($this->formFields as $fieldId=>$fieldObj) {
            /**
             * @var FormField $fieldObj
             */
            $value = $fieldObj->getValue();
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
        $this->validationErrors = [];
        $fieldsValues = $this->getValues();
        foreach( $this->formFields as $fieldId=>$fieldObj ) {
            /**
             * @var FormField $fieldObj
             */
            $errors = $fieldObj->validate( $fieldsValues );
            if ( sizeof($errors) ) {
                $valid = false;
                $this->validationErrors[$fieldId] = $errors;
            }
        }//foreach

        return $valid;
    }

    /**
     * @param $templateFileName
     * @throws Exception
     */
    protected function setTemplate($templateFileName) {
        if (empty($templateFileName) || !is_file($templateFileName)) {
            throw new Exception('Invalid template filename '.$templateFileName );
        }
        // Template file is OK
        $this->templateFileName = $templateFileName;
        if (is_null($this->view)) {
            $this->view = new View();
        }
    }

    /**
     * @return string
     */
    public function getFormId() {
        return $this->formId;
    }

    // SYNTAX SUGAR

    public function method($methodName) {
        $this->setMethodName($methodName);
        return $this;
    }

    public function action($action) {
        $this->setAction($action);
        return $this;
    }

    public function template($templateFileName) {
        $this->setTemplate($templateFileName);
        return $this;
    }

}
