<?php

include 'librerie/Database.php';
include 'librerie/metodi.php';
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/error.log'); 

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Restituisci uno stato 200 OK per la richiesta preflight
    http_response_code(200);
    exit;
}

$db = new Database();

function sendJsonResponse($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

$paction=get_param("_action");

switch($paction) 
{	
    
    case "login":
        $username=get_param("_username");
        $password=get_param("_password");
        if ($db->login($username,$password)) {
            echo $db->login($username,$password); 
        } else {
            echo 0; 
        }
       
    break; 

    case "aggiungiProdotto": 
        $idProdotto=get_param("_k");
        if ($db->insertProdottoCarrello($idProdotto)) {
            echo 1;
        } else {
            echo 0;
        }
      
    break;

    case "FillCarrello":
        $risultato = $db->recuperaProdottiCarrello();
        if ($risultato !== "false") {
            echo json_encode([
                'prodotti' => $risultato['prodotti'],
                'totale' => $risultato['totale']
            ]);
        } else {
            echo json_encode([
                'prodotti' => [],
                'totale' => 0
            ]);
        }
        break;
    

    case "eliminaProdotto":
        $id_prodottoCarrello=get_param("_id_prodottiCarrello");
        if ($db->eliminaProdotto($id_prodottoCarrello)) {
            echo 1; 
        } else {
            echo 0; 
        }
       
    break;  

 
    case "incrementa":
        $id_prodottoCarrello=get_param("_id_prodottiCarrello");
        if ($db->incrementa($id_prodottoCarrello)) {
            echo 1; 
        } else {
            echo 0; 
        }
       
    break;  

    case "decrementa":
        $id_prodottoCarrello=get_param("_id_prodottiCarrello");
        if ($db->decrementa($id_prodottoCarrello)) {
            echo 1; 
        } else {
            echo 0; 
        }
       
    break;  
    
    case "ordina":
            $nome = get_param("nome");
            $cognome = get_param("cognome");
            $indirizzo = get_param("indirizzo");
            $telefono = get_param("telefono");
            $email = get_param("email");
            $orarioConsegna = get_param("orarioConsegna");
            $note = get_param("note");
            $deliveryType = get_param("deliveryType");
            $paymentType = get_param("paymentType");
            $risultato_ordine = $db->ordina($nome, $cognome, $indirizzo, $telefono, $email, $orarioConsegna, $note, $deliveryType, $paymentType);
           
            if ($risultato_ordine['success']) {
                sendJsonResponse([
                    'status' => 1,
                    'message' => 'Ordine completato con successo',
                    'data' => [
                        'id_carrello' => $risultato_ordine['id_carrello'],
                        'totale' => $risultato_ordine['totale'],
                        'prodotti' => $risultato_ordine['prodotti']
                    ]
                ]);
            } else {
                sendJsonResponse([
                    'status' => 0,
                    'message' => 'Errore durante l\'elaborazione dell\'ordine'
                ]);
            }
   
    break;



    case "riepilogo":
        try {
            $risultato_ordine = $db->riepilogo();
            if ($risultato_ordine['success']) {
                sendJsonResponse([
                    'status' => 1,
                    'message' => 'Riepilogo',
                    'data' => [
                        'id_carrello' => $risultato_ordine['id_carrello'],
                        'totale' => $risultato_ordine['totale'],
                        'prodotti' => $risultato_ordine['prodotti']
                    ]
                ]);
            } else {
                sendJsonResponse([
                    'status' => 0,
                    'message' => 'Errore durante l\'elaborazione dell\'ordine'
                ]);
            }
        } catch (Exception $e) {
            error_log("Errore nell'ordine: " . $e->getMessage());
            sendJsonResponse([
                'status' => 0,
                'message' => 'Si è verificato un errore inaspettato'
            ]);
        }
    break;

    case "elencoOrdini":
        try {
            $risultato_ordine = $db->elencoOrdini();
            if ($risultato_ordine['success']) {
                sendJsonResponse([
                    'status' => 1,
                    'message' => 'elencoOrdini',
                    'data' => [
                        'elencoOrdini' => $risultato_ordine['elencoOrdini']
                    ]
                ]);
            } else {
                sendJsonResponse([
                    'status' => 0,
                    'message' => 'Errore durante l\'elaborazione dell\'ordine'
                ]);
            }
        } catch (Exception $e) {
            error_log("Errore nell'ordine: " . $e->getMessage());
            sendJsonResponse([
                'status' => 0,
                'message' => 'Si è verificato un errore inaspettato'
            ]);
        }
    break;

    case "elencoOrdiniConfermati":
        try {
            $risultato_ordine = $db->elencoOrdiniConfermati();
            if ($risultato_ordine['success']) {
                sendJsonResponse([
                    'status' => 1,
                    'message' => 'elencoOrdiniConfermati',
                    'data' => [
                        'elencoOrdiniConfermati' => $risultato_ordine['elencoOrdiniConfermati']
                    ]
                ]);
            } else {
                sendJsonResponse([
                    'status' => 0,
                    'message' => 'Errore durante l\'elaborazione dell\'ordine'
                ]);
            }
        } catch (Exception $e) {
            error_log("Errore nell'ordine: " . $e->getMessage());
            sendJsonResponse([
                'status' => 0,
                'message' => 'Si è verificato un errore inaspettato'
            ]);
        }
    break;

    

    
    case "visualizzaDettagli":
        try {
            $id_carrello = get_param("id_carrello");
            $risultato_ordine = $db->visualizzaDettagli($id_carrello);
            
            if ($risultato_ordine['success']) {
                sendJsonResponse([
                    'status' => 1,
                    'message' => 'Dettagli dell\'ordine recuperati con successo',
                    'data' => [
                        'dettaglio' => $risultato_ordine['data'] // Cambiato da 'dettaglio' a 'data'
                    ]
                ]);
            } else {
                sendJsonResponse([
                    'status' => 0,
                    'message' => $risultato_ordine['message'] // Utilizza il messaggio di errore specifico
                ]);
            }
        } catch (Exception $e) {
            error_log("Errore nell'ordine: " . $e->getMessage());
            sendJsonResponse([
                'status' => 0,
                'message' => 'Si è verificato un errore inaspettato'
            ]);
        }
    break;

    case "visualizzaDettagliProdotto":
        try {
            $id = get_param("id");
            $prodotto = $db->visualizzaDettagliProdotto($id);
            
            if ($prodotto['success']) {
                sendJsonResponse([
                    'status' => 1,
                    'message' => 'Dettagli dell\'ordine recuperati con successo',
                    'data' => [
                        'dettaglio' => $prodotto['data'] // Cambiato da 'dettaglio' a 'data'
                    ]
                ]);
            } else {
                sendJsonResponse([
                    'status' => 0,
                    'message' => $prodotto['message'] // Utilizza il messaggio di errore specifico
                ]);
            }
        } catch (Exception $e) {
            error_log("Errore nell'ordine: " . $e->getMessage());
            sendJsonResponse([
                'status' => 0,
                'message' => 'Si è verificato un errore inaspettato'
            ]);
        }
    break;

    case "visualizzaDettagliUtente":
        try {
            $id_utente = get_param("id_utente");
            $risultato_utente = $db->visualizzaDettagliUtente($id_utente);
            
            if ($risultato_utente['success']) {
                sendJsonResponse([
                    'status' => 1,
                    'message' => 'Dettagli dell\'ordine recuperati con successo',
                    'data' => [
                        'dettaglio' => $risultato_utente['data'] // Cambiato da 'dettaglio' a 'data'
                    ]
                ]);
            } else {
                sendJsonResponse([
                    'status' => 0,
                    'message' => $risultato_utente['message'] // Utilizza il messaggio di errore specifico
                ]);
            }
        } catch (Exception $e) {
            error_log("Errore nell'ordine: " . $e->getMessage());
            sendJsonResponse([
                'status' => 0,
                'message' => 'Si è verificato un errore inaspettato'
            ]);
        }
    break;

    case "visualizzaDettagliConfermato":
        try {
            $id_carrello = get_param("id_carrello");
            $risultato_ordine = $db->visualizzaDettagliConfermato($id_carrello);
            if ($risultato_ordine['success']) {
                sendJsonResponse([
                    'status' => 1,
                    'message' => 'Dettagli dell\'ordine recuperati con successo',
                    'data' => [
                        'dettaglio' => $risultato_ordine['data'] // Cambiato da 'dettaglio' a 'data'
                    ]
                ]);
            } else {
                sendJsonResponse([
                    'status' => 0,
                    'message' => $risultato_ordine['message'] // Utilizza il messaggio di errore specifico
                ]);
            }
        } catch (Exception $e) {
            error_log("Errore nell'ordine: " . $e->getMessage());
            sendJsonResponse([
                'status' => 0,
                'message' => 'Si è verificato un errore inaspettato'
            ]);
        }
    break;


    case "confermaOrdine":
        try {

            $id_carrello = get_param("id_carrello");
            $risultato_ordine = $db->confermaOrdine($id_carrello);
            
            if ($risultato_ordine['success']) {
                sendJsonResponse([
                    'status' => 1,
                    'message' => 'Mail inviata',
                  
                ]);
            } else {
                sendJsonResponse([
                    'status' => 0,
                    'message' => $risultato_ordine['message'] // Utilizza il messaggio di errore specifico
                ]);
            }
        } catch (Exception $e) {
            error_log("Errore nell'ordine: " . $e->getMessage());
            sendJsonResponse([
                'status' => 0,
                'message' => 'Si è verificato un errore inaspettato'
            ]);
        }
    break;

    case "eliminaOrdine":
        try {

            $id_carrello = get_param("id_carrello");
            $risultato_ordine = $db->eliminaOrdine($id_carrello);
            
            if ($risultato_ordine['success']) {
                sendJsonResponse([
                    'status' => 1,
                    'message' => 'ordine eliminato',
                  
                ]);
            } else {
                sendJsonResponse([
                    'status' => 0,
                    'message' => $risultato_ordine['message'] // Utilizza il messaggio di errore specifico
                ]);
            }
        } catch (Exception $e) {
            error_log("Errore nell'ordine: " . $e->getMessage());
            sendJsonResponse([
                'status' => 0,
                'message' => 'Si è verificato un errore inaspettato'
            ]);
        }
    break;


    case "rifiutaOrdine":
        try {

            $id_carrello = get_param("id_carrello");
            $risultato_ordine = $db->rifiutaOrdine($id_carrello);
            
            if ($risultato_ordine['success']) {
                sendJsonResponse([
                    'status' => 1,
                    'message' => 'ordine rifiutato',
                  
                ]);
            } else {
                sendJsonResponse([
                    'status' => 0,
                    'message' => $risultato_ordine['message'] // Utilizza il messaggio di errore specifico
                ]);
            }
        } catch (Exception $e) {
            error_log("Errore nell'ordine: " . $e->getMessage());
            sendJsonResponse([
                'status' => 0,
                'message' => 'Si è verificato un errore inaspettato'
            ]);
        }
    break;

    case "elencoUtenti":
        try {
            $risultato_utenti = $db->elencoUtenti();
            if ($risultato_utenti['success']) {
                sendJsonResponse([
                    'status' => 1,
                    'message' => 'elencoUtenti',
                    'data' => [
                        'elencoUtenti' => $risultato_utenti['elencoUtenti']
                    ]
                ]);
            } else {
                sendJsonResponse([
                    'status' => 0,
                    'message' => 'Errore durante l\'elaborazione dell\'ordine'
                ]);
            }
        } catch (Exception $e) {
            error_log("Errore nell'ordine: " . $e->getMessage());
            sendJsonResponse([
                'status' => 0,
                'message' => 'Si è verificato un errore inaspettato'
            ]);
        }
    break;

    case "numeroProdotti":
        try {
            $risultatoNumeroProdotti = $db->numeroProdotti();
            if ($risultatoNumeroProdotti['success']) {
                sendJsonResponse([
                    'status' => 1,
                    'message' => 'numeroProdotti',
                    'data' => [
                        'numeroProdotti' => $risultatoNumeroProdotti['numeroProdotti']
                    ]
                ]);
            } else {
                sendJsonResponse([
                    'status' => 0,
                    'message' => 'Errore durante l\'elaborazione dell\'ordine'
                ]);
            }
        } catch (Exception $e) {
            error_log("Errore nell'ordine: " . $e->getMessage());
            sendJsonResponse([
                'status' => 0,
                'message' => 'Si è verificato un errore inaspettato'
            ]);
        }
    break;

    case "numeroConfermare":
        try {
            $numeroConfermare = $db->numeroConfermare();
            $numeroCarrelli = $numeroConfermare['numero'][0]['COUNT(c.id_carrello)'];
            if ($numeroConfermare['success']) {
                sendJsonResponse([
                    'status' => 1,
                    'message' => 'elencoUtenti',
                    'data' => [
                        'numero' => $numeroCarrelli
                    ]
                ]);
            } else {
                sendJsonResponse([
                    'status' => 0,
                    'message' => 'Errore durante l\'elaborazione dell\'ordine'
                ]);
            }
        } catch (Exception $e) {
            error_log("Errore nell'ordine: " . $e->getMessage());
            sendJsonResponse([
                'status' => 0,
                'message' => 'Si è verificato un errore inaspettato'
            ]);
        }
    break;

    case "verifica":
        try {
            // Recupera il token dai parametri
            $token = get_param("token");
    
            // Stampa il token per il debug (puoi rimuovere questa parte)
    
            // Verifica il token utilizzando la funzione verifyToken
            $id_utente = $db->verifyToken($token);
    
            // Controlla se la verifica è andata a buon fine
            if ($id_utente['success']) {
                // Risposta JSON con successo
                sendJsonResponse([
                    'status' => 1,
                    'message' => 'elencoUtenti',
                    'data' => [
                        'id_utente' => $id_utente['id_utente']
                    ]
                ]);
            } else {
                // Risposta JSON in caso di errore di verifica
                sendJsonResponse([
                    'status' => 0,
                    'message' => 'Errore durante l\'elaborazione dell\'ordine: Token non valido o scaduto'
                ]);
            }
        } catch (Exception $e) {
            // Logga l'errore nel file di log e restituisci un messaggio di errore
            error_log("Errore nell'ordine: " . $e->getMessage());
    
            sendJsonResponse([
                'status' => 0,
                'message' => 'Si è verificato un errore inaspettato'
            ]);
        }
        break;

        case "caricaImmagine":
            if (isset($_POST['immagine']) && isset($_POST['nomeFile'])) {
                // Decodifica la stringa base64
                $data = $_POST['immagine'];
        
                // Trova la parte base64 dell'immagine
                list($type, $data) = explode(';', $data);
                list(, $data)      = explode(',', $data);
                $data = base64_decode($data);
                
                // Verifica se la decodifica ha avuto successo
                if ($data === false) {
                    echo json_encode(['status' => 0, 'message' => 'Immagine non valida.']);
                    exit;
                }
        
                // Definisci il percorso dove salvare l'immagine
                $uploadDir = 'images/'; // Sostituisci con il tuo percorso
                $fileName = $_POST['nomeFile'];
                $filePath = $uploadDir . basename($fileName);
        
                // Salva il file
                if (file_put_contents($filePath, $data)) {
                    echo json_encode(['status' => 1, 'nomeImmagine' => $fileName]);
                } else {
                    echo json_encode(['status' => 0, 'message' => 'Errore nel salvataggio dell\'immagine.']);
                }
            } else {
                echo json_encode(['status' => 0, 'message' => 'Dati mancanti.']);
            }
            break;

            case "salvaDatiProdotto":
                $idprodotto = get_param("id");
                $titolo = get_param("titolo");
                $descrizione = get_param("descrizione");
                $prezzo = get_param("prezzo");
                $categoria = get_param("categoria");
                $nomeImmagine = get_param("immagine"); // Aggiunto per l'immagine
                // Presumendo che la tua funzione di salvataggio richieda questi parametri
                $risultato_salvataggio = $db->salvaProdotto( $idprodotto,$titolo, $descrizione, $prezzo, $nomeImmagine, $categoria);
                if ($risultato_salvataggio['success']) {
                    sendJsonResponse([
                        'status' => 1,
                        'message' => 'Prodotto salvato con successo',
                        'data' => [
                            'id_prodotto' => $risultato_salvataggio['id_prodotto'],
                            // Aggiungi ulteriori dati se necessario
                        ]
                    ]);
                } else {
                    sendJsonResponse([
                        'status' => 0,
                        'message' => 'Errore durante il salvataggio del prodotto'
                    ]);
                }
            
       
        break;
    
    
    
}   
?>