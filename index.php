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
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <!-- I prodotti aggiunti verranno inseriti qui -->
            </tbody>
        </table>

        <button class="btn btn-primary mb-0">aaa</button>
    </div>
    <div class="col-lg-3">
                  <div class="input-group">
                      <span class="input-group-btn">
                          <button type="button" class="quantity-left-minus btn  "  data-type="minus" data-field="">
                            <svg class="bi currency-euro .text-primary" role="img" aria-label="Tools">
                              <use xlink:href="icon/bootstrap-icons.svg#dash-circle"/> 
                            </svg>                                       
                          </button>
                      </span>
                      <div class="col-2">
                        <input type="text" id="quantity" name="quantity" class="form-control "  value="1" min="1" max="100"  >
                      </div>
                      <span class="input-group-btn">
                          <button type="button" class="quantity-right-plus btn  " data-type="plus" data-field="">
                          <svg class="bi currency-euro .text-primary" role="img" aria-label="Tools">
                            <use xlink:href="icon/bootstrap-icons.svg#plus-circle"/> 
                          </svg>
                          </button>
                      </span>
                  </div>
              </div>
    <script src="node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>


<script>
    $(document).ready(function() {
        // Qui puoi chiamare il tuo metodo
        riempiCarrello(); // Ad esempio, chiama la funzione per riempire il carrello
    });




    // Mostra il carrello
    $('#btnCarrello').on('click', function () {
            $('#carrello').toggleClass('show');
            riempiCarrello();
    });


    // Nasconde il carrello
    $('.close-cart').on('click', function () {
        $('#carrello').toggleClass('show');

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


    
    function eliminaprodotto(id_prodottiCarrello){
        $.ajax({
            type: "POST",
            url: 'action.php?_action=eliminaProdotto&_id_prodottiCarrello=' + encodeURIComponent(id_prodottiCarrello),
            dataType: 'json',
            success: function (result) {

                if (result=1) {
                    console.log("okokok")
                    riempiCarrello()
                } else {
                    alert("errore")
                }
            },
            error: function () {
                console.log("Errore nel recupero dei prodotti.");
            }
        });
    }

    function riempiCarrello() {
    $.ajax({
        type: "POST",
        url: 'action.php?_action=FillCarrello',
        dataType: 'json',
        success: function (prodotti) {
            console.log("p"+JSON.stringify(prodotti));
            if (prodotti && prodotti !== false && Array.isArray(prodotti) && prodotti.length > 0) {
                let tableHTML = "";
                prodotti.forEach(function(prodotto) {
                    console.log(prodotto);
                    tableHTML += "<tr>";
                    tableHTML += "<td>" + prodotto.titolo + "</td>";
                    tableHTML += "<td>" + prodotto.prezzo + "€</td>";
                    tableHTML += "<td class='d-flex justify-content-between align-items-center'>";
                    tableHTML += "<div class='col-4 p-0 '>";
                    tableHTML += "<button class='btn btn-outline-secondary btn-sm rounded-circle' type='button' onclick='diminuisciQuantita(" + prodotto.id_prodottiCarrello + ")'>";
                    tableHTML += "<svg width='16' height='16' fill='currentColor'><use xlink:href='icon/bootstrap-icons.svg#dash-circle'/></svg>";
                    tableHTML += "</button>";
                    tableHTML += "</div>";
                    tableHTML += "<div class='col-4 p-0'>";
                    tableHTML += "<input type='text' class='form-control form-control-sm text-center' id='quantity_" + prodotto.id_prodottiCarrello + "' value='"+prodotto.numero_prodotti+"' min='1' max='100'>";
                    tableHTML += "</div>";
                    tableHTML += "<div class='col-4 p-0'>";
                    tableHTML += "<button class='btn btn-outline-secondary btn-sm rounded-circle' type='button' onclick='incrementaQuantita(" + prodotto.id_prodottiCarrello + ")'>";
                    tableHTML += "<svg width='16' height='16' fill='currentColor'><use xlink:href='icon/bootstrap-icons.svg#plus-circle'/></svg>";
                    tableHTML += "</button>";
                    tableHTML += "</div>";
                    tableHTML += "</td>";
                    tableHTML += "<td><button class='btn btn-danger btn-sm delete-btn' data-id='" + prodotto.id_prodottiCarrello + "' onclick='eliminaprodotto(" + prodotto.id_prodottiCarrello +")'>Delete</button></td>";
                    tableHTML += "</tr>";
                });
                $('#cartTable tbody').html(tableHTML); // Inserisce i prodotti nella tabella
            } else {
                $('#cartTable tbody').html("<tr><td colspan='4'>Il carrello è vuoto.</td></tr>");
            }
        },
        error: function () {
            console.log("Errore nel recupero dei prodotti.");
        }
    });
}

    
    function incrementaQuantita(id_prodottiCarrello) {
        $.ajax({
            type: "POST",
            url: 'action.php?_action=incrementa',
            dataType: 'json',
            success: function (result) {
                console.log("p"+JSON.stringify(result));
                if (result=1) {
                    riempiCarrello()
                } else {
                    console.log("err")
                }
            },
            error: function () {
                console.log("Errore nel recupero dei prodotti.");
            }
        });
    }

    function diminuisciQuantita(id_prodottiCarrello) {
        $.ajax({
            type: "POST",
            url: 'action.php?_action=decrementa',
            dataType: 'json',
            success: function (result) {
                console.log(result)
                if (result=1) {
                    riempiCarrello()
                } else {
                    console.log("err")
                }
            },
            error: function () {
                console.log("Errore nel recupero dei prodotti.");
            }
        });
    }

   


</script>
