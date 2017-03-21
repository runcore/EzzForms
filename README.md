
# Ezz\Forms
Forms for easy embedding

Example:
```php
$form = Ezz\form('registerForm')->->action('./')->method('POST')
    ->fields([
         Ezz\text('login')->rules(['required minlen:5','regexp'=>['/^[\w]+$/i','Incorrect login field']])
        ,Ezz\password('password')->rules('required minlen:8')
        ,Ezz\password('password2')->rules(['required','equalto'=>['password','Passwords not equals'] ])
        ,Ezz\text('email')->rules('required email')
        ,Ezz\select('sex')->options( [1=>'Male',2=>'Female'] )
        ,Ezz\submit('submitRegister')
    ])
    ->template('./form.template.php')
;
// Processing ...
if ( $form->isSubmit() ) {
    if (  $form->isValid() ) {
        $values = $form->getValues();
        // processing data from Form
    }
}
// render form
echo $form->render();
```
