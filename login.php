<?php
session_start();

include 'librerie/Database.php';
include 'librerie/metodi.php';

$db = new Database();

$log = get_param("login");
$nome = get_param("nome");
$pass = get_param("pass");


$login_success = $db->login($nome, $pass);

     

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>

    <!-- Bootstrap 5.3 CSS -->
    <link rel="stylesheet" href="bootstrap-5.3.3/css/bootstrap.min.css">

</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Navbar</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                  <a class="nav-link"  href="ordini.php">
                      Ordini
                  </a>
              </li>
                <li class="nav-item">
                    <a class="nav-link" href="utenti.html">Utenti</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="Comunicazioni.html">Comunicazioni</a>
              </li>
                <li class="nav-item">
                    <a class="nav-link " aria-disabled="true">Configurazioni</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
    <!-- Form di login -->
    <div class="container p-5">
        <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 col-sm-8">
                <h2 class="mb-4 text-center">Login amministratore</h2>
                <form action="login.php" method="GET">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome</label>
                        <input type="text" name="nome" id="nome" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="pass" class="form-label">Password</label>
                        <input type="password" name="pass" id="pass" class="form-control" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" id="login" name="login" value="true" class="btn btn-primary">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <input type="hidden" id="token" name="token" value="<?php echo $login_success; ?>">    <!-- Bootstrap Bundle JS e jQuery -->
    <script src="bootstrap-5.3.3/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>



<script>
    const token = document.getElementById('token').value;

    if (token) {
        sessionStorage.setItem('token', token); // Salva il token nel sessionStorage

        window.location.href = 'ordini.php';  // Modifica il percorso a seconda della pagina a cui vuoi fare il redirect
    } else {
        console.log("Login fallito o token mancante.");
    }
</script>

