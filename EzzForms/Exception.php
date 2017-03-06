<?php
namespace EzzForms;

/**
 * Class Exception
 * @package EzzForms
 */
class Exception extends \Exception {

    public function __toString() {
        return $this->getMessage();
    }

}
