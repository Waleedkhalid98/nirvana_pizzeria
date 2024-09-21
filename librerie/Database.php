<?php

class Database {
    private $servername = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "nirvana";
    public $conn;
    private $secretKey;
    
    public function __construct() {
        $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
        
        if ($this->conn->connect_error) {
            die("Connessione fallita: " . $this->conn->connect_error);
        }
        
    }
    
    private function getSecretKey() {
        $secret_key=("SELECT secret_key FROM secret_keys WHERE is_active=true");
        if ($secret_key) {
            return $secret_key;
        }
        throw new Exception("No active secret key found");
    }
    
    

    public function closeConnection() {
        $this->conn->close();
    }


    public function registrati($userId, $password) {
        // Esegui l'hashing della password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    
        // Prepara la query SQL utilizzando i prepared statements
        $sql = "INSERT INTO utenti (nome, password) VALUES (?, ?)";
    
        // Prepara la query
        $stmt = $this->conn->prepare($sql);
    
        if ($stmt === false) {
            throw new Exception("Errore nella preparazione della query: " . $this->conn->error);
        }
    
        // Lega i parametri alla query (user_id e hashedPassword)
        $stmt->bind_param("ss", $userId, $hashedPassword);
    
        // Esegue la query
        $stmt->execute();
    
        if ($stmt->affected_rows === 0) {
            throw new Exception("Inserimento fallito");
        }
    
        // Chiudi lo statement
        $stmt->close();
    }
    


    public function createSecretKey($keyName) {
        // Genera una chiave segreta casuale
        $secretKey = bin2hex(random_bytes(32)); // Genera una stringa esadecimale di 64 caratteri (256 bit)
    
        // Prepara la query SQL
        $sql = "INSERT INTO secret_keys (key_name, secret_key) VALUES (?, ?)";
        
        // Prepara lo statement
        $stmt = $this->conn->prepare($sql);
        
        if ($stmt === false) {
            throw new Exception("Errore nella preparazione della query: " . $this->conn->error);
        }
        
        // Collega i parametri
        $stmt->bind_param("ss", $keyName, $secretKey);
        
        // Esegue lo statement
        if (!$stmt->execute()) {
            throw new Exception("Errore nell'inserimento della chiave: " . $stmt->error);
        }
        
        // Chiude lo statement
        $stmt->close();
        
        return $secretKey;
    }

    public function login($username, $password) {
        $username = $this->conn->real_escape_string($username);
        $password = $this->conn->real_escape_string($password);

        $sql = "SELECT id_utente, password FROM utenti WHERE nome = '$username'";
        $result = $this->conn->query($sql);

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            if ($password==$user['password']) {
                $token = $this->generateToken($user['id_utente']);
                $this->saveToken($user['id_utente'], $token);
                return $token;
            }else{
                echo "la pass non è uguale";
            }
        }
        else{
            echo "NON TROVO";
        }
        return false;
    }


    private function generateToken($user_id) {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload = json_encode([
            'user_id' => $user_id,
            'exp' => time() + 3600 
        ]);
        $this->secretKey=$this->getSecretKey(); 
        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $this->secretKey, true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }



    private function saveToken($user_id, $token) {
        $user_id = $this->conn->real_escape_string($user_id);
        $token = $this->conn->real_escape_string($token);
        $expires = date('Y-m-d H:i:s', time() + 3600);

        $sql = "INSERT INTO utenti_tokens (id_utente, token, scadenza) VALUES ('$user_id', '$token', '$expires')";
        $this->conn->query($sql);
    }


    public function verifyToken($token) {
        $token = $this->conn->real_escape_string($token);

        $sql = "SELECT id_utente FROM utenti_tokens WHERE token = '$token' AND scadenza > NOW()";
        $result = $this->conn->query($sql);

        if ($result->num_rows == 1) {
            $tokenParts = explode('.', $token);
            $payload = json_decode(base64_decode($tokenParts[1]), true);
            return $payload['user_id'];
        }
        return false;
    }

    public function logout($token) {
        $token = $this->conn->real_escape_string($token);
        $sql = "DELETE FROM utenti_tokens WHERE token = '$token'";
        $this->conn->query($sql);
    }


    public function insertProdottoCarrello($id) {

       $id_carrello = $this->controlloCarrello();
        $id = $this->conn->real_escape_string($id);
        $prezzo=get_db_value("SELECT prezzo FROM prodotto WHERE id=$id");
       
        $sql = "INSERT INTO prodotticarrello (id_carrello, id_prodotto, prezzo,numero_prodotti) VALUES ($id_carrello, '$id','$prezzo',1)";

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

    
    private function esisteCarrello() {
        session_start();    

        $id_utente = $_SESSION['id_utente'];
        $id_carrello=get_db_value("SELECT id_carrello FROM carrello WHERE id_utente= '$id_utente' AND flag_ordinato IS NULL");
        if(empty($id_carrello)){
            return false;
        }
    
        return $id_carrello;
    }


    public function ordina() {
        $id_carrello = $this->esisteCarrello();
        if($id_carrello){
            //aggiungere un controllo per vedere se è pieno
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

        }else{
            return [
                'success' => 0,
            ];
        }
       
        
        return [
            'success' => $update_success,
            'id_carrello' => $id_carrello,
            'prodotti' => $prodotti_ordinati,
            'totale' => $totale
        ];
    }


    public function riepilogo() {
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
        
    
        return [
            'success' => TRUE,
            'id_carrello' => $id_carrello,
            'prodotti' => $prodotti_ordinati,
            'totale' => $totale
        ];
    }

}
