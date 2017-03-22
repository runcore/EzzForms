<?php
include('../Ezz/autoexec.php');

// Form
$form = Ezz\form('formLogin')->action('./formLogin.php')->method('POST')
    ->fields([
         Ezz\text('login')->rules(['required minlen:5','regexp'=>['/^[\w]+$/i','Некорректный логин'] ])
        ,Ezz\password('password')->rules('required minlen:8')
        ,Ezz\checkbox('rememberMe')->options([1=>'Запомнить меня'])
        ,Ezz\submit('submitLogin')
    ])
    ->template('./formLogin.tmpl.php')
;

// submit
if ( $form->isSubmit() ) {
    if ( $form->isValid() ) {
        $values = $form->getValues();
        // Processing data from Form
        print_r( $values );
    }
}

echo $form->render();


