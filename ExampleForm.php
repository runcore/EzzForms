<?php
use EzzForms as EF;

/**
 * Class ExampleForm
 */
class ExampleForm extends EF\Form {

    public function __construct() {

        parent::__construct( 'exampleForm' );

        $this->add([
            new EF\FieldText( 'login', 'Default text', ['required','regexp'=>'^[a-zA-Z0-9_]+$'] ),
            new EF\FieldSubmit('', 'Logged In')
        ]);

    }

}