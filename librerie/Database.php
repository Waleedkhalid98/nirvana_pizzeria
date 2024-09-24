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

    public function ordina($nome, $cognome, $indirizzo, $telefono, $email, $orarioConsegna, $note, $deliveryType) {
        $id_carrello = $this->esisteCarrello();
        if ($id_carrello) {
            // Recupera i prodotti nel carrello
            $ordine = get_data("SELECT id_prodottiCarrello, numero_prodotti, prezzo, id_prodotto FROM prodotticarrello WHERE id_carrello='$id_carrello'");
            
            $totale = 0;
            $prodotti_ordinati = [];
            
            foreach ($ordine as $item) {
                if (is_array($item)) {
                    $subtotale = $item['numero_prodotti'] * $item['prezzo'];
                    $totale += $subtotale;
                    $nomeProdotto = get_db_value("SELECT descrizione FROM prodotto WHERE id='" . $item['id_prodotto'] . "'");
                    $prodotti_ordinati[] = [
                        'id_prodotto_carrello' => $item['id_prodottiCarrello'],
                        'nomeProdotto' => $nomeProdotto,
                        'quantita' => $item['numero_prodotti'],
                        'prezzo_unitario' => $item['prezzo'],
                        'subtotale' => $subtotale
                    ];
                } else {
                    error_log("Elemento non valido nell'ordine: " . print_r($item, true));
                }
            }
            
            // Converti il tipo di consegna in un valore numerico per il database (1 = Delivery, 2 = Asporto)
            $tipologia = ($deliveryType === 'Delivery') ? 1 : 2;
    
            // Inserisci i dettagli dell'ordine nella tabella `carrello_dettaglio`
            if ($this->inserisciDettagliOrdine($id_carrello, $tipologia, $orarioConsegna, $note)) {
                // Salva le informazioni dell'utente
                if ($this->salvaInformazioniUtente($nome, $cognome, $email, $indirizzo, $telefono, $id_carrello)) {
                    // Aggiorna il flag "ordinato" per il carrello
                    $sql = "UPDATE carrello 
                            SET flag_ordinato = 1
                            WHERE id_carrello = '$id_carrello'";
                    
                    $update_success = $this->conn->query($sql) === TRUE;
    
                    // Invia email con i dettagli dell'ordine
                    $invioMail = $this->inviaEmailOrdine($nome, $cognome, $indirizzo, $telefono, $email, $orarioConsegna, $note, $deliveryType, $prodotti_ordinati, $totale);
                    
                    if ($invioMail) {
                        return [
                            'success' => $update_success,
                            'id_carrello' => $id_carrello,
                            'prodotti' => $prodotti_ordinati,
                            'totale' => $totale
                        ];
                    } else {
                        return [
                            'success' => 0,
                        ];
                    }
                } else {
                    return [
                        'success' => 0,
                        'message' => 'Errore durante il salvataggio delle informazioni utente.'
                    ];
                }
            } else {
                return [
                    'success' => 0,
                    'message' => 'Errore durante l\'inserimento dei dettagli dell\'ordine.'
                ];
            }
        } else {
            return [
                'success' => 0,
            ];
        }
    }
    



    public function riepilogo() {
        $id_carrello = $this->controlloCarrello();
        
        $ordine = get_data("SELECT id_prodottiCarrello, numero_prodotti, prezzo, id_prodotto  FROM prodotticarrello WHERE id_carrello='$id_carrello'");
        
        $totale = 0;
        $prodotti_ordinati = [];
        
        foreach ($ordine as $item) {
            if (is_array($item)) {
                $subtotale = $item['numero_prodotti'] * $item['prezzo'];
                $totale += $subtotale;
                $nomeProdotto = get_db_value("SELECT descrizione FROM prodotto WHERE id='" . $item['id_prodotto'] . "'");
                $prodotti_ordinati[] = [
                    'id_prodotto_carrello' => $item['id_prodottiCarrello'],
                    'nomeProdotto' => $nomeProdotto,
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

    public function elencoOrdini() {
        
        $ordini = get_data("SELECT  * 
        FROM carrello
        INNER JOIN utente_carrello ON carrello.id_carrello = utente_carrello.id_carrello
        INNER JOIN carrello_dettaglio ON carrello.id_carrello = carrello_dettaglio.id_carrello
        WHERE carrello.flag_conferma IS NULL 
          AND carrello.flag_ordinato IS NOT NULL;");

        $elencoOrdini = [];
        
        foreach ($ordini as $item) {
            if (is_array($item)) {
                $elencoOrdini[] = [
                    'id_carrello' => $item['id_carrello'],
                    'nome' => $item['nome'],
                    'cognome' => $item['cognome'],
                    'email' => $item['email'],
                    'flag_confermato' => $item['flag_confermato']
                ];
            } else {
                error_log("Elemento non valido nell'ordine: " . print_r($item, true));
            }
        }
        
    
        return [
            'success' => TRUE,
            'elencoOrdini' => $elencoOrdini
        ];
    }
    

    public function elencoOrdiniConfermati() {
        
        $ordini = get_data("SELECT  * 
        FROM carrello
        INNER JOIN utente_carrello ON carrello.id_carrello = utente_carrello.id_carrello
        INNER JOIN carrello_dettaglio ON carrello.id_carrello = carrello_dettaglio.id_carrello
        WHERE carrello.flag_conferma =1 
          AND carrello.flag_ordinato IS NOT NULL;");

        $elencoOrdini = [];
        
        foreach ($ordini as $item) {
            if (is_array($item)) {
                $elencoOrdini[] = [
                    'id_carrello' => $item['id_carrello'],
                    'nome' => $item['nome'],
                    'cognome' => $item['cognome'],
                    'email' => $item['email'],
                    'flag_confermato' => $item['flag_confermato']
                ];
            } else {
                error_log("Elemento non valido nell'ordine: " . print_r($item, true));
            }
        }
        
    
        return [
            'success' => TRUE,
            'elencoOrdiniConfermati' => $elencoOrdini
        ];
    }
    
    public function visualizzaDettagli($id_carrello) {
        $ordine = get_data("SELECT * 
        FROM carrello
        INNER JOIN utente_carrello ON carrello.id_carrello = utente_carrello.id_carrello
        INNER JOIN carrello_dettaglio ON carrello.id_carrello = carrello_dettaglio.id_carrello
        WHERE carrello.flag_conferma IS NULL 
          AND carrello.flag_ordinato IS NOT NULL
          AND carrello.id_carrello='$id_carrello';");


        $prodotti=get_data("SELECT * FROM prodotticarrello WHERE id_carrello='$id_carrello'");

        // Initialize a new array to store processed product data
        $prodotti_list = [];

        // Process the products and store them in $prodotti_list
        if (is_array($prodotti)) {
            foreach ($prodotti as $item) {
                if (is_array($item)) {
                    $nomeProdotto = get_db_value("SELECT descrizione FROM prodotto WHERE id='" . $item['id_prodotto'] . "'");
                    $prodotti_list[] = [
                        'prezzo' => $item['prezzo'],
                        'quantita' => $item['numero_prodotti'],
                        'nomeProdotto' => $nomeProdotto,
                        // Add other fields if necessary
                    ];
                }
            }
        }




        // Controlla se l'ordine esiste
        if (!empty($ordine) && is_array($ordine[0])) {
            $item = $ordine[0]; // Prendi il primo (e unico) elemento
    
            return [
                'success' => true,
                'data' => [
                    'id_carrello' => $item['id_carrello'],
                    'nome' => $item['nome'],
                    'cognome' => $item['cognome'],
                    'email' => $item['email'],
                    'flag_confermato' => $item['flag_confermato'],
                    'prodotti' => $prodotti_list
                    // Aggiungi altri campi se necessario
                ]
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Ordine non trovato.'
            ];
        }
    }

    
    public function visualizzaDettagliConfermato($id_carrello) {
        $ordine = get_data("SELECT * 
        FROM carrello
        INNER JOIN utente_carrello ON carrello.id_carrello = utente_carrello.id_carrello
        INNER JOIN carrello_dettaglio ON carrello.id_carrello = carrello_dettaglio.id_carrello
        WHERE carrello.flag_conferma =1 
          AND carrello.flag_ordinato IS NOT NULL
          AND carrello.id_carrello='$id_carrello';");
    
        // Controlla se l'ordine esiste
        if (!empty($ordine) && is_array($ordine[0])) {
            $item = $ordine[0]; // Prendi il primo (e unico) elemento
    
            return [
                'success' => true,
                'data' => [
                    'id_carrello' => $item['id_carrello'],
                    'nome' => $item['nome'],
                    'cognome' => $item['cognome'],
                    'email' => $item['email'],
                    'flag_confermato' => $item['flag_confermato']
                    // Aggiungi altri campi se necessario
                ]
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Ordine non trovato.'
            ];
        }
    }
    
    private function inviaEmailOrdine($nome, $cognome, $indirizzo, $telefono, $email, $orarioConsegna, $note, $deliveryType, $prodotti_ordinati, $totale) {
        require 'PHPMailer/src/Exception.php';
        require 'PHPMailer/src/PHPMailer.php';
        require 'PHPMailer/src/SMTP.php';
        $mail = new PHPMailer\PHPMailer\PHPMailer();
    
        try {
            $mail->IsSMTP(); 
            $mail->SMTPSecure = 'tls'; 
            $mail->Port = 587;
            $mail->Host = "smtp.gmail.com"; 
            $mail->SMTPAuth = true;
            $mail->isHTML(true);
            $mail->Username = "slvtr.lorenzo01@gmail.com";  
            $mail->Password = "sdnp lnie tvvk gsmu";  // Non lasciare mai le password nel codice finale!
            
            // Mittente e destinatario
            $mail->setFrom('mittente@email.com', 'Sistema Ordini');
            $mail->addAddress($email);  
            // Oggetto dell'email
            $mail->Subject = 'Nuovo Ordine Ricevuto da ' . $nome . ' ' . $cognome;
        
            // Corpo dell'email
            $body = "<h1>Riepilogo Ordine</h1>";
            $body .= "<p>Un nuovo ordine è stato effettuato. Di seguito i dettagli del cliente:</p>";
            
            // Elenco puntato dei dettagli dell'utente
            $body .= "<ul>";
            $body .= "<li><strong>Nome:</strong> $nome $cognome</li>";
            $body .= "<li><strong>Indirizzo:</strong> $indirizzo</li>";
            $body .= "<li><strong>Telefono:</strong> $telefono</li>";
            $body .= "<li><strong>Email:</strong> $email</li>";
            $body .= "<li><strong>Orario di Consegna:</strong> $orarioConsegna</li>";
            $body .= "<li><strong>Note:</strong> $note</li>";
            $body .= "<li><strong>Tipo di Consegna:</strong> $deliveryType</li>";
            $body .= "</ul>";
    
            // Aggiungi i prodotti ordinati in una tabella
            $body .= "<h2>Prodotti Ordinati</h2>";
            $body .= "<table border='1' cellpadding='5' cellspacing='0'>";
            $body .= "<thead><tr><th>Prodotto</th><th>Quantità</th><th>Prezzo Unitario</th><th>Subtotale</th></tr></thead>";
            $body .= "<tbody>";
        
            // Aggiungi i prodotti all'email
            foreach ($prodotti_ordinati as $prodotto) {
                $body .= "<tr>
                            <td>{$prodotto['nomeProdotto']}</td>
                            <td>{$prodotto['quantita']}</td>
                            <td>€" . number_format($prodotto['prezzo_unitario'], 2) . "</td>
                            <td>€" . number_format($prodotto['subtotale'], 2) . "</td>
                          </tr>";
            }
    
            $body .= "</tbody>";
            $body .= "</table>";
            
            // Totale dell'ordine
            $body .= "<p><strong>Totale Ordine: €" . number_format($totale, 2) . "</strong></p>";
    
            // Imposta il corpo dell'email
            $mail->Body = $body;
        
            // Invia l'email
            if ($mail->send()) {
                return true;
            } else {
                echo "Errore durante l'invio dell'email.";
            }
        } catch (Exception $e) {
            echo "Errore: " . $mail->ErrorInfo;
        }
    }

    private function inserisciDettagliOrdine($id_carrello, $tipologia, $orarioConsegna, $note) {
        $sqlDettagli = "INSERT INTO carrello_dettaglio (id_carrello, tipologia, orario_consegna, note)
                        VALUES ('$id_carrello', '$tipologia', '$orarioConsegna', '$note')";
        
        return $this->conn->query($sqlDettagli) === TRUE;
    }
        
    private function salvaInformazioniUtente($nome, $cognome, $email, $indirizzo, $telefono, $id_carrello) {
        $sqlUtente = "INSERT INTO utente_carrello (nome, cognome, email, indirizzo, telefono, id_carrello)
                      VALUES ('$nome', '$cognome', '$email', '$indirizzo', '$telefono', '$id_carrello')";
        
        return $this->conn->query($sqlUtente) === TRUE;
    }
    
    
}
