<?php

include('../Ezz/autoexec.php');

// Example of options array
$options = ['Столицы'=>[123=>'Moskow','WA'=>'Washington']];

$form = Ezz\form('exampleForm')
    ->csrf(true)
    ->action('./formFull.php')
    ->method('POST')
    ->fields([
        Ezz\text('login')->def('runcore')->rules(['required rangelen:3,30','regexp'=>['/^[\w]+$/i','Некорректный логин']
            // Example of custom rule
             , 'uppercase'=>[function($value){return $value===mb_strtoupper($value);}
             ,'Need upper case' // custom error message
            ]
        ])
        ,Ezz\text('age')->rules('required float range:-1,10.09 min:-1 max:10.09')
        ,Ezz\password('password')->rules('required minlen:8')
        ,Ezz\password('password2')->rules(['required','equalto'=>['password','Пароли несовпадают'] ])
        ,Ezz\select('towns')->def([14])->options($options)->size(1)
        ,Ezz\checkbox('towns2')->def(['NY'])->options($options)
        ,Ezz\radio('towns3')->options($options['Столицы'])
        ,Ezz\textarea('text')->rules('required')
        ,Ezz\file('avatar')
        ,Ezz\hidden('token')->def(md5(microtime(1)))
        ,Ezz\submit('submit1')
    ])
    ->template('./formFull.tmpl.php')
;

// Form submit
if ( $form->isSubmit() ) {
    if ( $form->isValid() ) {
        $values = $form->getValues();
        // Processing data from Form
        // ...
    }
}

echo $form->render();
