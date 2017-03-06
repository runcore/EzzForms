<?php
namespace EzzForms;

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
