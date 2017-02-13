<?php
require_once("inc/init.inc.php");

if(!preg_match('#^[0-9]+$#', $_GET['id'])) // on vérifie si l'id ne contient que des chiffres.
{
	header("location:index.php"); // sinon on renvoi sur l'accueil
	exit();
}

$id_produit = $_GET['id']; // récupération de l'id
$get_produit = execute_requete("SELECT * FROM salle s, produit p WHERE s.id_salle = p.id_salle AND id_produit = $id_produit");
if($get_produit->num_rows == 0) // si il n'a pas de ligne, l'id ne correspond à rien => on renvoi sur l'accueil		  
{
  header("location:index.php");
}
$produit = $get_produit->fetch_assoc();
$reftitre = strtolower(substr($produit['titre'], 0, 3));
$reftitrecat = strtolower(substr($produit['categorie'], 0, 2));

// Dans la même ville
$ville = $produit['ville'];
$autres_ville = execute_requete("SELECT * FROM salle WHERE ville = '$ville' LIMIT 4");

// Disponibilités
$salle = $produit['id_salle'];
$dispos = execute_requete("SELECT * FROM produit WHERE id_salle = '$salle' AND date_arrivee > NOW() LIMIT 3");

//debug($autres_villes);

// Choix de la photo à afficher
$photo_affichee = "img/no_photo.jpg";
if(!empty($produit['photo'])){
    $photo_affichee = $produit['photo'];
}elseif (!empty($produit['photo_2'])) {
    $photo_affichee = $produit['photo_2'];
}elseif (!empty($produit['photo_3'])) {
    $photo_affichee = $produit['photo_3'];
}

include("inc/header.inc.php");
include("inc/nav.inc.php");
?>

    <div class="container">
        <hr/>
        <h1 class="fiche_produit_h1"> <span class="glyphicon glyphicon-tags" aria-hidden="true"></span> &nbsp; Réservation de la salle <b> <?php echo $produit['titre'] ?> </b>  </h1>
        <h2 class="fiche_produit_h2"> <small><i> Salle <?php echo $produit['categorie'] ?> - Réference: <?php echo $reftitrecat.$reftitre.$produit['id_produit'] ?> </i></small></i></h2>
	  <div class="row">
		<div class="col-sm-8" style="display: table-cell;">
                    <div class=" panel panel-primary fiche_produit">
				<div class="col-sm-12 fiche-top gallery" id="gallery">
                                    <div class="col-sm-9 fiche_main_pic" id="lg-wrap" > <?php echo '<img id="large" data-idx="0" src="' . $photo_affichee . '" />'; ?>
                                    </div>
                                    <div class="col-sm-3 fiche_other_pics" id='thumbnails'>
                                        <div class="fiche_second_pic">
                                            <a href=" <?php echo $produit['photo_2'] ?> "> <?php echo '<img class="img-thumbnail" data-idx="1" src="' . $produit['photo_2'] . '" />'; ?> </a>
                                        </div>
                                        <div class="fiche_third_pic">
                                            <a href=" <?php echo $produit['photo_3'] ?> "> <?php echo '<img class="img-thumbnail" data-idx="2" src="' . $produit['photo_3'] . '" />'; ?> </a>
                                        </div>
                                        <div class="fiche_first_pic">
                                            <a href=" <?php echo $produit['photo'] ?> "> <?php echo '<img class="img-thumbnail" data-idx="0" src="' . $produit['photo'] . '" />'; ?> </a>
                                        </div>
                                    </div>
                                </div>
				<div class="panel-body">
                                       <p class="fiche_produit_dispo"><span class="label_fiche"> <b> Description :</b> </span> </p>
                                       <p class="fiche_produit_description"><?php echo $produit['description']; ?></p>
                                       <hr/>
                                       <div class="col-sm-6" style="padding-left: 0">
                                            <p class="fiche_produit_dispo"><span class="label_fiche"> <b> Caractéristiques :</b> </span> </p>
                                            <p><span class="label_fiche"> Entrée : </span><?php echo ' <span class="glyphicon glyphicon-arrow-right" aria-hidden="true"></span>  ' . '  ' . ' <b> ' . substr(change_date($produit['date_arrivee']), 0, 16) . ' </b>'; ?></p>
                                            <p><span class="label_fiche"> Sortie : </span><?php echo ' <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>  ' . ' ' . ' <b> ' . substr(change_date($produit['date_depart']), 0, 16) . '</b>'; ?></p>
                                       </div>
                                       <div class="col-sm-6">
                                            <p class="fiche_produit_dispo"><span class="label_fiche"> <b> </b> </span> </p>
                                            <p class="fiche_produit_dispo"><span class="label_fiche">Capacité :</span> <?php echo ' jusqu\'à ' . $produit['capacite'] . ' ' . ' personnes '; ?></p>
                                          
                                            <p class="fiche_produit_dispo"><span class="label_fiche">Ville : </span><?php echo  $produit['ville']; ?></p>
                                            <p class="fiche_produit_dispo"><span class="label_fiche">Prix : </span><b> <?php echo $produit['prix'] . ' euros ' ; ?> <b/></p>
					<br/>
                                       </div>
					
                                      
					<?php
				
					echo '<form method="post" action="panier.php">';
			
					echo '<input type="submit" class="form-control btn btn-success" name="ajout_panier" value="Demande de Réservation" />';
					echo '</form>';
				
					?>
					<br/>
					<p style="text-align: center;"><a href="index.php"><span class="glyphicon glyphicon-triangle-left" aria-hidden="true"></span>Retour à l'accueil</a></p>
				</div>
			</div>
                </div>
              <!-- GOOGLE MAP -->
              <div class="col-sm-4" style="display: table-cell;">
                   <div class=" panel panel-primary fiche_produit" >
                        <legend> Accès à la salle :  </legend>
                        <p class="adresse_carte"><span class="label_fiche"> Adresse :</span></p>
                         <span id="adresse"><?php echo $produit['adresse'] . ' ' . $produit['cp'] . ' ' . $produit['ville']; ?></span>
                        <div id="map">

                        </div>
                        <small><span id="text_latlng"></span></small>
                   </div>
                  <div class=" panel panel-primary fiche_produit">
                        <legend><small><a href="index.php?salle=<?= $produit['titre'] ?>">Voir les autres disponibilités... </a></small></legend>
                        <p class="fiche_produit_dispo"><span class="label_fiche" style="padding:10px;">Salle :</span><?= $produit['titre'] ?></p>
                        <table>
                        <?php
                        while($dispo = $dispos->fetch_assoc())
                        {
                            if($dispo['date_arrivee'] != $produit['date_arrivee'])
                            {
                        ?>
                            <tr>
                                <td class="dispo">
                                    <a href="fiche_produit.php?id=<?= $dispo['id_produit'] ?>">Du <?= substr(change_date($dispo['date_arrivee']), 0, 10) ?> au <?= substr(change_date($dispo['date_depart']), 0, 10) ?> : <?= $dispo['prix'] ?></a>
                                </td>
                            </tr>
                        <?php
                            }
                        }
                        ?>
                        </table>
                  </div>
              </div>
            
	  </div> <!-- ROW -->
          <div class="row">
                <div class="fiche_produits_salles_villes panel panel-primary">
                    <legend><a href="index.php?ville=<?= $produit['ville'] ?>">Egalement à <?= $produit['ville'] ?>...</a></legend>
                    
                    <div class="row">
                        <?php
                            while($autres_salles = $autres_ville->fetch_assoc())
                            {
                                if ($autres_salles['id_salle'] != $produit['id_salle'])
                                {
                                    $photo_salle = "img/no_photo.jpg";
                                    if(!empty($autres_salles['photo'])){
                                        $photo_salle = $autres_salles['photo'];
                                    }elseif (!empty($autres_salles['photo_2'])) {
                                        $photo_salle = $autres_salles['photo_2'];
                                    }elseif (!empty($autres_salles['photo_3'])) {
                                        $photo_salle = $autres_salles['photo_3'];
                                    }
                        ?>
                        <div class="col-xs-6 col-md-3">
                                <div class=" panel vignette_fichpro panel-primary">
                                    <div class="panel-body">
                                        <p style="text-align: center;">
                                            Salle <?php echo $autres_salles['titre'] . ' ' . ' - ' . ' ' . $autres_salles['categorie'] ?> 
                                        </p>
                                        <p style="text-align: center; font-size: 9pt;">
                                            Capacité <?php echo $autres_salles['capacite'] . ' ' . ' personnes ' ?>
                                        </p>
                                           <a href="index.php?salle=<?= $autres_salles['titre'] ?>"><img class="fichepro" src="<?= $photo_salle ?>"></a>
                                    </div>
                                    <div class="row">
                                        <div class="index-voir col-sm-10 col-sm-offset-1">
                                            <a href="index.php?salle=<?= $autres_salles['titre'] ?>"><button type="button" class="btn btn-primary index-bouton-voir raised"><small><span class="glyphicon glyphicon-search"></span> Voir les prduits</small></button></a>
                                        </div>
                                    </div> 
                                </div>
                        </div>
                        <?php
                                }
                            }
                        ?>
                    </div>
              </div>
          </div> <!-- ROW -->
    </div><!-- /.container -->
    
    
    
<!--  /* ******* FICHE PRODUIT ******* */-->
<script>
// Disposition/Gestion photos
var th = document.getElementById('thumbnails');

th.addEventListener('click', function(e) {
  var t = e.target, new_src = t.parentNode.href, 
      large = document.getElementById('large'),
      cl = large.classList,
      lgwrap = document.getElementById('lg-wrap');
  lgwrap.style.backgroundImage = 'url(' +large.src + ')';
  if(cl) cl.add('hideme');
  window.setTimeout(function(){
    large.src = new_src;
    if(cl) cl.remove('hideme');
  }, 50);
  e.preventDefault();
}, false);
</script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?language=fr&key=AIzaSyAStftnDxrXsZeedcHQnErvd3HdkbNGZIQ">
</script>
<script type="text/javascript">
    var geocoder;
    var map;
    // initialisation de la carte Google Map de départ
    function initialiserCarte() {
        geocoder = new google.maps.Geocoder();
        // longitude du vieux Port de Marseille pour centrer la carte de départ
        var latlng = new google.maps.LatLng(43.295309,5.374457);
        var mapOptions = {
            zoom      : 14,
            center    : latlng,
            mapTypeId : google.maps.MapTypeId.ROADMAP
        }
        // map-canvas est le conteneur HTML de la carte Google Map
        map = new google.maps.Map(document.getElementById('map'), mapOptions);
        trouverAdresse();
    }
    function trouverAdresse() {
        // Récupération de l'adresse
        var adresse = document.getElementById('adresse').innerHTML;
        console.log(adresse);
        geocoder.geocode( { 'address': adresse}, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                map.setCenter(results[0].geometry.location);
                // Récupération des coordonnées GPS de l'adresse
                var strposition = results[0].geometry.location+"";
                strposition=strposition.replace('(', '');
                strposition=strposition.replace(')', '');
                // Affichage des coordonnées dans le <span>
                document.getElementById('text_latlng').innerHTML='Coordonnées : '+strposition;
                // Création du marqueur du lieu (épingle)
                var marker = new google.maps.Marker({
                        map: map,
                        position: results[0].geometry.location
                });
            } else {
                alert('Adresse introuvable: ' + status);
            }
        });
    }
    // Lancement de la construction de la carte google map
    google.maps.event.addDomListener(window, 'load', initialiserCarte);
    
</script>
<?php
include("inc/footer.inc.php");