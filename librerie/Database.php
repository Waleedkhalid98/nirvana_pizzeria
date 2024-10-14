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

    public function ordina($nome, $cognome, $indirizzo, $telefono, $email, $orarioConsegna, $note, $deliveryType, $paymentType) {
        $id_carrello = $this->esisteCarrello();
        $data_odierna = date('Y-m-d');

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
            if ($this->inserisciDettagliOrdine($id_carrello, $tipologia, $orarioConsegna, $note, $paymentType)) {
                // Salva le informazioni dell'utente
                if ($this->salvaInformazioniUtente($nome, $cognome, $email, $indirizzo, $telefono, $id_carrello)) {
                    // Aggiorna il flag "ordinato" per il carrello
                    $sql = "UPDATE carrello 
                    SET flag_ordinato = 1,  
                        data_ordinazione = '$data_odierna'
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
        // Ottieni gli ordini e il totale del prezzo direttamente in un'unica query
        $ordini = get_data("
            SELECT 
                c.id_carrello, c.data_ordinazione, uc.nome, uc.cognome, uc.email, 
                SUM(pc.prezzo * pc.numero_prodotti) AS prezzo_totale
            FROM carrello c
            INNER JOIN utente_carrello uc ON c.id_carrello = uc.id_carrello
            INNER JOIN prodotticarrello pc ON c.id_carrello = pc.id_carrello
            WHERE c.flag_conferma IS NULL 
              AND c.flag_ordinato IS NOT NULL
              AND c.flag_eliminato =0
              AND c.flag_rifiutato =0
            GROUP BY c.id_carrello, uc.nome, uc.cognome, uc.email
        ");
    
        // Inizializza un array per raccogliere gli ordini elaborati
        $elencoOrdini = [];
        
        foreach ($ordini as $item) {
            if (is_array($item)) {
                // Aggiungi l'ordine all'elenco con il totale del prezzo
                $elencoOrdini[] = [
                    'id_carrello' => $item['id_carrello'],
                    'nome' => $item['nome'],
                    'cognome' => $item['cognome'],
                    'email' => $item['email'],
                    'data_ordinazione' => $item['data_ordinazione'],
                    'prezzo' => $item['prezzo_totale'] // Prezzo totale per l'ordine
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
        WHERE 
        (carrello.flag_conferma =1 or flag_eliminato=1) or(carrello.flag_conferma=1 or flag_rifiutato=1)
        AND carrello.flag_ordinato IS NOT NULL
        ");
        $elencoOrdini = [];
        
        foreach ($ordini as $item) {
            if (is_array($item)) {
                if ($item['flag_conferma'] == 1) {
                    $flag_confermato = "CONFERMATO";
                } elseif ($item['flag_eliminato'] == 1) {
                    $flag_confermato = "ELIMINATO";
                } elseif ($item['flag_rifiutato'] == 1) {
                    $flag_confermato = "RIFIUTATO";
                } 
                $elencoOrdini[] = [
                    'id_carrello' => $item['id_carrello'],
                    'nome' => $item['nome'],
                    'cognome' => $item['cognome'],
                    'email' => $item['email'],
                    'data_ordinazione' => $item['data_ordinazione'],
                    'flag_confermato' => $flag_confermato
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


            if($item['tipologia_pagamento']=="Elettronico"){
                $tipologia_pagamento="POS";
            }else{
                $tipologia_pagamento="CASH";
            }
    
            return [
                'success' => true,
                'data' => [
                    'id_carrello' => $item['id_carrello'],
                    'nome' => $item['nome'],
                    'cognome' => $item['cognome'],
                    'email' => $item['email'],
                    'indirizzo' => $item['indirizzo'],
                    'telefono' => $item['telefono'],
                    'pagamento' => $tipologia_pagamento,
                    'orario_consegna' => substr($item['orario_consegna'], 0, 5),
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

    public function visualizzaDettagliUtente($id_utente) {
        $utente = get_data("SELECT *  FROM utente_carrello WHERE id_utente_carrello='$id_utente';");



        // Controlla se l'ordine esiste
        if (!empty($utente) && is_array($utente[0])) {
            $item = $utente[0]; // Prendi il primo (e unico) elemento


    
            return [
                'success' => true,
                'data' => [
                    'nome' => $item['nome'],
                    'cognome' => $item['cognome'],
                    'email' => $item['email'],
                    'indirizzo' => $item['indirizzo'],
                    'telefono' => $item['telefono'],
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
        WHERE 
           carrello.flag_ordinato IS NOT NULL
          AND carrello.id_carrello='$id_carrello'");


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
                    'indirizzo' => $item['indirizzo'],
                    'telefono' => $item['telefono'],
                    'orario_consegna' => substr($item['orario_consegna'], 0, 5),
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

    public function confermaOrdine($id_carrello) {
        // Ottieni i dettagli dell'ordine incluso prodotti, tipologia, orario di consegna e note
        $ordini = get_data("
            SELECT 
                c.id_carrello, uc.nome, uc.cognome, uc.email, 
                p.descrizione AS nomeProdotto, pc.prezzo, pc.numero_prodotti,
                cd.tipologia, cd.orario_consegna, cd.note
            FROM carrello c
            INNER JOIN utente_carrello uc ON c.id_carrello = uc.id_carrello
            INNER JOIN carrello_dettaglio cd ON c.id_carrello = cd.id_carrello
            INNER JOIN prodotticarrello pc ON c.id_carrello = pc.id_carrello
            INNER JOIN prodotto p ON pc.id_prodotto = p.id
            WHERE c.flag_conferma IS NULL 
                AND c.id_carrello = '$id_carrello'
                AND c.flag_ordinato IS NOT NULL
        ");
    
        
        if (is_array($ordini) && count($ordini) > 0) {
            $ordine = $ordini[0];  // Recupera il primo elemento poiché tutti hanno lo stesso id_carrello
    
            // Converti il tipo di consegna in un valore leggibile
            $deliveryType = ($ordine['tipologia'] == 1) ? 'Delivery' : 'Asporto';
    
            // Inizializza le variabili per l'email
            $prodotti_ordinati = [];
            $totaleOrdine = 0;
    
            // Itera sugli ordini e calcola il totale dell'ordine
            foreach ($ordini as $item) {
                if (is_array($item)) {
                    // Calcola il prezzo totale del prodotto
                    $prezzoProdottoTotale = $item['prezzo'] * $item['numero_prodotti'];
                    $totaleOrdine += $prezzoProdottoTotale;
    
                    // Aggiungi i dettagli del prodotto all'array dei prodotti
                    $prodotti_ordinati[] = [
                        'nomeProdotto' => $item['nomeProdotto'],
                        'quantita' => $item['numero_prodotti'],
                        'prezzo_unitario' => $item['prezzo'],
                        'subtotale' => $prezzoProdottoTotale
                    ];
                }
            }
    

            // Invia l'email con i dettagli dell'ordine
         

            if($this->inviaEmailConferma(
                $ordine['nome'],
                $ordine['cognome'],
                $ordine['indirizzo'],    // Aggiungi indirizzo nel database se manca
                $ordine['telefono'],     // Aggiungi telefono nel database se manca
                $ordine['email'],
                $ordine['orario_consegna'],
                $ordine['note'],
                $deliveryType,
                $prodotti_ordinati,
                $totaleOrdine
            )){
                $sql = "UPDATE carrello 
                SET flag_conferma = 1
                WHERE id_carrello = '$id_carrello'";
        
                $update_success = $this->conn->query($sql) === TRUE;
            }
            
            if($update_success){
                return [
                    'success' => true,
                    'message' => 'Email inviata con successo.'
                ];
            }else{
                return [
                    'success' => false,
                    'message' => 'Errore invio mail.'
                ];
            }
           
        } else {
            return [
                'success' => false,
                'message' => 'Ordine non trovato.'
            ];
        }
    }


    public function eliminaOrdine($id_carrello) {
                $sql = "UPDATE carrello 
                SET flag_eliminato = 1
                WHERE id_carrello = '$id_carrello'";
        
                $update_success = $this->conn->query($sql) === TRUE;
            
            if($update_success){
                return [
                    'success' => true,
                    'message' => 'Eliminato con successo.'
                ];
            }else{
                return [
                    'success' => false,
                    'message' => 'Errore invio mail.'
                ];
            }
           
       
    }

    public function rifiutaOrdine($id_carrello) {
        // Recupera i dati dell'utente associati all'ordine
        $dati_utente = get_data("SELECT * FROM utente_carrello WHERE id_carrello='$id_carrello'");
    
        if (!empty($dati_utente) && is_array($dati_utente[0])) {
            $item = $dati_utente[0]; // Prendi il primo (e unico) elemento
    
            // Recupera le informazioni necessarie per l'invio dell'email
            $nome = $item['nome'];
            $cognome = $item['cognome'];
            $email = $item['email'];
    
            // Aggiorna il flag dell'ordine come rifiutato
            $sql = "UPDATE carrello 
                    SET flag_rifiutato = 1
                    WHERE id_carrello = '$id_carrello'";
            $update_success = $this->conn->query($sql) === TRUE;
    
            // Se l'aggiornamento è andato a buon fine, invia l'email
            if ($update_success) {
                $email_inviata = $this->inviaEmailRifiutoOrdine($nome, $cognome, $email);
                if ($email_inviata) {
                    return [
                        'success' => true,
                        'message' => 'Ordine rifiutato e email inviata con successo.'
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'Ordine rifiutato, ma si è verificato un errore durante l\'invio dell\'email.'
                    ];
                }
            } else {
                return [
                    'success' => false,
                    'message' => 'Errore nell\'aggiornamento dello stato dell\'ordine.'
                ];
            }
        } else {
            return [
                'success' => false,
                'message' => 'Nessun ordine trovato con l\'ID fornito.'
            ];
        }
    }
    
    
    public function elencoUtenti() {
        // Ottieni gli ordini e il totale del prezzo direttamente in un'unica query
        $utenti = get_data("SELECT distinct * FROM utente_carrello");
    
        // Inizializza un array per raccogliere gli ordini elaborati
        $elencoUtenti = [];
        
        foreach ($utenti as $item) {
            if (is_array($item)) {
                // Aggiungi l'ordine all'elenco con il totale del prezzo
                $elencoUtenti[] = [
                    'id_utente' => $item['id_utente_carrello'],
                    'nome' => $item['nome'],
                    'cognome' => $item['cognome'],
                    'email' => $item['email'],
                    'telefono' => $item['telefono'] // Prezzo totale per l'ordine
                ];
            } else {
                error_log("Elemento non valido nell'ordine: " . print_r($item, true));
            }
        }
    
        return [
            'success' => TRUE,
            'elencoUtenti' => $elencoUtenti
        ];
    }
    

    
    public function numeroConfermare() {
        // Ottieni gli ordini e il totale del prezzo direttamente in un'unica query
        $ordini = get_data("
        SELECT distinct 
            COUNT(c.id_carrello)
           
        FROM carrello c
        INNER JOIN utente_carrello uc ON c.id_carrello = uc.id_carrello
        WHERE c.flag_conferma IS NULL 
          AND c.flag_ordinato IS NOT NULL
          AND c.flag_eliminato=0
          AND c.flag_rifiutato=0;
       
    ");
        
     
           
    
        return [
            'success' => TRUE,
            'numero' => $ordini
        ];
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

    private function inviaEmailConferma($nome, $cognome, $indirizzo, $telefono, $email, $orarioConsegna, $note, $deliveryType, $prodotti_ordinati, $totale) {
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
            $mail->Password = "sdnp lnie tvvk gsmu";  // Assicurati di non mantenere la password in chiaro nel codice finale!
            
            // Mittente e destinatario
            $mail->setFrom('mittente@email.com', 'Sistema Ordini');
            $mail->addAddress($email);  
            
            // Oggetto dell'email
            $mail->Subject = 'Nuovo Ordine Ricevuto da ' . $nome . ' ' . $cognome;
        
            // Corpo dell'email
            $body = "<h1>Conferma Ordine Nirvana</h1>";
            $body .= "<p>Di seguito i dettagli dell'ordine:</p>";
            
            // Dettagli del cliente
            $body .= "<ul>";
            $body .= "<li><strong>Nome:</strong> $nome $cognome</li>";
            $body .= "<li><strong>Indirizzo:</strong> $indirizzo</li>";
            $body .= "<li><strong>Telefono:</strong> $telefono</li>";
            $body .= "<li><strong>Email:</strong> $email</li>";
            $body .= "<li><strong>Orario di Consegna:</strong> $orarioConsegna</li>";
            $body .= "<li><strong>Note:</strong> $note</li>";
            $body .= "<li><strong>Tipo di Consegna:</strong> $deliveryType</li>";
            $body .= "</ul>";
        
            // Prodotti ordinati
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

    private function inviaEmailRifiutoOrdine($nome, $cognome, $email) {
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
            $mail->Password = "sdnp lnie tvvk gsmu"; // Assicurati di sostituire questa password con una sicura
            $mail->setFrom('noreply@tuosito.com', 'Sistema Ordini');
            $mail->addAddress($email);  
    
            // Oggetto dell'email
            $mail->Subject = 'Rifiuto Ordine da ' . $nome . ' ' . $cognome;
    
            // Corpo dell'email
            $body = "<h1>Rifiuto dell'Ordine</h1>";
            $body .= "<p>Caro $nome $cognome, purtroppo il tuo ordine è stato rifiutato. Ci scusiamo per l'inconveniente.</p>";
            $body .= "<p>Se hai domande, non esitare a contattarci.</p>";
    
            // Imposta il corpo dell'email
            $mail->Body = $body;
    
            // Invia l'email
            if ($mail->send()) {
                return true;
            } else {
                echo "Errore durante l'invio dell'email: " . $mail->ErrorInfo;
                return false;
            }
        } catch (Exception $e) {
            echo "Errore: " . $mail->ErrorInfo;
            return false;
        }
    }
    
    

    private function inserisciDettagliOrdine($id_carrello, $tipologia, $orarioConsegna, $note, $paymentType) {
        $sqlDettagli = "INSERT INTO carrello_dettaglio (id_carrello, tipologia, orario_consegna, note, tipologia_pagamento)
                        VALUES ('$id_carrello', '$tipologia', '$orarioConsegna', '$note','$paymentType')";
        
        return $this->conn->query($sqlDettagli) === TRUE;
    }
        
    private function salvaInformazioniUtente($nome, $cognome, $email, $indirizzo, $telefono, $id_carrello) {
        $sqlUtente = "INSERT INTO utente_carrello (nome, cognome, email, indirizzo, telefono, id_carrello)
                      VALUES ('$nome', '$cognome', '$email', '$indirizzo', '$telefono', '$id_carrello')";
        
        return $this->conn->query($sqlUtente) === TRUE;
    }
    
    
}
