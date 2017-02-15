<?php
require_once("inc/init.inc.php");

$tab_produit = array(); // tableau regrouppant les informations de salles et leurs produits

/***** GESTION DU FILTRE ET CONSTRUCTION DE LA REQUETE *****/
// Initialisation des variables pour le filtre de recherche

$date_arrivee= "";
$date_depart="";
$prix=10000;   //le plus cher par defaut
$capacite=0;
$capacite_affichage = "indifferent";
$date_default = "date_arrivee > NOW()";
$filtre=array("etat='libre'", "$date_default");

$get_active = FALSE;

//listes pour les selects du filtre
// Liste villes
$resultat_ville = execute_requete("SELECT DISTINCT ville FROM salle");
$liste_ville="";

while ($element=$resultat_ville->fetch_assoc()){
	if (isset($_GET['ville']) && $_GET['ville']==$element['ville']){
		$liste_ville.='<option selected value="'.$element['ville'].'">'.ucfirst($element['ville']).'</option>';
	}
	else{
		$liste_ville.='<option value="'.$element['ville'].'">'.ucfirst($element['ville']).'</option>';
	}
}
// Liste catégories
$resultat_cat=execute_requete("SELECT DISTINCT categorie FROM salle");
$liste_cat="";

while ($element=$resultat_cat->fetch_assoc()){
	if (isset($_GET['categorie']) && $_GET['categorie']==$element['categorie']){
		$liste_cat.='<option selected value="'.$element['categorie'].'">'.ucfirst($element['categorie']).'</option>';
	}
	else{
		$liste_cat.='<option value="'.$element['categorie'].'">'.ucfirst($element['categorie']).'</option>';
	}
}
// Liste catégories
$resultat_salles=execute_requete("SELECT DISTINCT titre FROM salle");
$liste_salle="";

while ($element=$resultat_salles->fetch_assoc()){
	if (isset($_GET['salle']) && $_GET['salle']==$element['titre']){
		$liste_salle .='<option selected value="'.$element['titre'].'">'.ucfirst($element['titre']).'</option>';
	}
	else{
		$liste_salle .='<option value="'.$element['titre'].'">'.ucfirst($element['titre']).'</option>';
	}
}

if (isset($_GET['categorie']) || isset($_GET['ville']) || isset($_GET['capacite']) || isset($_GET['prix']) || isset($_GET['date_arrivee']) || isset($_GET['date_depart']) || isset($_GET['salle'])){
	$get_active = TRUE;
        /* HTMLENTITIES pour matcher sur la BDD */
	foreach ($_GET as $key => $value) {
		$_GET[$key]=htmlentities($value,ENT_QUOTES);
	}
	/******* TRIS HORS DATE ********/
	if (!empty($_GET['categorie']) && $_GET['categorie'] != "indifferent"){
		array_push($filtre, "categorie='".$_GET['categorie']."'");
	}
	if (!empty($_GET['ville']) && $_GET['ville'] != "indifferent"){
		array_push($filtre, "ville='".$_GET['ville']."'");
	}
	if (!empty($_GET['capacite'])){
		array_push($filtre, "capacite >= ".$_GET['capacite']);
		$capacite=$_GET['capacite'];
		$capacite_affichage=$_GET['capacite'];
	}
	else
	{
		$capacite_affichage = "indifferent";
	}
        if (!empty($_GET['salle']) && $_GET['salle'] != "indifferent"){
		array_push($filtre, "titre='".$_GET['salle']."'");
	}
	if (!empty($_GET['prix'])){
		array_push($filtre, "prix <= ".$_GET['prix']);
		$prix=$_GET['prix'];
	}
	/******* VERIFICATIONS DATE ********/
		// Date arrivée
	if(!empty($_GET['date_arrivee'])) 
	{	
		if (new DateTime($_GET['date_arrivee']) < new DateTime(date("d-m-Y H:i")))
		{
			$msg .= "<p class='error'>La date d'arrivée doit correspondre à une date future</p>";
		}
	}	
	if(!empty($_GET['date_depart'])) 
	{
		// Date départ
		if (new DateTime($_GET['date_depart']) < new DateTime(date("d-m-Y H:i")))
		{
			$msg .= "<p class='error'>La date de départ doit correspondre à une date future</p>";
		}
	}
	if(!empty($_GET['date_depart']) && !empty($_GET['date_arrivee']))
	{
		if (new DateTime($_GET['date_depart']) < new DateTime($_GET['date_arrivee']))
		{
			$msg .= "<p class='error'>La date de départ doit correspondre à une date supérieure à la date d'arrivée</p>";
		}		
	} 		
	/******* TRI DATE ********/
	if(empty($msg))
	{
		if(!empty($_GET['date_arrivee'])) 
		{
			array_splice($filtre, 1, 1);
			$date_arrivee=$_GET['date_arrivee'];
			$_GET['date_arrivee'] = date('Y-m-d H:i:s' ,strtotime($_GET['date_arrivee']));
			array_push($filtre, "date_arrivee >= '".$_GET['date_arrivee']."'");
		}
		if(!empty($_GET['date_depart']))	
		{
			$date_default ="";
			$date_depart=$_GET['date_depart'];
			$_GET['date_depart'] = date('Y-m-d H:i:s' ,strtotime($_GET['date_depart']));
			array_push($filtre, "date_depart <= '".$_GET['date_depart']."'");			
		}
	}
}
// Creation du filtre de requête
//debug($filtre);
$filtre=" WHERE ".implode($filtre, " AND ");

/***** END gestion du filtre et construction de la requete *****/

$nb_result = "";
// Je mets dans un array les informations de chaque produit y compris les infos de la salle qui correspond
if($get_active)
{
    $contenu_produit = execute_requete(
        "SELECT * FROM produit p JOIN salle s ON p.id_salle=s.id_salle "
            . $filtre
            . " GROUP BY p.id_produit"
            . " ORDER BY p.date_arrivee ASC"
            );
    $nb_result = mysqli_affected_rows($mysqli);
    while($produit = $contenu_produit->fetch_assoc())
    {
        $tab_produit[] = $produit;
    }
} else {
    $contenu_produit = execute_requete("SELECT * FROM produit");
    $nb_result = mysqli_affected_rows($mysqli);
    while($produit = $contenu_produit->fetch_assoc())
    {
            $contenu_salle = execute_requete("SELECT * FROM salle WHERE id_salle = '$produit[id_salle]'");

            $salle = $contenu_salle->fetch_assoc();

            $produit["titre"] = $salle['titre'];
            $produit["description"] = $salle['description'];
            $produit["photo"] = $salle['photo'];
            $produit["photo_2"] = $salle['photo_2'];
            $produit["photo_3"] = $salle['photo_3'];
            $produit["ville"] = $salle['ville'];
            $produit["adresse"] = $salle['adresse'];
            $produit["cp"] = $salle['cp'];
            $produit["capacite"] = $salle['capacite'];
            $produit["categorie"] = $salle['categorie'];
            $produit["pays"] = $salle['pays'];
            $produit["id_salle"] = $salle['id_salle'];

            $tab_produit[] = $produit;

    }
}
// Texte affiché pour le résultat de produits
$p = "";
if($nb_result == 0 || $nb_result > 1)
{
    $p = 's';
}
$nb_produit_texte = $nb_result . ' produit' . $p . ' disponible' . $p;



// 
//debug($tab_produit);

include("inc/header.inc.php");
include("inc/nav.inc.php");
?>

    <div class="container">
          <h1 class="hometitre" style="color: #225BAA;">CavaSalle - Réservation de salles pour professionnels </h1>
          <hr/>
          <button type="button" class="btn btn-primary recherche_mobile" style="width:100%;"><span class="glyphicon glyphicon-search"></span>
           RECHERCHEZ VOTRE SALLE
          </button>
<!--     
        <div class="starter-template">
            <?php echo $msg; // variable initialisée dans le fichier init.inc.php ?>
        </div>-->
          <div class="row">
              <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
                <!-- Indicators -->
                <ol class="carousel-indicators">
                  <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
                  <li data-target="#carousel-example-generic" data-slide-to="1"></li>
                  <li data-target="#carousel-example-generic" data-slide-to="2"></li>
                  <li data-target="#carousel-example-generic" data-slide-to="3"></li>
                </ol>

                <!-- Wrapper for slides -->
                <div class="carousel-inner" role="listbox">
                  <div class="item active">
                      <img src="img/homepub.jpg" alt="...">
                      <div class="carousel-caption">
                          <h2><b></b></h2>
                        <p></p>
                      </div>
                  </div>
                    <div class="item">
                    <img src="img/conference_1.jpg" alt="...">
                    <div class="carousel-caption">
                        <h2><b>Salles Conférence</b></h2>
                       <p>Conférence, Assemblée...</p>
                    </div>
                  </div>
                  <div class="item">
                    <img src="img/reu_1.jpg" alt="...">
                    <div class="carousel-caption">
                        <h2><b>Salles Réunion</b></h2>
                       <p>Réunion, entretien, présentation...</p>
                    </div>
                  </div>
                  <div class="item">
                    <img src="img/standup2.jpg" alt="...">
                    <div class="carousel-caption">
                        <h2><b>Salles StandUp Meeting - Buffet</b></h2>
                      <p>Pour Petit-Déjeuner, Rencontre, Débat...</p>
                    </div>
                  </div>
                </div>

                <!-- Controls -->
                <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
                  <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                  <span class="sr-only">Previous</span>
                </a>
                <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
                  <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                  <span class="sr-only">Next</span>
                </a>
            </div>
              
          </div>
         <div class="row">
            <aside id="filtres-boutique" class="col-sm-3">
                    <form method="GET" action="" class="form">
                        <div style="padding-top: 50px; color: #225BAA; font-size:14pt;"><i> </i></div>
                            <div class="form-group">
                                    <label for="categorie">Catégorie </label>
                                    <select name="categorie" id="cattegorie" class="form-control">
                                            <option value="indifferent">Indifférent</option>
                                            <?= $liste_cat ?>
                                    </select>
                            </div>

                            <div class="form-group">
                                    <label for="ville"> Ville </label>
                                    <select name="ville" id="ville" class="form-control">
                                            <option value="indifferent">Indifférent</option>
                                            <?= $liste_ville ?>
                                    </select>
                            </div>

                            <!-- ajouter du javascript pour afficher la valeur des input dessous -->
                            <div class="form-group">
                                    <label for="capacite"> Capacité : <span id="capacite-filtre"><?= $capacite_affichage ?></span> <span class="small">(minimum)</span></label>
                                    <input id="capacite" type="range" value="<?= $capacite ?>" max="100" min="0" step="5" name="capacite">
                            </div>

                            <div class="form-group">
                                    <label for="prix"> Prix : <span id="prix-filtre"><?= $prix ?></span> &euro; <span class="small">(maximum)</span></label>
                                    <input type="range" value="<?= $prix ?>" max="10000" min="0" step="200" name="prix" id="prix">
                            </div>

                            <?= $msg ?>
                            
                            <div class="form-group">
                                    <label for="date-arrive-pdt">Date d'arrivée</label>
                                    <div class="input-group">
                                            <div class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></div>
                                            <input type="text" class="form-control datepicker" name="date_arrivee" id="date-arrive-pdt" placeholder="Date d'arrivée" value="<?= $date_arrivee ?>">
                                    </div>
                            </div>
                            <div class="form-group">
                                    <label for="date-depart-pdt">Date de départ</label>
                                    <div class="input-group">
                                            <div class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></div>
                                            <input type="text" class="form-control datepicker" name="date_depart" id="date-depart-pdt" placeholder="Date de départ" value="<?= $date_depart ?>">
                                    </div>
                            </div>

                            <input type="submit" value="Valider" class="btn btn-default btn-ok">
                    </form>
                    <form method="GET" action="" class="form recherche_salle_index_cache">
                        <legend style="padding-top: 30px;">Recherche par salle</legend>
                            <div class="form-group">
                                    <label for="salle">Salle</label>
                                    <select name="salle" id="salle" class="form-control">
                                            <option value="indifferent">-- Choix de la salle --</option>
                                            <?= $liste_salle ?>
                                    </select>
                            </div>
                            <input type="submit" value="Valider" class="btn btn-default btn-ok">
                    </form>
                
             </aside>
             <div class="col-xs-9 col-sm-9">
             
                 <div class="nb-produits"><h4><?= $nb_produit_texte ?></h4></div>
              <?php 
                    // Affichage des différents produits
                    foreach($tab_produit AS $index => $val)
                    {
                        $reftitre = strtolower(substr($tab_produit[$index]['titre'], 0, 3));
                            ?>
                <div class="col-xs-12 col-sm-6 col-md-5 col-lg-4" style="min-width:271px;">
                    <div class=" panel panel-primary">
                        <div class="panel-body" style="min-width:240px;">
                            <?php 
                                if(!empty($tab_produit[$index]['photo']))
                                {
                                    echo '<p><a href="fiche_produit.php?id=' . $tab_produit[$index]['id_produit'] . '"><img class="fichepro" src="' . $tab_produit[$index]['photo'] . '" /></a></p>';
                                }elseif(!empty($tab_produit[$index]['photo_2']))
                                {
                                    echo '<p><a href="fiche_produit.php?id=' . $tab_produit[$index]['id_produit'] . '"><img class="fichepro" src="' . $tab_produit[$index]['photo_2'] . '" /></a></p>';
                                }elseif(!empty($tab_produit[$index]['photo_3']))
                                {
                                    echo '<p><a href="fiche_produit.php?id=' . $tab_produit[$index]['id_produit'] . '"><img class="fichepro" src="' . $tab_produit[$index]['photo_3'] . '" /></a></p>';
                                } else
                                {
                                    echo '<p><a href="fiche_produit.php?id=' . $tab_produit[$index]['id_produit'] . '"><img class="fichepro" src="img/no_photo.jpg" /></a></p>';
                                }
                                $type_salle="Salle ";
                                if($tab_produit[$index]['categorie'] == 'bureau')
                                {
                                        $type_salle = "Bureau ";
                                }
                            ?>
                            <div class="row">
                                    <p class="col-sm-11 salle_titre"><?php echo '<strong>' . $tab_produit[$index]['ville'] . '</strong>' . ' - ' . $type_salle . $tab_produit[$index]['titre']; ?></p>
                                    <p class="col-sm-4 salle_prix"><?php echo $tab_produit[$index]['prix']; ?> €</p>
                            </div>
                            <p class="index-descr-fiche"><?php echo substr($tab_produit[$index]['description'], 0, 26); ?>...</p>
                            <p class="index-ref-fiche"> <small> <i>Référence: <?php echo $tab_produit[$index]['id_produit'].$reftitre; ?></i></small></p>
                            <p class="glyphicon glyphicon-calendar"> <?php 
                                if (substr(change_date($tab_produit[$index]['date_arrivee']), 0, 10)==substr(change_date($tab_produit[$index]['date_depart']), 0, 10))
                                    {
                                    echo substr(change_date($tab_produit[$index]['date_arrivee']), 0, 10) . ' <small> ' . ' de ' . substr(change_date($tab_produit[$index]['date_arrivee']), 11, 5) . ' à ' . substr(change_date($tab_produit[$index]['date_depart']), 11, 5) . '</small>' ;
                                    } else {
                                    echo substr(change_date($tab_produit[$index]['date_arrivee']), 0, 10) . ' au ' . substr(change_date($tab_produit[$index]['date_depart']), 0, 10); 
                                    } ?>
                            </p>
                            <div class="row">
                                <div class="index-voir col-sm-6 col-sm-offset-3">
                                 <a href="fiche_produit.php?id=<?= $tab_produit[$index]['id_produit'] ?>"><button type="button" class="btn btn-info index-bouton-voir"><span class="glyphicon glyphicon-search"></span> Voir</button></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>

            </div>
	</div>
	  

    </div><!-- /.container -->

<?php
include("inc/footer.inc.php");