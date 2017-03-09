<?php
use EzzForms as Ezz;

// todo: this bad!! need change to simple way work with form and fields

$login = $fields['login'];
/**
 * @var Ezz\FormField $login
 */
?>
<h2>Custom form template</h2>
<table>
<tr>
<td><?= $login->label('Логин') ?></td>
<td><?= $login->render() ?></td>
<td><?= $login->errors() ?></td>
</tr>
</table>