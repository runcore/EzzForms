<?php
namespace Ezz;

/**
 * Class Exception
 * @package Ezz
 */
class Exception extends \Exception {

    public function __toString() {
        return $this->getMessage();
    }

}
