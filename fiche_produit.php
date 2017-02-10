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


$ville = $produit['ville'];
$autres_villes = execute_requete("SELECT * FROM salle WHERE ville = '$ville' LIMIT 3");
//debug($autres_villes);


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
                                    <div class="col-sm-9 fiche_main_pic" id="lg-wrap" > <?php echo '<img id="large" data-idx="0" src="' . $produit['photo'] . '" />'; ?>
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
                                            <p><span class="label_fiche"> Entrée : </span><?php echo ' <span class="glyphicon glyphicon-arrow-right" aria-hidden="true"></span>  ' . ' ' . ' <b> ' . $produit['date_arrivee'] . ' </b>' . ' '; ?></p>
                                            <p><span class="label_fiche"> Sortie : </span><?php echo ' <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>  ' . ' ' . ' <b> ' . $produit['date_depart'] . '</b>'; ?></p>
                                       </div>
                                       <div class="col-sm-6">
                                            <p class="fiche_produit_dispo"><span class="label_fiche"> <b> </b> </span> </p>
                                            <p class="fiche_produit_dispo"><span class="label_fiche">Capacité :</span> <?php echo ' jusqu\'à ' . $produit['capacite'] . ' ' . ' personnes '; ?></p>
                                          
                                            <p class="fiche_produit_dispo"><span class="label_fiche">Ville :</span> <?php echo  $produit['ville']; ?></p>
                                            <p class="fiche_produit_dispo"><span class="label_fiche">Prix :</span> <b> <?php echo $produit['prix'] . ' euros ' ; ?> <b/></p>
					<br/>
                                       </div>
					
                                      
					<?php
				
					echo '<form method="post" action="panier.php">';
			
					echo '<input type="submit" class="form-control btn btn-success" name="ajout_panier" value="Demande de Réservation" />';
					echo '</form>';
				
					?>
					<br/>
					<p style="text-align:center;"><a href="index.php"><span class="glyphicon glyphicon-triangle-left" aria-hidden="true"></span>RETOUR</a></p>
				</div>
			</div>
                </div>
              <div class="col-sm-4" style="display: table-cell;">
                   <div class=" panel panel-primary fiche_produit" >
                        <legend> Accès à la salle :  </legend>
                        <p class="fiche_produit_dispo"> 
                            <span class="label_fiche adresse_carte" style="padding:10px; max-width:85px;"> Adresse :</span>
                            <span><?php echo $produit['adresse'] . ' ' . $produit['cp'] . ' ' . $produit['ville']; ?></span>
                        </p>
                       <div id="map">
					
                       </div>
                   </div>
                  <div class=" panel panel-primary fiche_produit">
                       <legend> Voir les autres disponibilités... </legend>
                      <p class="fiche_produit_dispo"><span class="label_fiche">Salle :</span> <?php echo $produit['titre'] ?></p>
                     
                  </div>
              </div>
            
	  </div> <!-- ROW -->
          <div class="row">
                <div class="fiche_produits_salles_villes panel panel-primary">
                    <legend> Egalement à <?php echo $produit['ville'] ?> ...   </legend>
                    <div class="row">
                        <?php
                            while($autres_salles = $autres_villes->fetch_assoc())
                            {
                                if ($autres_salles['id_salle'] != $produit['id_salle'])
                                {

                        ?>
                        <div class="col-sm-4">
                                <div class=" panel panel-primary">
                                   <div class="panel-body">
                                       <p style="text-align: center;">
                                Salle <?php echo $autres_salles['titre'] . ' ' . ' - ' . ' ' . $autres_salles['categorie'] ?>
                                       </p>
                                           <a href=""><img class="fichepro" src="<?php echo $autres_salles['photo']?>"></a>
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
<?php
include("inc/footer.inc.php");