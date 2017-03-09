<?php
namespace EzzForms;

/**
 * Class FieldFile
 * @package EzzForms
 */
class FieldFile extends FormField {

    /**
     * @var bool
     */
    protected $isFileField = true;

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

    // todo: need extend file field. processing, control size,file types, upload errors ...

}
