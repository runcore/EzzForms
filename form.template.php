<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="./favicon.ico">
    <title>EzzForms demo</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</head>
<body>

<div class="container">

    {formOpen:exampleForm class="form-horizontal"}

    <div class="form-group">
        <div class="col-md-offset-4 col-md-4"><h3><a href="./">EzzForms. Form template demo</a></h3></div>
    </div>

    <div class="form-group">
        {label:login:"Логин" class="col-md-4 control-label"}
        <div class="col-md-4">
            {field:login class="form-control" placeholder="Login"}
            {error:login class="text-danger small"}
        </div>
    </div>

    <div class="form-group"> <!-- has-error -->
            {label:password:"Пароль" class="col-md-4 control-label"}
            <div class="col-md-4">
                {field:password class="form-control" placeholder="пароль"}
                {error:password class="text-danger small"}
            </div>
    </div>

    <div class="form-group">
            {label:password2:"Пароль 2" class="col-md-4 control-label"}
            <div class="col-md-4">
                {field:password2 class="form-control" placeholder="пароль"}
                {error:password2 class="text-danger small"}
            </div>
    </div>

    <div class="form-group">
            {label:towns:"Города" class="col-md-4 control-label"}
            <div class="col-md-4">
                {field:towns class="form-control"}
                {error:towns class="text-danger small"}
            </div>
        </div>
        <div class="form-group">
            {label:towns2:"Города" class="col-md-4 control-label"}
            <div class="col-md-4">
                <div class="checkbox">
                    {field:towns2}
                    {error:towns2 class="text-danger small"}
                </div>
            </div>
        </div>
        <div class="form-group">
            {label:towns3:"Города" class="col-md-4 control-label"}
            <div class="col-md-4">
                <div class="radio">
                    {field:towns3}
                    {error:towns3 class="text-danger small"}
                </div>
            </div>
        </div>

    <div class="form-group">
            {label:text:"Текст комментария" class="col-md-4 control-label"}
            <div class="col-md-4">
                {field:text class="form-control"}
                {error:text class="text-danger small"}
            </div>
        </div>

        <div class="form-group">
            <div class="col-md-offset-4 col-md-4">
                {field:submit1 value="Отправить" class="btn btn-default"}
            </div>
        </div>

    {formClose}

</div> <!-- /container -->

</body>
</html>