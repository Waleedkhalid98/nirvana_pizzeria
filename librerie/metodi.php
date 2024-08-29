<?php

function get_param($name)
{
  if (isset($_GET[$name])) {
    return $_GET[$name];
  }

  if (isset($_POST[$name])) {
    return $_POST[$name];
  }

  return null;
}


function get_db_array($name)
{
  $db = new Database();
  $array = array();
  $ordini = $db->conn->query("SELECT * FROM $name");

  if ($ordini) {
    while ($row = $ordini->fetch_assoc()) {
      $array[] = $row;
    }
  } else {
    // Gestione errore query
  }

  return $array;
}




function db_fill_array($query)
{
    $db = new Database();
    $array = []; // Assicurati che l'array sia inizializzato come array vuoto.
    $ordini = $db->conn->query($query);

    if ($ordini) {
        while ($row = $ordini->fetch_assoc()) {
            $array[] = $row; // Aggiunge ogni riga come array associativo.
        }
    } else {
        // Gestione errore query
        error_log("Errore nella query: " . $db->conn->error);
    }

    return $array;
}

function get_db_value($k)
{
  $db = new Database();
  $result = $db->conn->query($k);

  if ($result === false) {
    error_log("Errore nella query: " . $db->conn->error);
    return null;
  }

  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    return reset($row);
  } else {
    return null;
  }

}
