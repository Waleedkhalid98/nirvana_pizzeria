<?php
session_start();

include 'librerie/Database.php';
include 'librerie/metodi.php';

$db = new Database();

$log=get_param("login");
$nome=get_param("nome");
$pass=get_param("pass");

if($log)
{
   echo $db->login($nome,$pass);
    
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<form action="login.php">
    <div class="container p-5">
        <div class="row">
            <div class="col-4">
                <label for="nome">NOME</label>
                <input name="nome" id="nome" type="text">
            </div>
            <div class="col-4">
                <label for="pass">PASSWORD</label>
                <input name="pass" id="pass" type="text">
            </div>
            <div class="col-4">
                <button id="login" name="login" value="true" class="btn btn-primary">LOGIN</button>
            </div>
        </div>
    </div>
</form>
</body>
</html>
