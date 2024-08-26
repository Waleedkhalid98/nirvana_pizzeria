<?php
session_start(); // Aggiungi questa riga all'inizio del file

include 'librerie/Database.php';
include 'librerie/metodi.php';

$db = new Database();

$aPRODOTTI = get_db_array("prodotto");

//controlla se è presente id utente altrimenti lo crea
if (!isset($_SESSION['id_utente'])) {
    $_SESSION['id_utente'] = uniqid();
    setcookie('id_utente', $_SESSION['id_utente'], time() + (86400 * 30), "/"); // Cookie valido per 30 giorni
}

//prendo id_utente
$id_utente = $_SESSION['id_utente'];

echo "IL TUO ID = " . $id_utente;


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
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="container">
                    <div class="row">
                        <?php
                        foreach ($aPRODOTTI as $row) {
                            echo '<div class="col-4">
                                <div class="card" >
                                    <img class="card-img-top" src="..." alt="Card image cap">
                                    <div class="card-body">
                                        <h5 class="card-title">' . $row['titolo'] . '</h5>
                                        <p class="card-text">' . $row['descrizione'] . '</p>
                                        <a href="#" class="btn btn-primary" onclick="aggiungiProdotto(' . $row['id'] . ')">Aggiungi al Carrello</a>
                                    </div>
                                </div>
                            </div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Bottone per aprire il carrello -->
    <button id="btnCarrello" class="btn btn-success">Carrello</button>

    <!-- Carrello che scorre da destra -->
    <div id="carrello">
        <span class="close-cart">&times;</span>
        <h2>Carrello</h2>
        <table class="table table-responsive" id="cartTable">
            <thead>
                <tr>
                    <th>Prodotto</th>
                    <th>Prezzo</th>
                    <th>Quantità</th>
                </tr>
            </thead>
            <tbody>
                <!-- I prodotti aggiunti verranno inseriti qui -->
            </tbody>
        </table>

        <button class="btn btn-primary mb-0">aaa</button>
    </div>

    <script src="node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>


<script>
$(document).ready(function() {
    // Qui puoi chiamare il tuo metodo
    riempiCarrello(); // Ad esempio, chiama la funzione per riempire il carrello
});

    // Funzione per aggiungere prodotto (può essere connessa con il tuo backend tramite AJAX)
    function aggiungiProdotto(id_prodotto) {
        $.ajax({
            type: "POST",
            url: 'action.php?_action=aggiungiProdotto&_k=' + encodeURIComponent(id_prodotto),
            cache: false,
            contentType: false,
            processData: false,
            success: function (result) {
                if(result == 1){
                    alert("Prodotto aggiunto al carrello!");
                    riempiCarrello();
                }else{
                    alert("Errore durante l'aggiunta del prodotto.");
                }
            },
            error: function () {
                console.log("Chiamata fallita, si prega di riprovare...");
            }
        });
    }

    function riempiCarrello() {
    $.ajax({
        type: "POST",
        url: 'action.php?_action=FillCarrello',
        dataType: 'json',
        success: function (prodotti) {

            if (prodotti.length > 0) {
                let tableHTML = "";
                prodotti.forEach(function(prodotto) {
                    tableHTML += "<tr>";
                    tableHTML += "<td>" + prodotto.titolo + "</td>";
                    tableHTML += "<td>" + prodotto.prezzo + "€</td>";
                    tableHTML += "<td>1</td>"; // Qui puoi aggiungere la logica per gestire la quantità
                    tableHTML += "</tr>";
                });
                $('#cartTable tbody').html(tableHTML); // Inserisce i prodotti nella tabella
            } else {
                $('#cartTable tbody').html("<tr><td colspan='3'>Il carrello è vuoto.</td></tr>");
            }
        },
        error: function () {
            console.log("Errore nel recupero dei prodotti.");
        }
    });
}


   

    // Mostra il carrello
    $('#btnCarrello').on('click', function () {
         $('#carrello').toggleClass('show');
         riempiCarrello();
    });


    // Nasconde il carrello
    $('.close-cart').on('click', function () {
        $('#carrello').toggleClass('show');

    });

</script>
