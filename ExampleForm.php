<?php
use EzzForms as EF;

class ExampleForm extends EF\Form {
//    protected $isCsrfProtectionEnabled = false;
    public function __construct() {
        parent::__construct( 'exampleForm' );
        // Default options
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
        // Add Fields
        $this->add([
            new EF\FieldText(    'login',    '2', ['required'
//                ,'regexp:/^[a-z_]+$/i'
//                ,'minlen:3 maxlen:5 '
                //,'int float'
                //,'ipv4'
                //,'datetime'
            ]),
            //new EF\FieldPassword('password', '',             'required minlen:8' ),
            new EF\FieldText('password', '',             'required minlen:3' ),
            new EF\FieldText('password2', '',            'equalto:password' ),
            //new EF\FieldTextarea('text3',    '',             [] ),
            //new EF\FieldHidden(  'token2',   '2e2ee34r34r3', ['required'] ),
            //new EF\FieldSelect(  'town',     [14],           $options['town'], 1 ),
            //new EF\FieldFile(    'upload', 1024*1024*2 ),
            //new EF\FieldCheckbox('oss',      [232,456],      $options['town'] ),
            //new EF\FieldRadio(   'oss2',     [232],          $options['town'] ),
            //
            new EF\FieldSubmit('submit1', 'Logged In')
        ]);
    }
}
