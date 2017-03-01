<?php

include('./EzzForms.php');
include('./ExampleForm.php');

use EzzForms as EF;

$ef = new ExampleForm();
if ($ef->isSubmit() && $ef->isValid()) {
    //
}
echo '<hr />';
$form = EF\escape( $ef->render() );
$form = str_replace('><', ">\n<", $form);
echo EF\pr($form);

echo '<hr />';
echo '<a href="./">Home</a>';
echo $ef->render();

echo '<hr />';
EF\pr($ef);
