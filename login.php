<?php

require_once ('core/init.php');

if(Input::exists()){

    if(Token::check(Input::get('token'))){

        $validate = new Validate();
        $validation = $validate->check($_POST, array(
            'username' => array(
                'required' => true,
            ),
            'password' => array(
                'required' => true,
            )
        ));

        if($validate->passed()){
            $user = new User();
            $remember = (Input::get('remember')=== "on") ? true : false;

            $login = $user->login(Input::get('username'), Input::get('password'), $remember);

            if($login){
                Redirect::to('index.php');
            }else{
                echo "Username or Password Incorrect";
            }

        }else{
            $errors = $validate->errors();

            foreach($errors as $error){
                echo $error . "<br>";
            }
        }

    }

}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>


<form action="" method="post">
    <div class="field">
        <label for='username'>Username</label>
        <input type="text" name="username" id="username">
    </div>

    <div class="field">
        <label for='password'>Password</label>
        <input type="password" name="password" id="password">
    </div>

    <div class="field">
        <label for="remember">
            <input type="checkbox" name="remember" id="remember">Remember me
        </label>
    </div>

    <input type="hidden" name="token" value="<?php echo Token::genarate(); ?>">
    <input type="submit" value="Login">
</form>



</body>
</html>