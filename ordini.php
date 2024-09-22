<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionale Pizzeria</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Pizzeria Gestionale</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="#" id="ordiniLink">Ordini</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" id="utentiLink">Utenti</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" id="configurazioniLink">Configurazioni</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                Elenco Ordini
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Cliente</th>
                            <th scope="col">Pizza</th>
                            <th scope="col">Quantità</th>
                            <th scope="col">Stato</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="row">1</th>
                            <td>Mario Rossi</td>
                            <td>Margherita</td>
                            <td>2</td>
                            <td>In preparazione</td>
                        </tr>
                        <tr>
                            <th scope="row">2</th>
                            <td>Giulia Bianchi</td>
                            <td>Quattro Formaggi</td>
                            <td>1</td>
                            <td>Consegnato</td>
                        </tr>
                        <tr>
                            <th scope="row">3</th>
                            <td>Luca Verdi</td>
                            <td>Diavola</td>
                            <td>3</td>
                            <td>In consegna</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>