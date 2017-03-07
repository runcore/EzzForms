<?php
namespace EzzForms;

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

        $this->fieldDefaultValue = [];
        $this->fieldValue = [];

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
