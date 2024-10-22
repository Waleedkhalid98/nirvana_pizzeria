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


function get_data($query)
{
  $db = new Database();
  $array = array();
  $ordini = $db->conn->query($query);

  if ($ordini) {
    while ($row = $ordini->fetch_assoc()) {
      $array[] = $row;
    }
  } else {
    // Gestione errore query
  }

  return $array;
}




function db_fill_array($query) {
  $db = new Database();
  $array = []; // Inizializza l'array vuoto

  $result = $db->conn->query($query);
  
  if ($result) {
      while ($row = $result->fetch_assoc()) {
          $array[] = $row; // Aggiunge ogni riga come array associativo
      }
  } else {
      // Gestione errore query
      error_log("Errore nella query: " . $db->conn->error);
  }
  
  return $array;
}
function get_db_value($query, $params = [])
{
    $db = new Database();
    $stmt = $db->conn->prepare($query);

    if ($stmt === false) {
        error_log("Errore nella preparazione della query: " . $db->conn->error);
        return null;
    }

    if (!empty($params)) {
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result === false) {
        error_log("Errore nell'esecuzione della query: " . $db->conn->error);
        return null;
    }

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return reset($row); 
    } else {
        return null;
    }
}


function footer(){

  echo '    <footer class="ftco-footer ftco-section img">
		<div class="overlay"></div>
		<div class="container">
			<div class="row mb-5">
			<div class="col-lg-4 col-md-6 mb-5">
					<div class="ftco-footer-widget mb-4">
						<h2 class="ftco-heading-2">Recapiti</h2>
						<div class="block-23 mb-3">
							<ul>
								<li><span class="icon icon-map-marker"></span><span class="text">Via Madonna delle carceri 4, Camerino, MC 62032</span></li>
								<li><span class="icon icon-phone"></span><span class="text">+39 3295695194</span></li>
								<li><span class="icon icon-envelope"></span><span class="text">esempio@mail.com</span></li>
								<li class="ftco-animate"><a href="https://www.instagram.com/nirvanacamerino/" target=”_blank”><span class="icon-instagram"></span></a></li>
							</ul>
						</div>
					</div>
				</div>
	
				<div class="col-lg-4 col-md-6 mb-5">
					<div class="ftco-footer-widget mb-4 ml-md-4">
						<h2 class="ftco-heading-2">Pagine</h2>
						<ul class="list-unstyled">
						  <li><a href="index.php" class="py-2 d-block">Home</a></li>
						  <li><a href="menu.php" class="py-2 d-block">Menu</a></li>
						  <li><a href="blog.html" class="py-2 d-block">Blog</a></li>
						  <li><a href="contatti.html" class="py-2 d-block">Contatti</a></li>
					  </ul>
					</div>
				</div>
	
				<!-- <div class="col-lg-4 col-md-6 mb-5">
					<div class="ftco-footer-widget mb-4">
						<h2 class="ftco-heading-2">Eventi recenti</h2>
						<div class="block-21 mb-4 d-flex">
							<a class="blog-img mr-4" style="background-image: url(images/barto.png);"></a>
							<div class="text">
								<h3 class="heading"><a href="#">DJ set</a></h3>
								<div class="meta">
									<div><span class="icon-calendar"></span> Sept 15, 2018</div>
									<div><span class="icon-person"></span> Admin</div>
									<div><span class="icon-chat"></span> 19</div>
								</div>
							</div>
						</div>
						<div class="block-21 mb-4 d-flex">
							<a class="blog-img mr-4" style="background-image: url(images/university.png);"></a>
							<div class="text">
								<h3 class="heading"><a href="#">Dj set</a></h3>
								<div class="meta">
									<div><span class="icon-calendar"></span> Sept 15, 2018</div>
									<div><span class="icon-person"></span> Admin</div>
									<div><span class="icon-chat"></span> 19</div>
								</div>
							</div>
						</div>
					</div>
				</div> -->
	
			</div>
			<div class="row">
				<div class="col-md-12 text-center">
					<p>
						Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved
					</p>
				</div>
			</div>
		</div>
	</footer>
    ';
}


function getHeader(){
  '<nav class="navbar navbar-expand-lg navbar-dark ftco_navbar bg-dark ftco-navbar-light" id="ftco-navbar">
	    <div class="container">
	      <a class="navbar-brand" href="index.php"><span class="flaticon-pizza-1 mr-1"></span>Nirvana<br><small>Pub Pizzeria</small></a>
	      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#ftco-nav" aria-controls="ftco-nav" aria-expanded="false" aria-label="Toggle navigation">
	        <span class="oi oi-menu"></span> 
	      </button>
	      <div class="collapse navbar-collapse" id="ftco-nav">
	        <ul class="navbar-nav ml-auto">
	          <li class="nav-item active"><a href="index.php" class="nav-link">Home</a></li>
	          <li class="nav-item"><a href="menu.php" class="nav-link">Menu</a></li>
	          <li class="nav-item"><a href="blog.html" class="nav-link">Eventi</a></li>
	          <li class="nav-item"><a href="contatti.html" class="nav-link">Contatti</a></li>
	        </ul>
	      </div>
		  </div>
	  </nav>'; 
}