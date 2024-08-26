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
    
    

    case "RecuperoProdotti":

       
       
    break;  

 
    
}   
?>