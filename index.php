<?php

include('./EzzForms/autoexec.php');
include('./ExampleForm.php');

use EzzForms as EF;

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
