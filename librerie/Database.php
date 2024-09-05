<?php

class Database {
    private $servername = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "nirvana";
    public $conn;

    public function __construct() {
        $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);

        if ($this->conn->connect_error) {
            die("Connessione fallita: " . $this->conn->connect_error);
        }
    }

    

    public function closeConnection() {
        $this->conn->close();
    }







    public function insertProdottoCarrello($id) {

       $id_carrello = $this->controlloCarrello();
        $id = $this->conn->real_escape_string($id);
        // $prezzo=get_db_value("SELECT prezzo FROM prodotto WHERE id=$id");
       
        $sql = "INSERT INTO prodotticarrello (id_carrello, id_prodotto) VALUES ($id_carrello, '$id')";

        if ($this->conn->query($sql) === TRUE) {
            return true;
        } else {
            return false;
        }
    }

    public function recuperaProdottiCarrello() {
        $id_carrello = $this->controlloCarrello(); 

        $sql = "SELECT * from prodotticarrello
                        INNER join carrello on prodotticarrello.id_carrello = carrello.id_carrello
                        INNER JOIN prodotto ON prodotticarrello.id_prodotto=prodotto.id 
                        WHERE carrello.flag_ordinato IS NULL and prodotticarrello.id_carrello= ".$id_carrello."";

        $result = $this->conn->query($sql);

        if ($result->num_rows > 0) {
            $prodotti = [];
            while($row = $result->fetch_assoc()) {
                $prodotti[] = $row;
            }
            return $prodotti;
        } else {
            return "false";
        }
    }


    public function eliminaProdotto($id) {
        $id_carrello = $this->controlloCarrello(); 

        $sql = "DELETE FROM prodotticarrello WHERE id_carrello=$id_carrello AND id_prodottiCarrello=$id";

        if ($this->conn->query($sql) === TRUE) {
            return true;
        } else {
            return false;
        }
    }

    public function incrementa($id) {
        $id_carrello = $this->controlloCarrello(); 
       $numero_prodotto = get_db_value("SELECT numero_prodotti FROM prodotticarrello WHERE id_carrello = '$id_carrello' AND id_prodottiCarrello = '$id'");
        $numero_prodotto++;
        $sql = "UPDATE prodotticarrello 
                SET numero_prodotti = $numero_prodotto
                WHERE id_carrello = '$id_carrello' AND id_prodottiCarrello = '$id'";
        
        if ($this->conn->query($sql) === TRUE) {
            return $numero_prodotto;
        } else {
            return false;
        }
    }

    public function decrementa($id) {
        $id_carrello = $this->controlloCarrello();
        $numero_prodotto = get_db_value("SELECT numero_prodotti FROM prodotticarrello WHERE id_carrello = '$id_carrello' AND id_prodottiCarrello = $id");

        $numero_prodotto--;

        if ($numero_prodotto < 0) {
            $numero_prodotto = 0;
        }

        $sql = "UPDATE prodotticarrello 
                SET numero_prodotti = $numero_prodotto
                WHERE id_carrello = '$id_carrello' AND id_prodottiCarrello = $id";
        
        if ($this->conn->query($sql) === TRUE) {
            return true;
        } else {
           error_log("Errore nella query di aggiornamento: " . $this->conn->error);
        return false;
        }
    }


    private function controlloCarrello() {
        session_start();    

        $id_utente = $_SESSION['id_utente'];
        $id_carrello=get_db_value("SELECT id_carrello FROM carrello WHERE id_utente= '$id_utente' AND flag_ordinato IS NULL");

        if(empty($id_carrello))
        {
            $sql = "INSERT INTO carrello ( id_utente) VALUES ('$id_utente')";

            if ($this->conn->query($sql) === TRUE) {
                return $id_carrello=get_db_value("SELECT id_carrello FROM carrello WHERE id_utente= '$id_utente' AND flag_ordinato IS NULL");
            } else {
                return false;
            }
        }
        return $id_carrello;
    }


    public function ordina() {
        $id_carrello = $this->controlloCarrello();
        
        $ordine = get_data("SELECT id_prodottiCarrello, numero_prodotti, prezzo  FROM prodotticarrello WHERE id_carrello='$id_carrello'");
        
        $totale = 0;
        $prodotti_ordinati = [];
        
        foreach ($ordine as $item) {
            if (is_array($item)) {
                $subtotale = $item['numero_prodotti'] * $item['prezzo'];
                $totale += $subtotale;
                
                $prodotti_ordinati[] = [
                    'id_prodotto_carrello' => $item['id_prodottiCarrello'],
                    'nome_prodotto' => $item['nome'],
                    'quantita' => $item['numero_prodotti'],
                    'prezzo_unitario' => $item['prezzo'],
                    'subtotale' => $subtotale
                ];
            } else {
                error_log("Elemento non valido nell'ordine: " . print_r($item, true));
            }
        }
        
        $sql = "UPDATE carrello 
                SET flag_ordinato = 1
                WHERE id_carrello = '$id_carrello'";
        
        $update_success = $this->conn->query($sql) === TRUE;
        
        return [
            'success' => $update_success,
            'id_carrello' => $id_carrello,
            'prodotti' => $prodotti_ordinati,
            'totale' => $totale
        ];
    }

}
