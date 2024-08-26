<?php

include 'librerie/Database.php';
include 'librerie/metodi.php';

$db = new Database();



$paction=get_param("_action");

switch($paction) 
{	
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


    
}   
?>