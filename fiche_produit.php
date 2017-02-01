<?php
require_once("inc/init.inc.php");

if(!preg_match('#^[0-9]+$#', $_GET['id'])) // on vérifie si l'id ne contient que des chiffres.
{
	header("location:index.php"); // sinon on renvoi sur boutique
	exit();
}

$id_produit = $_GET['id']; // récupération de l'id
$get_produit = execute_requete("SELECT * FROM salle s, produit p WHERE s.id_salle = p.id_salle AND id_produit = $id_produit");
$produit = $get_produit->fetch_assoc();

//debug($produit);


include("inc/header.inc.php");
include("inc/nav.inc.php");
// EXERCICE:
// Afficher sur la page un lien qui permet de revenir à sa sélection sur la page boutique (par exemple si l'utilisateur est sur un pantalon, on le renvoie sur boutique.php avec par défaut la catégorie pantalon affichée)
// Dans les informations produit, afficher le stock ou le message "rupture de stock pour ce produit" si le stock est à 0
?>

    <div class="container">

      <div class="starter-template">
       
			
      </div>
	  
	  <div class="row">
		<div class="col-sm-8">
                    <h1 class="fiche_produit_h1"><span class="glyphicon glyphicon-play" aria-hidden="true"></span> Réservez la salle <b> <?php echo $produit['titre'] ?> </b> </h1>
                  
                     <div class=" panel panel-primary fiche_produit" style="background-color:#F4F7F7">
                        
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
<!--                                       <div style="background-color:pink">-->
                                       <p><span class="label_fiche">Description:</span> <?php echo $produit['description']; ?></p>
                                       <hr/>
                                       <p class="fiche_produit_dispo"><span class="label_fiche"> <b> Disponibilités:</b> </span> </p>
                                       <p><span class="label_fiche"> Entrée </span><?php echo ' <span class="glyphicon glyphicon-arrow-right" aria-hidden="true"></span>  ' . ' ' . ' <b> ' . $produit['date_arrivee'] . ' </b>' . ' '; ?></p>
                                       <p><span class="label_fiche"> Sortie </span><?php echo ' <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>  ' . ' ' . ' <b> ' . $produit['date_depart'] . '</b>'; ?></p>
<!--                                       </div>-->
                                    
                                       
					
                                        
                                        <p class="fiche_produit_dispo"><span class="label_fiche">Capacité:</span> <?php echo $produit['capacite']; ?></p>
					<p class="fiche_produit_dispo"><span class="label_fiche">Adresse:</span> <?php echo $produit['adresse'] . ' ' . $produit['cp'] . ' ' .  $produit['ville']; ?></p>
	
					
                                        <hr/>
					
					 <p class="fiche_produit_dispo" style="font-size:18pt;"><span class="label_fiche" >Prix:</span> <?php echo $produit['prix'] . ' euros ' ; ?></p>
					
					<hr/>
					<?php
				
					echo '<form method="post" action="panier.php">';
			
					echo '<input type="submit" class="form-control btn btn-success" name="ajout_panier" value="Demande de Réservation" />';
					echo '</form>';
				
					?>
					<br/>
					<p><a href="index.php"><span class="glyphicon glyphicon-triangle-left" aria-hidden="true"></span>RETOUR</a></p>
				</div>
			</div>
		</div>
	  </div>
	  
	  

    </div><!-- /.container -->

    <script>
        
        </script>
<?php
include("inc/footer.inc.php");