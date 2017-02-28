<?php
namespace EzzForms;

abstract class Form {

    protected $name = '';
    protected $id = '';
    public $action	= '';
    public $methodName	= 'POST';
    public $method	= [];
    // Flags
    protected $isFileUpload = false;
    protected $isSubmit = false;
    // Arrays
    protected $fields = [];

    protected function __construct($name='', $action='', $methodName='post') {
        $this->name	= $name ? $name : '';
        $this->id	= $name ? $name : '';
        $this->action = $action ? $action : '';
        $this->methodName = $methodName ? strtolower($methodName) : '';
        $this->method = $this->methodName=='post' ? $_POST : $_GET;
        //
        $this->isSubmit = isset( $this->method[$this->name.'_submit'] );
    }

    /**
     * Add field|fields to form
     * @param array|FormField $params
     * @throws \Exception
     */
    public function add( $params ) {
//        pr($this->isSubmit);
        if ( is_array($params) ) {
            foreach($params as $field) {
                $this->add( $field );
            }//foreach
        } else {
            // Add one field
            if ( $params instanceof FormField ) {
                // Set value from GET|POST
                if ( $this->isSubmit ) {
                    //pr($_POST);
                    if ( isset($this->method[ $params->getId() ]) ) {
                        $params->setValue($this->method[$params->getId()]);
                    }
                }
                $this->fields[$params->getId()] = $params;
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
        //return $this->before."\n".'<form '.$this->inline.' '.$extra.'>'
        $inline = ' name="'.$this->name.'" id="'.$this->id.'" action="'.$this->action.'" method="'.$this->methodName.'"';
        $inline .= $this->isFileUpload ? ' enctype="multipart/form-data"' : '';
        $inline .= !empty($extra) ? ' '.$extra : '';
        //
        return '<form '.$inline.'>'
        .'<input type="hidden" name="'.$this->name.'_submit" value="1"/>';
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
        foreach($this->fields as $field) {
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
        return $this->isSubmit;
    }

    // Valid all values of formElements of this Form
    public function isValid() {
        $valid = true;
//        foreach ($this->fields as $k=>$e) {
//            if (!$e->isValid()) {
//                $valid = false;
//            }
//        }
        return $valid;
    }


}

abstract class FormField {

    protected $id  = '';
    protected $name  = '';
    protected $value  = null;
    protected $default = null;
    protected $validation = null;

    /**
     * FormField constructor.
     * @param $id
     * @param null $default
     * @param null $validation
     */
    public function __construct($id, $default=null, $validation=null) {
        $this->id = $id;
        $this->name = $id;
        $this->default = $default;
        $this->value   = $default;
        $this->validation = $validation;
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function setValue($value) {
        $this->value = $value;
    }

    public function getValue() {
        return $this->value;
    }

    public function getDefault() {
        return $this->default;
    }

    public function label($text) {
        return '<label for="'.$this->getId().'">'.$text.'</label>';
    }

    abstract function render($extra='');

    protected function renderAttributes( $extra='' ) {
        $out = [];
        //$v = getParams($this->validator);
        if (!empty($this->name)) {
            $out[] = 'name="'.$this->name.'"';
        }
        if (!empty($this->id)) {
            $out[] = 'id="'.$this->id.'"';
        }
        //if (isset($v['maxlen']) && $v['maxlen']>0 ) $o[] = 'maxlength="'.$v['maxlen'].'"';
        if (!empty($this->extra)) {
            $out[] = $this->extra;
        }
        if (!empty($extra)) {
            $out[] = $extra;
        }
        $out = join(' ',$out);
        //if ($this->error) {
            //$oo = preg_replace("/class=\"([a-z_\d]+)\"/", "class=\"$1 error\"", $oo);
        //}
        return $out;
    }

    public function error() {
        // TODO:!
    }

}

class FieldSubmit extends FormField {
    public function render($extra='') {
        return '<input type="submit" '.parent::renderAttributes($extra).' value="'.escape($this->value).'"/>';
    }

    public function label($text) {
        return '';
    }
}

class FieldText extends FormField {
    public function render($extra='') {
        //$this->value = isset($this->default)&&!empty($this->default)&&empty($this->value) ? $this->default : $this->value;
        return '<input type="text" '.parent::renderAttributes($extra).' value="'. escape($this->value).'"/>';
    }
}

class FieldTextarea extends FormField {
    public function render($extra='') {
        return '<textarea '.parent::renderAttributes($extra).' >'.escape($this->value).'</textarea>';
    }
}

class FieldSelect extends FormField {
    public $size = 1;
    public $options = [];

    public function __construct($id, Array $default = null, Array $options = null, $size=1) {
        parent::__construct($id, $default, null);
        if ( !is_array($this->value) ) {
            $this->value = [ $this->value ];
        }
        $this->options = $options;
        $this->size = $size;
    }

    public function render($extra='') {
        if (!is_array($this->value)) {
            $this->value = [$this->value];
        }
        $out = '<select '.$this->renderAttributes($extra).' size="'.$this->size.'"'.($this->size>1?' multiple="multiple"':'').'>';
        if ( is_array($this->options) ) {
            foreach ($this->options as $id => $data) {
                if (is_array($data)) { // OPTGROUP
                    $out .= '<optgroup label="' . $id . '">';
                    foreach ($data as $kk => $vv) { // OPTION
                        $out .= '<option value="' . $kk . '"' . (in_array($kk, $this->value) ? ' selected="selected"' : '') . '>' . escape($vv) . '</option>';
                    }
                    $out .= '</optgroup>';
                } else { // OPTION
                    $out .= '<option value="' . $id . '"' . (in_array($id, $this->value) ? ' selected="selected"' : '') . '>' . escape($data) . '</option>';
                }
            }//foreach
        }
        $out .= '</select>';
        return $out;
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
