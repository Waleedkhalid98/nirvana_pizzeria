<?php 
include 'librerie/Database.php';
include 'librerie/metodi.php';
$prodotti=get_data("SELECT * FROM prodotto") ;
// print_r($prodotti);


?>


<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionale Pizzeria</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="bootstrap-5.3.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                    <a class="nav-link active" aria-current="page" href="ordini.php">
                        Ordini
                        <span class="badge bg-danger" id="badgeOrdini">0</span> <!-- Badge per il conteggio delle notifiche -->
                    </a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link active " aria-current="page" href="utenti.html">Utenti</a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link active " href="Comunicazioni.html">Comunicazioni</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active " href="configurazioni.php">Configurazioni</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            Elenco prodotti
        </div>
        <div class="card-body">

            <table class="table table-striped mt-3" id="tabellaUtenti">
                <thead>
                    <tr>
                        <th scope="col">ID prodotto</th>
                        <th scope="col">Titolo</th>
                        <th scope="col">Descrizione</th>
                        <th scope="col">Prezzo</th>
                        <th scope="col">Categoria</th>
                    </tr>
                </thead>
                <tbody id="elencoProdotti">
                    <?php
                    // Ciclo per iterare attraverso i prodotti e popolare la tabella
                    foreach ($prodotti as $item) {
                        echo "<tr>";
                        echo "<td>" . $item['id'] . "</td>";
                        echo "<td>" . $item['titolo'] . "</td>";
                        echo "<td>" . $item['descrizione'] . "</td>";
                        echo "<td>€" . $item['prezzo'] . "</td>";
                        echo "<td>" . $item['categoria'] . "</td>";
                        echo "<td><button class='btn btn-primary' onclick='visualizzaProdotto(" . $item['id'] . ")'>Visualizza</button></td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody> 
            </table>

        </div>
    </div>
</div>

<!-- Modal per Visualizzare Utente -->
<div class="modal fade" id="modalVisualizzaUtente" tabindex="-1" aria-labelledby="modalVisualizzaUtenteLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalVisualizzaUtenteLabel">Dettagli prodotto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formProdotto">
                    <div class="row mb-3">
                        <div class="col">
                            <label for="titolo" class="form-label">Titolo</label>
                            <input type="text" class="form-control" id="titolo" placeholder="Inserisci il titolo">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="descrizione" class="form-label">Descrizione</label>
                        <input type="text" class="form-control" id="descrizione" placeholder="Inserisci descrizione">
                    </div>
                    <div class="mb-3">
                        <label for="prezzo" class="form-label">Prezzo</label>
                        <input type="text" class="form-control" id="prezzo" placeholder="Inserisci l'indirizzo">
                    </div>
                    <div class="mb-3">
                        <label for="categoria" class="form-label">Categoria</label>
                        <select class="form-control" id="categoria">
                            <option value="" disabled selected>Seleziona categoria</option>
                            <option value="Fritti">Fritti</option>
                            <option value="Pizze Rosse">Pizze Rosse</option>
                            <option value="Pizze Bianche">Pizze Bianche</option>
                            <option value="pizze speciali">pizze speciali</option>
                            <option value="Hamburger">Hamburger</option>
                            <option value="panini">panini</option>
                            <option value="piatti unici">piatti unici</option>
                            <option value="Dolci">dolci</option>
                            <!-- Aggiungi altre opzioni se necessario -->
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="immagine" class="form-label">Carica Immagine</label>
                        <input type="file" class="form-control" id="immagine" accept="image/*">
                    </div>

                    <!-- Div per l'anteprima dell'immagine -->
                    <div class="mb-3">
                        <label class="form-label">Anteprima Immagine</label>
                        <div id="anteprimaImmagine" style="border: 1px solid #ddd; padding: 10px; max-width: 300px;">
                            <img id="preview" src="" alt="Anteprima" style="max-width: 100%; display: none;">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-dark"  onclick="salvaProdotto()">Salva</button>
                <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Chiudi</button>
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="idProdotto" name="idProdotto">
<script src="bootstrap-5.3.3/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    elencoUtenti();
    numeroConfermare();
});

function elencoUtenti() {
    $.ajax({
        type: "POST",
        url: 'action.php?_action=elencoUtenti',
        dataType: 'json',
        success: function (result) {
            console.log(result);
            if (result.status === 1) {
                const elencoOrdini = $("#elencoUtenti");
                elencoOrdini.empty();
                
                const utentiUnici = [];

                result.data.elencoUtenti.forEach(utente => {
                    const nomePulito = utente.nome.trim().toLowerCase();
                    const cognomePulito = utente.cognome.trim().toLowerCase();
                    const emailPulita = utente.email.trim().toLowerCase();

                    const chiaveUnica = `${nomePulito}-${cognomePulito}-${emailPulita}`;

                    if (!utentiUnici.includes(chiaveUnica)) {
                        utentiUnici.push(chiaveUnica); // la chiave unica nell'array per evitare duplicati

                        const ordineRow = `
                            <tr>
                                <td>${utente.id_utente}</td>
                                <td>${nomePulito}</td>
                                <td>${cognomePulito}</td>
                                <td>${emailPulita}</td>
                                <td>
                                    <button class="btn btn-secondary" onclick="visualizzaUtente(${utente.id_utente})">
                                        <i class="bi bi-person-fill"></i> <!-- Icona della scheda utente -->
                                    </button>
                                </td>                         
                            </tr>
                        `;

                        elencoOrdini.append(ordineRow); 
                    }
                });
            } else {
                alert("Errore durante il caricamento degli utenti.");
            }
        },
        error: function (xhr, status, error) {
            console.error("Errore nella richiesta AJAX:", status, error);
            alert("Si è verificato un errore. Riprova più tardi.");
        }
    });
}


function visualizzaProdotto(id) {
    // Mostra il modal quando si clicca sul bottone
    $("#modalVisualizzaUtente").modal('show');
    $("#idProdotto").val(id);

    $.ajax({
        type: "POST",
        url: 'action.php?_action=visualizzaDettagliProdotto',
        data: { id: id },
        dataType: 'json',
        success: function (result) {
            if (result.status === 1) {
                // Popola il modal con i dettagli dell'ordine
                $("#id").val(result.data.dettaglio.id);
                $("#titolo").val(result.data.dettaglio.titolo);
                $("#descrizione").val(result.data.dettaglio.descrizione);
                $("#prezzo").val(result.data.dettaglio.prezzo);
                  // Visualizza l'immagine nel div con anteprima
                var immaginePercorso = 'images/' + result.data.dettaglio.immagine; // Cartella in cui sono salvate le immagini
                $('#preview').attr('src', immaginePercorso);  // Setta l'URL dell'immagine nel tag <img>
                $('#preview').show();  // Mostra l'anteprima dell'immagine se nascosta

                // Imposta la categoria come selezionata
                $("#categoria").val(result.data.dettaglio.categoria);
            }else {
                alert("error")
            }
        }
    });
    // Qui puoi aggiungere logica per popolare il modal con i dati dell'utente
}


function salvaProdotto() {
    var idProdotto = $("#idProdotto").val();
    var titolo = $("#titolo").val();
    var descrizione = $("#descrizione").val();
    var prezzo = $("#prezzo").val();
    var categoria = $("#categoria").val(); // Ottieni il valore della select della categoria
    var immagine = $("#immagine")[0].files[0];  // Ottieni il file immagine selezionato

    // Se c'è un'immagine selezionata, la inviamo al server
    if (immagine) {
        var reader = new FileReader();

        // Leggi il file immagine selezionato
        reader.readAsDataURL(immagine);

        reader.onload = function (e) {
            var base64Image = e.target.result;

            // Invia l'immagine al server come stringa base64
            $.ajax({
                type: "POST",
                url: 'action.php?_action=caricaImmagine',  // URL per caricare l'immagine
                data: {
                    immagine: base64Image,
                    nomeFile: immagine.name
                },
                success: function (result) {
                    var jsonResponse = JSON.parse(result); // Parsing della risposta

                    if (jsonResponse.status === 1) {
                        alert("Immagine salvata con successo!");
                        salvaDatiProdotto(idProdotto, titolo, descrizione, prezzo, jsonResponse.nomeImmagine,categoria);
                    } else {
                        alert('Errore durante il caricamento dell\'immagine.');
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Errore nella richiesta AJAX:", status, error);
                    alert("Errore durante il caricamento dell'immagine.");
                }
            });
        };
    } else {
        // Se non c'è immagine, inviamo solo i dati del prodotto
        salvaDatiProdotto(idProdotto, titolo, descrizione, prezzo, null);
    }
}

function salvaDatiProdotto(idProdotto, titolo, descrizione, prezzo, nomeImmagine,categoria) {
    $.ajax({
        type: "POST",
        url: 'action.php?_action=salvaDatiProdotto',  // Inserisci l'URL corretto per l'azione di salvataggio
        data: {
            id: idProdotto,
            titolo: titolo,
            descrizione: descrizione,
            prezzo: prezzo,
            categoria: categoria,
            immagine: nomeImmagine  // Invia il nome del file immagine al server
        },
        success: function (result) {
            if (result.status === 1) {
                alert('Prodotto salvato con successo!');
                $("#modalVisualizzaUtente").modal('hide');
                elencoProdotti();  // Aggiorna l'elenco prodotti
            } else {
                alert('Errore durante il salvataggio del prodotto.');
            }
        },
        error: function (xhr, status, error) {
            console.error("Errore nella richiesta AJAX:", status, error);
            alert("Errore nel salvataggio del prodotto.");
        }
    });
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

</script>
</body>
</html>
