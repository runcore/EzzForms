<?php
use EzzForms as Ezz;
include('./EzzForms/autoexec.php');

// Options
$towns = ['Towns'=>[123=>'Moskow',435=>'New York']];

// Form
$form = Ezz\form('exampleForm','./', 'POST')->fields([
    Ezz\text('login')->def(2)->validation(['required minlen:3 regexp:/[\w\-]+/i'])
    ,Ezz\password('password')->validation('required minlen:8')
    ,Ezz\password('password2')->validation('required equalto:password')
    ,Ezz\select('town')->def([14])->options($towns)->size(1)
    ,Ezz\checkbox('towns2')->def([435])->options($towns)
    ,Ezz\radio('towns3')->options($towns)
    ,Ezz\textarea('text')
    ,Ezz\file('file2')
    ,Ezz\hidden('token')->def(md5(microtime(1)))
    ,Ezz\submit('Отправить')
]);

// Processing ...
if ( $form->isSubmit() ) {
    if (  $form->isValid() ) {
        $values = $form->getValues();
//        Ezz\pr( $values );
    }
}

$formHtml = $form->render();

// Form code
echo '<hr />';
$html = str_replace('><', ">\n<", Ezz\escape( $formHtml ) );
Ezz\pr($html);

// Form render
echo '<hr />';
echo '<a href="./">Home</a>';
echo $formHtml;

// Form object inside
echo '<hr />';
Ezz\pr($form);

/*
$ef = new ExampleForm();
if ($ef->isSubmit() ) {
    if ($ef->isValid()) {
        $values = $ef->getValues();
        //
        //EF\pr($values);
    } else {
        //EF\pr('FORM IS INVALID');
    }
}
echo '<hr />';
$form = EF\escape( $ef->render() );
$form = str_replace('><', ">\n<", $form);
EF\pr($form);

echo '<hr />';
echo '<a href="./">Home</a>';
echo $ef->render();

echo '<hr />';
EF\pr($ef);

*/