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




?>


<!DOCTYPE html>
<html lang="it">
  <head>
    <title>Menu</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Josefin+Sans" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nothing+You+Could+Do" rel="stylesheet">

    <link rel="stylesheet" href="css/open-iconic-bootstrap.min.css">
    <link rel="stylesheet" href="css/animate.css">
    
    <link rel="stylesheet" href="css/owl.carousel.min.css">
    <link rel="stylesheet" href="css/owl.theme.default.min.css">
    <link rel="stylesheet" href="css/magnific-popup.css">

    <link rel="stylesheet" href="css/aos.css">

    <link rel="stylesheet" href="css/ionicons.min.css">

    <link rel="stylesheet" href="css/sidebar.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">


    <link rel="stylesheet" href="css/bootstrap-datepicker.css">
    <link rel="stylesheet" href="css/jquery.timepicker.css">

    
    <link rel="stylesheet" href="css/flaticon.css">
    <link rel="stylesheet" href="css/icomoon.css">
    <link rel="stylesheet" href="css/style.css">
  </head>
  <body>
  	<nav class="navbar navbar-expand-lg navbar-dark ftco_navbar bg-dark ftco-navbar-light" id="ftco-navbar">
	    <div class="container">
	      <a class="navbar-brand" href="index.html"><span class="flaticon-pizza-1 mr-1"></span>Nirvana<br><small>Pub Pizzeria</small></a>
	      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#ftco-nav" aria-controls="ftco-nav" aria-expanded="false" aria-label="Toggle navigation">
	        <span class="oi oi-menu"></span> 
	      </button>
	      <div class="collapse navbar-collapse" id="ftco-nav">
	        <ul class="navbar-nav ml-auto">
	          <li class="nav-item"><a href="index.html" class="nav-link">Home</a></li>
	          <li class="nav-item active"><a href="menu.php" class="nav-link">Menu</a></li>
	          <li class="nav-item"><a href="blog.html" class="nav-link">Blog</a></li>
	          <li class="nav-item"><a href="contact.html" class="nav-link">Contact</a></li>
	        </ul>
	      </div>
		  </div>
	  </nav>
    <!-- END nav -->

    <style>
    .hero {
    position: relative;
    height: 50vh; /* Altezza metà pagina */
    background-size: cover;
    background-position: center;
}

.overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5); /* Overlay scuro con opacità */
    z-index: 0; /* Posizionamento dell'overlay sopra l'immagine */
}

.hero .container {
    position: relative;
    z-index: 0; /* Contenuto sopra l'overlay */
}
</style>

    <section class="hero" style="background-image: url('images/bg_3.jpg');">
    <div class="overlay"></div>
    <div class="container h-100 d-flex justify-content-center align-items-center">
        <div class="row text-center">
            <div class="col-md-7">
                <h1 class="mb-3 bread">Menu</h1>
            </div>
        </div>
    </div>
</section>
    
		<section class="ftco-section">
    	<div class="container">
    		<div class="row justify-content-center mb-5 pb-3">
          <div class="col-md-7 heading-section ftco-animate text-center">
            <h2 class="mb-4">Our Menu</h2>
			<p class="flip"><span class="deg1"></span><span class="deg2"></span><span class="deg3"></span></p>
            <p style="margin-top: 40px;">Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the blind texts.</p>
          </div>
        </div>
    	</div>
    	<div class="container-wrap">
    		<div class="row no-gutters d-flex">
			<?php
                        foreach ($aPRODOTTI as $row) {
                            echo '<div class="col-lg-4 d-flex ftco-animate">
                                <div class="services-wrap d-flex" >
                                    <a href="#" class="img" style="background-image: url(images/pizza-1.jpg);"></a>
                                    <div class="text p-4">
                                        <h3>' . $row['titolo'] . '</h3>
                                        <p>' . $row['descrizione'] . '</p>
                                        <button href="#" class="ml-2 btn btn-white btn-outline-white" onclick="aggiungiProdotto(' . $row['id'] . ')">Aggiungi al Carrello</button>
                                    </div>
                                </div>
                            </div>';
                        }
                        ?>
    			
    			</div>
    		</div>
    	</div>

      

    </section>

<!-- Bottone per aprire il carrello -->
<button id="btnCarrello" class="btn btn-primary btn-lg shadow-lg">
    <i class="fas fa-shopping-cart">Carrello</i>
</button>

<!-- Sidebar carrello che scorre da destra -->
<div id="carrello" class="bg-dark text-light shadow-lg">
    <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
        <h2 class="cart-title m-0">Carrello</h2>
        <span class="close-cart btn btn-danger btn-sm">&times;</span>
    </div>
    <div class="p-3">
        <table class="table table-hover table-borderless text-light" id="cartTable">
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
    </div>
    <div class="p-3 border-top">
        <button class="btn btn-success btn-block btn-lg" href="riepilogo.html" onclick="ordina()">Ordina Ora</button>
    </div>
</div>


<footer class="ftco-footer ftco-section img">
    <div class="overlay"></div>
    <div class="container">
        <div class="row mb-5">
        <div class="col-lg-4 col-md-6 mb-5">
                <div class="ftco-footer-widget mb-4">
                    <h2 class="ftco-heading-2">Recapiti</h2>
                    <div class="block-23 mb-3">
                        <ul>
                            <li><span class="icon icon-map-marker"></span><span class="text">Via Madonna delle carceri 4, Camerino, MC 62032</span></li>
                            <li><a href="#"><span class="icon icon-phone"></span><span class="text">+39 3295695194</span></a></li>
                            <li><a href="#"><span class="icon icon-envelope"></span><span class="text">esempio@mail.com</span></a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-5">
                <div class="ftco-footer-widget mb-4 ml-md-4">
                    <h2 class="ftco-heading-2">Pagine</h2>
                    <ul class="list-unstyled">
                        <li><a href="#" class="py-2 d-block">Home</a></li>
                        <li><a href="#" class="py-2 d-block">Menu</a></li>
                        <li><a href="#" class="py-2 d-block">Blog</a></li>
                        <li><a href="#" class="py-2 d-block">Contatti</a></li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-5">
                <div class="ftco-footer-widget mb-4">
                    <h2 class="ftco-heading-2">Recent Blog</h2>
                    <div class="block-21 mb-4 d-flex">
                        <a class="blog-img mr-4" style="background-image: url(images/image_1.jpg);"></a>
                        <div class="text">
                            <h3 class="heading"><a href="#">Even the all-powerful Pointing has no control about</a></h3>
                            <div class="meta">
                                <div><a href="#"><span class="icon-calendar"></span> Sept 15, 2018</a></div>
                                <div><a href="#"><span class="icon-person"></span> Admin</a></div>
                                <div><a href="#"><span class="icon-chat"></span> 19</a></div>
                            </div>
                        </div>
                    </div>
                    <div class="block-21 mb-4 d-flex">
                        <a class="blog-img mr-4" style="background-image: url(images/image_2.jpg);"></a>
                        <div class="text">
                            <h3 class="heading"><a href="#">Even the all-powerful Pointing has no control about</a></h3>
                            <div class="meta">
                                <div><a href="#"><span class="icon-calendar"></span> Sept 15, 2018</a></div>
                                <div><a href="#"><span class="icon-person"></span> Admin</a></div>
                                <div><a href="#"><span class="icon-chat"></span> 19</a></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="row">
            <div class="col-md-12 text-center">
                <p>
                    Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved
                </p>
            </div>
        </div>
    </div>
</footer>

    
  

  <!-- loader -->
  <div id="ftco-loader" class="show fullscreen"><svg class="circular" width="48px" height="48px"><circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee"/><circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10" stroke="#F96D00"/></svg></div>


  <script src="js/jquery.min.js"></script>
  <script src="js/jquery-migrate-3.0.1.min.js"></script>
  <script src="js/popper.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/jquery.easing.1.3.js"></script>
  <script src="js/jquery.waypoints.min.js"></script>
  <script src="js/jquery.stellar.min.js"></script>
  <script src="js/owl.carousel.min.js"></script>
  <script src="js/jquery.magnific-popup.min.js"></script>
  <script src="js/aos.js"></script>
  <script src="js/jquery.animateNumber.min.js"></script>
  <script src="js/bootstrap-datepicker.js"></script>
  <script src="js/jquery.timepicker.min.js"></script>
  <script src="js/scrollax.min.js"></script>
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBVWaKrjvy3MaE7SQ74_uJiULgl1JY0H2s&sensor=false"></script>
  <script src="js/google-map.js"></script>
  <script src="js/main.js"></script>
    
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
                    // console.log("okokok")
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
            if (prodotti && prodotti !== false && Array.isArray(prodotti) && prodotti.length > 0) {
                let tableHTML = "";
                prodotti.forEach(function(prodotto) {
                    tableHTML += "<tr>";
                    tableHTML += "<td>" + prodotto.titolo + "</td>";
                    tableHTML += "<td>" + prodotto.prezzo + "€</td>";
                    tableHTML += "<td class='d-flex justify-content-between align-items-center'>";

                    // Bottone per diminuire quantità
                    tableHTML += "<div class='input-group'>";
                    tableHTML += "<button class='btn btn-outline-secondary btn-sm' type='button' onclick='diminuisciQuantita(" + prodotto.id_prodottiCarrello + ")'>";
                    tableHTML += "<i class='fas fa-minus-circle'></i>";
                    tableHTML += "</button>";

                    // Input quantità
                    tableHTML += "<input type='text' class='form-control form-control-sm text-center' id='quantity_" + prodotto.id_prodottiCarrello + "' value='"+prodotto.numero_prodotti+"' min='1' max='100' readonly>";

                    // Bottone per incrementare quantità
                    tableHTML += "<button class='btn btn-outline-secondary btn-sm' type='button' onclick='incrementaQuantita(" + prodotto.id_prodottiCarrello + ")'>";
                    tableHTML += "<i class='fas fa-plus-circle'></i>";
                    tableHTML += "</button>";
                    tableHTML += "</div>";

                    tableHTML += "</td>";
                    // Bottone per eliminare prodotto
                    tableHTML += "<td><button class='btn btn-danger btn-sm delete-btn' data-id='" + prodotto.id_prodottiCarrello + "' onclick='eliminaprodotto(" + prodotto.id_prodottiCarrello +")'>";
                    tableHTML += "<i class='fas fa-trash-alt'></i> Delete</button></td>";
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
            url: 'action.php?_action=incrementa&_id_prodottiCarrello=' + encodeURIComponent(id_prodottiCarrello),
            dataType: 'json',
            success: function (result) {
                if (result=1) {
                    riempiCarrello()
                } else {
                    console.log("err")
                }
            },
            error: function () {
                console.log("Errore nell'incremento.");
            }
        });
    }

    function diminuisciQuantita(id_prodottiCarrello) {
        $.ajax({
            type: "POST",
            url: 'action.php?_action=decrementa&_id_prodottiCarrello=' + encodeURIComponent(id_prodottiCarrello),
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

    function ordina() {
    $.ajax({
        type: "POST",
        url: 'action.php?_action=ordina',
        dataType: 'json',
        success: function (result) {
            console.log(result);
            if (result.status === 1) {
                riempiCarrello();
                //alert("Ordine completato con successo! Totale: €" + result.data.totale.toFixed(2));
                console.log("ID Carrello:", result.data.id_carrello);
                console.log("Prodotti ordinati:", result.data.prodotti);

                // Reindirizzamento alla pagina riepilogo.html
                window.location.href = 'riepilogo.html';
            } else {
                console.error("Errore nell'elaborazione dell'ordine:", result.message);
                alert("Si è verificato un errore durante l'elaborazione dell'ordine. Riprova più tardi.");
            }
        },
        error: function (xhr, status, error) {
            console.error("Errore nella richiesta AJAX:", status, error);
            console.error("Risposta del server:", xhr.responseText);
            
            let errorMessage = "Si è verificato un errore di comunicazione con il server.";
            if (xhr.responseText.startsWith("<br />") || xhr.responseText.startsWith("<b>")) {
                errorMessage += " Il server ha generato un errore PHP. Controlla i log del server per maggiori dettagli.";
            }
            
            alert(errorMessage + " Riprova più tardi.");
        }
    });
}


</script>