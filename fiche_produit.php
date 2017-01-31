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
                     <span class="blueviolet glyphicon glyphicon-share"></span> Salle <?php echo $produit['titre'] . ' ' . '</span> DU ' . $produit['date_arrivee'] . ' AU ' . $produit['date_depart'] ?>
                  
                     <div class=" panel panel-primary fiche_produit">
                        
				<div class="col-sm-12 fiche-top">
                                    <div class="col-sm-9 fiche_main_pic" > <?php echo '<img class="main_pic" src="' . $produit['photo'] . '" />'; ?>
                                    </div>
                                    <div class="col-sm-3 fiche_other_pics">
                                        <div class="fiche_second_pic">
                                                <?php echo '<img class="img-thumbnail" src="' . $produit['photo_2'] . '" />'; ?>
                                        </div>
                                        <div class="fiche_third_pic">
                                             <?php echo '<img class="img-thumbnail" src="' . $produit['photo_3'] . '" />'; ?>
                                        </div>
                                    </div>
                                </div>
                                <hr/>
				<div class="panel-body">
					<p><span class="label_fiche">Prix:</span> <?php echo $produit['prix']; ?></p>
                                        <p><span class="label_fiche">Capacité:</span> <?php echo $produit['capacite']; ?></p>
                                        <p><span class="label_fiche">Etat:</span> <?php echo $produit['etat']; ?></p>
					<p><span class="label_fiche">Adresse:</span> <?php echo $produit['adresse'] . ' ' . $produit['cp'] . ' ' .  $produit['ville']; ?></p>
					<p><span class="label_fiche">></p>
					
                                        <hr/>
					
					 <p><span class="label_fiche">Description:</span> <?php echo $produit['description']; ?></p>
					
					<hr/>
					<?php
				
					echo '<form method="post" action="panier.php">';
			
					echo '<input type="submit" class="form-control btn btn-success" name="ajout_panier" value="Ajouter au panier" />';
					echo '</form>';
				
					?>
					<br/>
					<p><a href="index.php"><span class="glyphicon glyphicon-triangle-left" aria-hidden="true"></span>RETOUR</a></p>
				</div>
			</div>
		</div>
	  </div>
	  
	  

    </div><!-- /.container -->

<?php
include("inc/footer.inc.php");