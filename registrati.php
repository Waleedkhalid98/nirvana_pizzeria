<?php 


include 'librerie/Database.php';
include 'librerie/metodi.php';

$db = new Database();


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

    <div class="container mt-5">



    <label for="username">USERNAME</label>
    <input type="text" id="username" name="username">

    
    <label for="pass">PASSWORD</label>
    <input type="text" id="pass" name="pass">


    <button class="btn btn-primary" id="regista" name="regista" value="true" onclick="login()">REGISTRATI </button>


    </div>


    


</body>
</html>
<script>



function login() {
    var username = $('#username').val();
    var password = $('#pass').val();
    
    $.ajax({
        type: "POST",
        url: 'action.php?_action=login&_username=' + encodeURIComponent(username) + "&_password=" + encodeURIComponent(password),
        cache: false,
        success: function (result) {
            alert("ok")
            saveToken(result); // Salva il token nella localStorage
            var token = getTokenFromLocalStorage(); // Recupera il token dalla localStorage
            alert(token); // Mostra il token
        },
        error: function () {
            console.log("Chiamata fallita, si prega di riprovare...");
        }
    });
}

function saveToken(token) {
    localStorage.setItem('userToken', token);
}

function getTokenFromLocalStorage() {
    return localStorage.getItem('userToken');
}



</script>