<?php

include('./EzzForms.php');
include('./ExampleForm.php');

use EzzForms as EF;

$ef = new ExampleForm();
if ($ef->isSubmit() && $ef->isValid()) {
    //
}
echo '<hr />';
echo EF\escape( $ef->render() );

echo '<hr />';
echo '<a href="./">Home</a>';
echo $ef->render();

echo '<hr />';
EF\pr($ef);
