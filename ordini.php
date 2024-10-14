<?php
include 'librerie/Database.php';
include 'librerie/metodi.php';

$db = new Database();



?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionale Pizzeria</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="bootstrap-5.3.3/css/bootstrap.min.css">

 
    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }

      .b-example-divider {
        width: 100%;
        height: 3rem;
        background-color: rgba(0, 0, 0, .1);
        border: solid rgba(0, 0, 0, .15);
        border-width: 1px 0;
        box-shadow: inset 0 .5em 1.5em rgba(0, 0, 0, .1), inset 0 .125em .5em rgba(0, 0, 0, .15);
      }

      .b-example-vr {
        flex-shrink: 0;
        width: 1.5rem;
        height: 100vh;
      }

      .bi {
        vertical-align: -.125em;
        fill: currentColor;
      }

      .nav-scroller {
        position: relative;
        z-index: 2;
        height: 2.75rem;
        overflow-y: hidden;
      }

      .nav-scroller .nav {
        display: flex;
        flex-wrap: nowrap;
        padding-bottom: 1rem;
        margin-top: -1px;
        overflow-x: auto;
        text-align: center;
        white-space: nowrap;
        -webkit-overflow-scrolling: touch;
      }

      .btn-bd-primary {
        --bd-violet-bg: #712cf9;
        --bd-violet-rgb: 112.520718, 44.062154, 249.437846;

        --bs-btn-font-weight: 600;
        --bs-btn-color: var(--bs-white);
        --bs-btn-bg: var(--bd-violet-bg);
        --bs-btn-border-color: var(--bd-violet-bg);
        --bs-btn-hover-color: var(--bs-white);
        --bs-btn-hover-bg: #6528e0;
        --bs-btn-hover-border-color: #6528e0;
        --bs-btn-focus-shadow-rgb: var(--bd-violet-rgb);
        --bs-btn-active-color: var(--bs-btn-hover-color);
        --bs-btn-active-bg: #5a23c8;
        --bs-btn-active-border-color: #5a23c8;
      }

      .bd-mode-toggle {
        z-index: 1500;
      }

      .bd-mode-toggle .dropdown-menu .active .bi {
        display: block !important;
      }

      

    </style>
</head>
<body>
  <!-- Loader -->
<div id="loader" class="d-none position-fixed top-0 start-0 w-100 h-100 bg-light bg-opacity-75 d-flex justify-content-center align-items-center" style="z-index: 1050;">
  <div class="spinner-border" role="status">
      <span class="visually-hidden">Loading...</span>
  </div>
</div>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Navbar</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                  <a class="nav-link active" aria-current="page" href="ordini.php">
                      Ordini
                      <span class="badge bg-danger" id="badgeOrdini">0</span> <!-- Badge per il conteggio delle notifiche -->
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

<div class="container mt-4" id="containerMain">

    <div class="card">
        <div class="card-header">
            Elenco Ordini
        </div>
        <div class="card-body">
            <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                <input type="radio" class="btn-check" name="btnradio" id="btnradio1" autocomplete="off" checked>
                <label class="btn btn-outline-dark" for="btnradio1">Non Confermati</label>

                <input type="radio" class="btn-check" name="btnradio" id="btnradio2" autocomplete="off">
                <label class="btn btn-outline-dark" for="btnradio2">Confermati</label>
            </div>
            <div id="alertContainer" class="mt-3"></div>


            <div class="table-responsive"> <!-- Aggiungi questo div -->
              <table class="table table-responsive table-striped mt-3" id="tabellaNonConfermati">
                  <thead>
                      <tr>
                          <th scope="col">#</th>
                          <th scope="col">Nome</th>
                          <th scope="col">Cognome</th>
                          <th scope="col">Email</th>
                          <th scope="col">Data</th>
                          <th scope="col">Totale</th>
                          <th scope="col">Dettaglio</th>
                      </tr>
                  </thead>
                  <tbody id="elencoOrdini">
                      <!-- I dati degli ordini non confermati verranno inseriti qui -->
                  </tbody>
              </table>
            </div>

            <table class="table table-striped mt-3 d-none" id="tabellaConfermati">
                <thead>
                    <tr>
                        <th scope="col">id_carrello</th>
                        <th scope="col">nome</th>
                        <th scope="col">cognome</th>
                        <th scope="col">email</th>
                        <th scope="col">Data</th>
                        <th scope="col">Stato</th>
                        <th scope="col">Dettaglio</th>

                    </tr>
                </thead>
                <tbody id="elencoOrdiniConfermati">
                    <!-- I dati degli ordini confermati verranno inseriti qui -->
                </tbody>
            </table>
        </div>
    </div>
</div>


<!-- Modal per i dettagli dell'ordine con Accordion -->
<div class="modal fade" id="modalDettagli" tabindex="-1" aria-labelledby="modalDettagliLabel" aria-hidden="true">
  <div class="modal-dialog">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="modalDettagliLabel">Dettagli Ordine</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="formDettagli" class="m-3">
              
                <div class="row mb-3">
                    <!-- Nome -->
                    <div class="col-md-6">
                        <label for="nome" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="nome" readonly>
                    </div>
                    
                    <!-- Cognome -->
                    <div class="col-md-6">
                        <label for="cognome" class="form-label">Cognome</label>
                        <input type="text" class="form-control" id="cognome" readonly>
                    </div>
                </div>

                <!-- Indirizzo -->
                <div class="mb-3">
                  <label for="indirizzo" class="form-label">Indirizzo</label>
                  <input type="text" class="form-control" id="indirizzo" readonly>
              </div>
                
                <!-- Accordion per numero e email -->
                <div class="accordion" id="accordionContactDetails">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                Informazioni di contatto
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionContactDetails">
                            <div class="accordion-body">
                                <!-- Email -->
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" readonly>
                                </div>

                                <!-- Numero -->
                                <div class="mb-3">
                                    <label for="numero" class="form-label">Numero</label>
                                    <input type="text" class="form-control" id="numero" readonly>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabella Prodotti -->
                <div class="mb-3">
                  <div class="table-responsive">
                      <table class="table table-bordered">
                          <thead>
                              <tr>
                                  <th>Nome Prodotto</th>
                                  <th>Quantità</th>
                                  <th>Prezzo (€)</th>
                              </tr>
                          </thead>
                          <tbody id="tabellaProdotti">
                              <!-- Le righe dei prodotti verranno aggiunte qui -->
                          </tbody>
                      </table>
                  </div>
                  <div id="totale" class="text-end" style="font-weight: bold;"></div>
                </div>
                <input type="hidden" id="id_carrello" value="">
              </form>
        </div>

          <div class="modal-footer  d-flex justify-content-center">
            <button type="button" class="btn btn-warning me-2" id="eliminaOrdine" onclick="apriModalRifiutaOrdine()">Rifiuta Ordine</button>
            <button type="button" class="btn btn-danger me-2" id="eliminaOrdine" onclick="apriModalEliminaOrdine()">Elimina Ordine</button>
            <button type="button" class="btn btn-success" id="confermaOrdine" onclick="apriModalConfermaOrdine()">Conferma Ordine</button>
        </div>
      </div>
  </div>
</div>

<!-- Modal di conferma per Conferma Ordine -->
<div class="modal fade" id="modalConfermaOrdine" tabindex="-1" aria-labelledby="modalConfermaOrdineLabel" aria-hidden="true">
  <div class="modal-dialog  modal-lg"> <!-- Aggiungi "custom-modal" -->
    <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="modalConfermaOrdineLabel">Conferma Ordine</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
              Sei sicuro di voler confermare l'ordine?
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
              <button type="button" class="btn btn-success" id="confermaOrdine" onclick="confermaOrdine()">Sì, conferma</button>
          </div>
      </div>
  </div>
</div>


<!-- Modal di conferma per Elimina Ordine -->
<div class="modal fade" id="modalEliminaOrdine" tabindex="-1" aria-labelledby="modalEliminaOrdineLabel" aria-hidden="true">
  <div class="modal-dialog  modal-lg"> <!-- Aggiungi "custom-modal" -->
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="modalEliminaOrdineLabel">Elimina Ordine</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
              Sei sicuro di voler eliminare l'ordine?
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
              <button type="button" class="btn btn-danger" onclick="eliminaOrdine()">Sì, elimina</button>
          </div>
      </div>
  </div>
</div>

<div class="modal fade" id="modalRifiutaOrdine" tabindex="-1" aria-labelledby="modalRifiutaOrdineLabel" aria-hidden="true">
  <div class="modal-dialog  modal-lg"> <!-- Aggiungi "custom-modal" -->
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="modalRifiutaOrdineLabel">Rifiuta Ordine</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
              Sei sicuro di voler rifiutare l'ordine?
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
              <button type="button" class="btn btn-warning" onclick="rifiutaOrdine()">Sì, rifiuta</button>
          </div>
      </div>
  </div>
</div>



<!-- Modal per i dettagli dell'ordine -->
<div class="modal fade" id="modalDettagliConfermato" tabindex="-1" aria-labelledby="modalDettagliConfermatoLabel" aria-hidden="true">
  <div class="modal-dialog">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="modalDettagliConfermatoLabel">Dettagli Ordine</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="formDettagli" class="m-3">
              
                <div class="row mb-3">
                    <!-- Nome -->
                    <div class="col-md-6">
                        <label for="nomeConfe" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="nomeConfe" readonly>
                    </div>
                    
                    <!-- Cognome -->
                    <div class="col-md-6">
                        <label for="cognomeConfe" class="form-label">Cognome</label>
                        <input type="text" class="form-control" id="cognomeConfe" readonly>
                    </div>
                </div>

                <!-- Indirizzo -->
                <div class="mb-3">
                  <label for="indirizzoConfe" class="form-label">Indirizzo</label>
                  <input type="text" class="form-control" id="indirizzoConfe" readonly>
              </div>
                
                <!-- Accordion per numero e email -->
                <div class="accordion" id="accordionContactDetails">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                Informazioni di contatto
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionContactDetails">
                            <div class="accordion-body">
                                <!-- Email -->
                                <div class="mb-3">
                                    <label for="emailConfe" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="emailConfe" readonly>
                                </div>

                                <!-- Numero -->
                                <div class="mb-3">
                                    <label for="numeroConfe" class="form-label">Numero</label>
                                    <input type="text" class="form-control" id="numeroConfe" readonly>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabella Prodotti -->
                <div class="mb-3">
                  <div class="table-responsive">
                      <table class="table table-bordered">
                          <thead>
                              <tr>
                                  <th>Nome Prodotto</th>
                                  <th>Quantità</th>
                                  <th>Prezzo (€)</th>
                              </tr>
                          </thead>
                          <tbody id="tabellaProdottiConfe">
                              <!-- Le righe dei prodotti verranno aggiunte qui -->
                          </tbody>
                      </table>
                  </div>
                  <div id="totale" class="text-end" style="font-weight: bold;"></div>
                </div>
                <input type="hidden" id="id_carrello" value="">
              </form>
        </div>

      </div>
  </div>
</div>



<div class="container"></div>
  <footer class="py-3 my-4">
    <ul class="nav justify-content-center border-bottom pb-3 mb-3">
      <li class="nav-item"><a href="#" class="nav-link px-2 text-body-secondary">Home</a></li>
      <li class="nav-item"><a href="#" class="nav-link px-2 text-body-secondary">Features</a></li>
      <li class="nav-item"><a href="#" class="nav-link px-2 text-body-secondary">Pricing</a></li>
      <li class="nav-item"><a href="#" class="nav-link px-2 text-body-secondary">FAQs</a></li>
      <li class="nav-item"><a href="#" class="nav-link px-2 text-body-secondary">About</a></li>
    </ul>
    <p class="text-center text-body-secondary">&copy; 2024 Company, Inc</p>
  </footer>
</div>

<script src="bootstrap-5.3.3/js/bootstrap.bundle.min.js"></script>
   <!-- jQuery -->
   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   
<script>
  $(document).ready(function() {
        numeroConfermare();
        elencoOrdini(); // Richiama la funzione per riempire gli ordini
        elencoOrdiniConfermati();
        $("#btnradio1").change(function() {
            $("#tabellaNonConfermati").removeClass("d-none");
            $("#tabellaConfermati").addClass("d-none");
        });

        $("#btnradio2").change(function() {
            $("#tabellaNonConfermati").addClass("d-none");
            $("#tabellaConfermati").removeClass("d-none");
        });
    });


    function getTokenFromLocalStorage() {
        return localStorage.getItem('userToken');
    }

    function verificaToken(token) {
    console.log("Token inviato:", token);  // Log del token per verificare cosa stai inviando

    $.ajax({
        type: "POST",
        url: 'action.php?_action=verifica',
        data: { token: token },
        dataType: 'json',
        success: function (result) {
            console.log("Risultato ricevuto:", result);  // Log del risultato per debug
            if (result.status === 1) {
               alert(result.message);  // Se il JSON ha un campo 'message'
            } else {
                alert("Errore durante il caricamento degli ordini.");
            }
        },
        error: function (xhr, status, error) {
            console.error("Errore nella richiesta AJAX:", status, error);
            console.log("Response Text:", xhr.responseText);  // Aggiungi questo per ulteriori dettagli
            alert("Si è verificato un errore. Riprova più tardi.");
        }
    });
}


function elencoOrdini() {
    $.ajax({
        type: "POST",
        url: 'action.php?_action=elencoOrdini',
        dataType: 'json',
        success: function (result) {
            if (result.status === 1) {
                const elencoOrdini = $("#elencoOrdini");
                elencoOrdini.empty();
                counter=0;
                result.data.elencoOrdini.forEach(ordine => {
                    counter++
                    const ordineRow = `
                        <tr>
                            <td>${counter}</td>
                            <td>${ordine.nome}</td>
                            <td>${ordine.cognome}</td>
                            <td>${ordine.email}</td>
                            <td>${ordine.data_ordinazione}</td>
                            <td>${ordine.prezzo}€</td>
                            <td><button class="btn btn-success btn-sm" onclick="visualizzaDettagli(${ordine.id_carrello})">Apri ordine</button></td>
                        </tr>
                    `;
                    elencoOrdini.append(ordineRow);
                });
            } else {
                alert("Errore durante il caricamento degli ordini.");
            }
        },
        error: function (xhr, status, error) {
            console.error("Errore nella richiesta AJAX:", status, error);
            alert("Si è verificato un errore. Riprova più tardi.");
        }
    });
}

function elencoOrdiniConfermati() {
    $.ajax({
        type: "POST",
        url: 'action.php?_action=elencoOrdiniConfermati',
        dataType: 'json',
        success: function (result) {
            if (result.status === 1) {
                const elencoOrdiniConfermati = $("#elencoOrdiniConfermati");
                elencoOrdiniConfermati.empty();
                
                result.data.elencoOrdiniConfermati.forEach(ordine => {
                    const ordineRow = `
                        <tr>
                            <td>${ordine.id_carrello}</td>
                            <td>${ordine.nome}</td>
                            <td>${ordine.cognome}</td>
                            <td>${ordine.email}</td>
                            <td>${ordine.data_ordinazione}</td>
                            <td>${ordine.flag_confermato}</td>
                            <td><button class="btn btn-success btn-sm" onclick="visualizzaDettagliConfermato(${ordine.id_carrello})">Controlla ordine</button></td>
                        </tr>
                    `;
                    elencoOrdiniConfermati.append(ordineRow);
                });
            } else {
                alert("Errore durante il caricamento degli ordini.");
            }
        },
        error: function (xhr, status, error) {
            console.error("Errore nella richiesta AJAX:", status, error);
            alert("Si è verificato un errore. Riprova più tardi.");
        }
    });
}


function visualizzaDettagli(id_carrello) {
    $.ajax({
        type: "POST",
        url: 'action.php?_action=visualizzaDettagli',
        data: { id_carrello: id_carrello },
        dataType: 'json',
        success: function (result) {
            console.log(result);
            if (result.status === 1) {
                // Popola il modal con i dettagli dell'ordine
                $("#nome").val(result.data.dettaglio.nome);
                $("#cognome").val(result.data.dettaglio.cognome);
                $("#email").val(result.data.dettaglio.email);
                $("#numero").val(result.data.dettaglio.telefono);
                $("#indirizzo").val(result.data.dettaglio.indirizzo);
                orarioConsegna=result.data.dettaglio.orario_consegna
                pagamento=result.data.dettaglio.pagamento
                // Crea una variabile per accumulare il totale
                let totale = 0; // Variabile per il totale

                // Resetta la tabella dei prodotti
                $("#tabellaProdotti").empty();

                if (result.data.dettaglio.prodotti.length > 0) {
                    result.data.dettaglio.prodotti.forEach(function(prodotto) {
                        let prezzoTot = prodotto.prezzo * prodotto.quantita; // Calcola il subtotale
                        totale += prezzoTot; // Aggiungi al totale

                        // Aggiungi una riga alla tabella
                        $("#tabellaProdotti").append(`
                            <tr>
                                <td>${prodotto.nomeProdotto}</td>
                                <td>${prodotto.quantita}</td>
                                <td>${prezzoTot}€</td>
                            </tr>
                        `);
                    }); 
                }
                // Aggiungi una riga per il totale con l'orario di consegna
                $("#tabellaProdotti").append(`
                    <tr>
                        <td class="text-start"><strong>Orario di consegna: ${orarioConsegna}</strong></td>
                        <td  class="text-end"><strong>${pagamento}</strong></td>
                        <td><strong>${totale}€</strong></td>
                    </tr>
                `);
                //imposto carrello da usare su confermaOrdine
                $("#id_carrello").val(id_carrello);
                // Mostra il modal
                $("#modalDettagli").modal('show');
            } else {
                alert("Errore nel recupero dei dettagli dell'ordine: " + result.message);
            }
        },
        error: function (xhr, status, error) {
            console.error("Errore nella richiesta AJAX:", status, error);
            alert("Si è verificato un errore. Riprova più tardi.");
        }
    });
}




function visualizzaDettagliConfermato(id_carrello) {
    $.ajax({
        type: "POST",
        url: 'action.php?_action=visualizzaDettagliConfermato',
        data: { id_carrello: id_carrello },
        dataType: 'json',
        success: function (result) {
          if (result.status === 1) {
                // Popola il modal con i dettagli dell'ordine
                $("#nomeConfe").val(result.data.dettaglio.nome); // Aggiornato per accedere a 'dettaglio'
                $("#cognomeConfe").val(result.data.dettaglio.cognome);
                $("#emailConfe").val(result.data.dettaglio.email);
                $("#numeroConfe").val(result.data.dettaglio.telefono);
                $("#indirizzoConfe").val(result.data.dettaglio.indirizzo);
                orarioConsegna=result.data.dettaglio.orario_consegna

                // Crea una variabile per accumulare il totale
                let totale = 0; // Variabile per il totale

                // Resetta la tabella dei prodotti
                $("#tabellaProdottiConfe").empty();

                if (result.data.dettaglio.prodotti.length > 0) {
                    result.data.dettaglio.prodotti.forEach(function(prodotto) {
                        let prezzoTot = prodotto.prezzo * prodotto.quantita; // Calcola il subtotale
                        totale += prezzoTot; // Aggiungi al totale

                        // Aggiungi una riga alla tabella
                        $("#tabellaProdottiConfe").append(`
                            <tr>
                                <td>${prodotto.nomeProdotto}</td>
                                <td>${prodotto.quantita}</td>
                                <td>${prezzoTot}€</td>
                            </tr>
                        `);
                    });
                }

                // Aggiungi una riga per il totale
                // Aggiungi una riga per il totale con l'orario di consegna
                $("#tabellaProdottiConfe").append(`
                    <tr>
                        <td class="text-start"><strong>Orario di consegna: ${orarioConsegna}</strong></td>
                        <td  class="text-end"><strong>Totale:</strong></td>
                        <td><strong>${totale}€</strong></td>
                    </tr>
                `);


                //imposto carrello
                $("#id_carrello").val(id_carrello);

            

                // Mostra il modal
                $("#modalDettagliConfermato").modal('show');
            } else {
                alert("Errore nel recupero dei dettagli dell'ordine: " + result.message);
            }
        },
        error: function (xhr, status, error) {
            console.error("Errore nella richiesta AJAX:", status, error);
            alert("Si è verificato un errore. Riprova più tardi.");
        }
    });
}





function apriModalConfermaOrdine() {
    $("#modalConfermaOrdine").modal('show');
}

function apriModalEliminaOrdine() {
    $("#modalEliminaOrdine").modal('show');
}

function apriModalRifiutaOrdine() {
    $("#modalRifiutaOrdine").modal('show');
}

function confermaOrdine() {
    // Ottieni l'ID del carrello dall'input hidden
    let id_carrello = $("#id_carrello").val();
    $("#modalDettagli").modal('hide');
    $("#modalConfermaOrdine").modal('hide');
                
    // Mostra il loader
    $("#loader").removeClass("d-none");

    // Usa l'ID del carrello per la chiamata AJAX
    $.ajax({
        type: "POST",
        url: 'action.php?_action=confermaOrdine',
        data: { id_carrello: id_carrello }, // Usa l'ID del carrello
        dataType: 'json',
        success: function (result) {
            // Nascondi il loader
            $("#loader").addClass("d-none");
            
            if (result.status == 1) {
                // Nascondi entrambi i modali
              
                // Richiama la funzione per aggiornare l'elenco ordini
                elencoOrdini();
                elencoOrdiniConfermati();
                numeroConfermare();

                // Mostra un alert di Bootstrap per la conferma
                mostraAlertConferma("Ordine confermato con successo!", "success");
            } else {
                alert("Errore nella conferma dell'ordine: " + result.message);
            }
        },
        error: function (xhr, status, error) {
            // Nascondi il loader
            $("#loader").addClass("d-none");
            
            console.error("Errore nella richiesta AJAX:", status, error);
            alert("Si è verificato un errore. Riprova più tardi.");
        }
    });
}

function eliminaOrdine() {
    // Ottieni l'ID del carrello dall'input hidden
    let id_carrello = $("#id_carrello").val();
    $("#modalDettagli").modal('hide');
    $("#modalEliminaOrdine").modal('hide');
                
    // Mostra il loader
    $("#loader").removeClass("d-none");

    // Usa l'ID del carrello per la chiamata AJAX
    $.ajax({
        type: "POST",
        url: 'action.php?_action=eliminaOrdine',
        data: { id_carrello: id_carrello }, // Usa l'ID del carrello
        dataType: 'json',
        success: function (result) {
            // Nascondi il loader
            $("#loader").addClass("d-none");
            
            if (result.status == 1) {
                // Nascondi entrambi i modali
              
                // Richiama la funzione per aggiornare l'elenco ordini
                elencoOrdini();
                elencoOrdiniConfermati();
                numeroConfermare();

                // Mostra un alert di Bootstrap per la conferma
                mostraAlertConferma("Ordine eliminato con successo!", "success");
            } else {
                alert("Errore nella conferma dell'ordine: " + result.message);
            }
        },
        error: function (xhr, status, error) {
            // Nascondi il loader
            $("#loader").addClass("d-none");
            
            console.error("Errore nella richiesta AJAX:", status, error);
            alert("Si è verificato un errore. Riprova più tardi.");
        }
    });
}

function rifiutaOrdine() {
    // Ottieni l'ID del carrello dall'input hidden
    let id_carrello = $("#id_carrello").val();
    $("#modalDettagli").modal('hide');
    $("#modalRifiutaOrdine").modal('hide');
                
    // Mostra il loader
    $("#loader").removeClass("d-none");

    // Usa l'ID del carrello per la chiamata AJAX
    $.ajax({
        type: "POST",
        url: 'action.php?_action=rifiutaOrdine',
        data: { id_carrello: id_carrello }, // Usa l'ID del carrello
        dataType: 'json',
        success: function (result) {
            // Nascondi il loader
            $("#loader").addClass("d-none");
            
            if (result.status == 1) {
                // Nascondi entrambi i modali
              
                // Richiama la funzione per aggiornare l'elenco ordini
                elencoOrdini();
                elencoOrdiniConfermati();
                numeroConfermare();

                // Mostra un alert di Bootstrap per la conferma
                mostraAlertConferma("Ordine rifiutato con successo!", "success");
            } else {
                alert("Errore nella conferma dell'ordine: " + result.message);
            }
        },
        error: function (xhr, status, error) {
            // Nascondi il loader
            $("#loader").addClass("d-none");
            
            console.error("Errore nella richiesta AJAX:", status, error);
            alert("Si è verificato un errore. Riprova più tardi.");
        }
    });
}



// Funzione per mostrare un alert di Bootstrap
function mostraAlertConferma(message, type) {
    // Crea l'alert
    let alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    // Aggiungi l'alert al DOM
    $("#alertContainer").append(alertHtml);
    
    $('html, body').animate({
        scrollTop: $("#containerMain").offset().top
    }, 500); // Tempo di animazione in millisecondi
    
    // Rimuovi l'alert dopo 3 secondi
    setTimeout(() => {
        $(".alert").alert('close');
    }, 10000);
}




function numeroConfermare() {
    $.ajax({
        type: "POST",
        url: 'action.php?_action=numeroConfermare',
        dataType: 'json',
        success: function (result) {
            if (result.status === 1) {
              contaNuoviOrdini(result.data.numero)
            } else {
                alert("Errore durante il caricamento degli ordini.");
            }
        },
        error: function (xhr, status, error) {
            console.error("Errore nella richiesta AJAX:", status, error);
            alert("Si è verificato un errore. Riprova più tardi.");
        }
    });
}


function contaNuoviOrdini(count) {
    // Aggiorna il contenuto del badge
    $("#badgeOrdini").text(count);
}

// Esempio di chiamata alla funzione


</script>
</body>
</html>
