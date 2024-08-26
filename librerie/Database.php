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
        $id_carrello = $this->controlloCarrello(); // Recupera l'ID del carrello corrente

        // Query per recuperare i prodotti nel carrello
        $sql = "SELECT * from prodotticarrello
                        INNER join carrello on prodotticarrello.id_carrello = carrello.id_carrello
                        INNER JOIN prodotto ON prodotticarrello.id_prodotto=prodotto.id 
                        WHERE carrello.flag_ordinato IS NULL and prodotticarrello.id_carrello= ".$id_carrello."";

        $result = $this->conn->query($sql);

        if ($result->num_rows > 0) {
            // Converte i risultati in un array associativo
            $prodotti = [];
            while($row = $result->fetch_assoc()) {
                $prodotti[] = $row;
            }
            return $prodotti;
        } else {
            return "false";
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
}
?>