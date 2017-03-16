<?php
session_start();
use EzzForms as Ezz;

include('./EzzForms/autoexec.php');

// Options
$towns = ['Столицы'=>[123=>'Moskow','WA'=>'Washington']];

// Form
$form = Ezz\form('exampleForm')->csrf(false)->action('./')->method('POST')
    ->fields([
         Ezz\text('login')->def('runcore')->rules(['required minlen:3','regexp'=>['/^[\w]+$/i','Некорректный логин']
             , 'uppercase'=>[function($value){return $value===mb_strtoupper($value);}
             //,'Need upper case'
            ]
         ])
        ,Ezz\password('password')->rules('required minlen:8')
        ,Ezz\password('password2')->rules(['required','equalto'=>['password','Пароли несовпадают'] ])
        ,Ezz\select('towns')->def([14])->options($towns)->size(1)
        ,Ezz\checkbox('towns2')->def(['NY'])->options($towns)
        ,Ezz\radio('towns3')->options($towns)
        ,Ezz\textarea('text')->rules('required')
//      ,Ezz\file('file2')
        ,Ezz\hidden('token')->def(md5(microtime(1)))
        ,Ezz\submit('submit1')
    ])
//    ->bootstrap(true)->template('./form.bootstrap.php')
    ->bootstrap(false)//->template('./form.template.php')
;

// Processing ...
if ( $form->isSubmit() ) {
    if (  $form->isValid() ) {
        $values = $form->getValues();
//        Ezz\pr( $values );
    }
}

echo $form->render();
