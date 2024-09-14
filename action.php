<?php

include 'librerie/Database.php';
include 'librerie/metodi.php';
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/error.log');  
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
        $prodotti = $db->recuperaProdottiCarrello();
        if (!empty($prodotti)) {
            echo json_encode($prodotti); // Restituisce i prodotti come JSON
        } else {
            echo json_encode([]); // Restituisce un array vuoto se non ci sono prodotti
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
        try {
            $risultato_ordine = $db->ordina();
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
        } catch (Exception $e) {
            error_log("Errore nell'ordine: " . $e->getMessage());
            sendJsonResponse([
                'status' => 0,
                'message' => 'Si è verificato un errore inaspettato'
            ]);
        }
        break;

    
}   
?>