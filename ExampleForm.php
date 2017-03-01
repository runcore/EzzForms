<?php
use EzzForms as EF;

/**
 * Class ExampleForm
 */
class ExampleForm extends EF\Form {

//    protected $isCsrfProtectionEnabled = false;

    public function __construct() {

        parent::__construct( 'exampleForm' );

        $options = [];
        $options['town'] = [
            'RU' => [
                12=>'Moskow',
                13=>'Piter',
                15=>'Novosib',
            ],
            'US' => [
                14=>'New York'
            ]
        ];
        $options['oss'] = [
            'Test'=>[
                123=>'Windows',
                232=>'Linux',
                456=>'Mac OS',
            ],
        ];
//        $options['oss'] = [];

        $this->add([
            new EF\FieldText(    'login',    'Default text', ['required','regexp'=>'^[a-zA-Z0-9_]+$'] ),
            new EF\FieldPassword('password', '',             ['required','regexp'=>'^[a-zA-Z0-9_]+$'] ),
            new EF\FieldTextarea('text3',    '',             [] ),
            new EF\FieldHidden(  'token2',   '2e2ee34r34r3', ['required'] ),
            //new EF\FieldSelect(  'town',     [14],           $options['town'],5 ),
            new EF\FieldFile(    'upload', 1024*1024*2 ),
            //new EF\FieldCheckbox('oss',      [232,456],      $options['town'] ),
            //new EF\FieldRadio(   'oss2',     [232],          $options['town'] ),
            //
            new EF\FieldSubmit('', 'Logged In')
        ]);

    }

}
