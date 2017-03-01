<?php
use EzzForms as EF;

/**
 * Class ExampleForm
 */
class ExampleForm extends EF\Form {

    protected $isCsrfProtectionEnabled = false;

    public function __construct() {

        parent::__construct( 'exampleForm' );

        $options = [];
        $options['town'] = [
            'RU' => [
                12=>'Moskow',
                13=>'Piter',
            ],
            'US' => [
                14=>'New York'
            ]
        ];

        $this->add([
            new EF\FieldText( 'login', 'Default text', ['required','regexp'=>'^[a-zA-Z0-9_]+$'] ),
            new EF\FieldSelect('town', [14], $options['town'] ),
            new EF\FieldFile('upload'),
            //
            new EF\FieldSubmit('', 'Logged In')
        ]);

    }

}
