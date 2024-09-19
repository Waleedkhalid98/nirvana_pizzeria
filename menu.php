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
    <title>Pizza - Free Bootstrap 4 Template by Colorlib</title>
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
	        <span class="oi oi-menu"></span> Menu
	      </button>
	      <div class="collapse navbar-collapse" id="ftco-nav">
	        <ul class="navbar-nav ml-auto">
	          <li class="nav-item"><a href="index.html" class="nav-link">Home</a></li>
	          <li class="nav-item active"><a href="menu.html" class="nav-link">Menu</a></li>
	          <li class="nav-item"><a href="services.html" class="nav-link">Services</a></li>
	          <li class="nav-item"><a href="blog.html" class="nav-link">Blog</a></li>
	          <li class="nav-item"><a href="about.html" class="nav-link">About</a></li>
	          <li class="nav-item"><a href="contact.html" class="nav-link">Contact</a></li>
	        </ul>
	      </div>
		  </div>
	  </nav>
    <!-- END nav -->

    <section class="home-slider owl-carousel img" style="background-image: url(images/bg_1.jpg);">

      <div class="slider-item" style="background-image: url(images/bg_3.jpg);">
      	<div class="overlay"></div>
        <div class="container">
          <div class="row slider-text justify-content-center align-items-center">

            <div class="col-md-7 col-sm-12 text-center ftco-animate">
            	<h1 class="mb-3 mt-5 bread">Menu</h1>
            </div>

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

        <button class="btn btn-primary mb-0" onclick="ordina()">aaa</button>
    </div>

    <footer class="ftco-footer ftco-section img">
    	<div class="overlay"></div>
      <div class="container">
        <div class="row mb-5">
          <div class="col-lg-3 col-md-6 mb-5 mb-md-5">
            <div class="ftco-footer-widget mb-4">
              <h2 class="ftco-heading-2">About Us</h2>
              <p>Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the blind texts.</p>
              <ul class="ftco-footer-social list-unstyled float-md-left float-lft mt-5">
                <li class="ftco-animate"><a href="#"><span class="icon-twitter"></span></a></li>
                <li class="ftco-animate"><a href="#"><span class="icon-facebook"></span></a></li>
                <li class="ftco-animate"><a href="#"><span class="icon-instagram"></span></a></li>
              </ul>
            </div>
          </div>
          <div class="col-lg-4 col-md-6 mb-5 mb-md-5">
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
          <div class="col-lg-2 col-md-6 mb-5 mb-md-5">
             <div class="ftco-footer-widget mb-4 ml-md-4">
              <h2 class="ftco-heading-2">Services</h2>
              <ul class="list-unstyled">
                <li><a href="#" class="py-2 d-block">Cooked</a></li>
                <li><a href="#" class="py-2 d-block">Deliver</a></li>
                <li><a href="#" class="py-2 d-block">Quality Foods</a></li>
                <li><a href="#" class="py-2 d-block">Mixed</a></li>
              </ul>
            </div>
          </div>
          <div class="col-lg-3 col-md-6 mb-5 mb-md-5">
            <div class="ftco-footer-widget mb-4">
            	<h2 class="ftco-heading-2">Have a Questions?</h2>
            	<div class="block-23 mb-3">
	              <ul>
	                <li><span class="icon icon-map-marker"></span><span class="text">203 Fake St. Mountain View, San Francisco, California, USA</span></li>
	                <li><a href="#"><span class="icon icon-phone"></span><span class="text">+2 392 3929 210</span></a></li>
	                <li><a href="#"><span class="icon icon-envelope"></span><span class="text">info@yourdomain.com</span></a></li>
	              </ul>
	            </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12 text-center">

            <p><!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
  Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved | This template is made with <i class="icon-heart" aria-hidden="true"></i> by <a href="https://colorlib.com" target="_blank">Colorlib</a>
  <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. --></p>
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
            // console.log("p"+JSON.stringify(prodotti));
            if (prodotti && prodotti !== false && Array.isArray(prodotti) && prodotti.length > 0) {
                let tableHTML = "";
                prodotti.forEach(function(prodotto) {
                    // console.log(prodotto);
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
                alert("Ordine completato con successo! Totale: €" + result.data.totale.toFixed(2));
                console.log("ID Carrello:", result.data.id_carrello);
                console.log("Prodotti ordinati:", result.data.prodotti);
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