<?php
require_once("inc/init.inc.php");

$tab_produit = array();

$contenu_produit = execute_requete("SELECT * FROM produit");
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

	$tab_produit[] = $produit;
       
}

//
//debug($tab_produit);

include("inc/header.inc.php");
include("inc/nav.inc.php");
?>

    <div class="container">

      <div class="starter-template">
        <h1><span class="blueviolet glyphicon glyphicon-home"></span> Lokisalle - Réservation de salle</h1>
		<?php echo $msg; // variable initialisée dans le fichier init.inc.php ?>
      </div>
	  <div class="row">
		 <aside class="col-sm-3" style="background-color: lightskyblue; min-height: 600px">
			
			
		  </aside>
		  <div class="col-sm-9">
		  <?php 
                        // Affichage des différents produits
			foreach($tab_produit AS $index => $val)
			{
				?>
				<div class="col-sm-4">
					<div class=" panel panel-primary">
						<div class="panel-body">
				<?php 
				if(!empty($tab_produit[$index]['photo']))
				{
                                    echo '<p><img class="fichepro" src="' . $tab_produit[$index]['photo'] . '" /></p>';
				}else
                                {
                                    echo '<div class="no-photo"><p>Pas de photo pour cette salle</p></div>';
                                }
				$type_salle="Salle ";
				if($tab_produit[$index]['categorie'] == 'bureau')
				{
					$type_salle = "Bureau ";
				}
                            
				?>
                                                   
							<div class="row">
								<h5 class="col-sm-8 salle_titre"><?php echo $type_salle . $tab_produit[$index]['titre']; ?></h5>
								<h5 class="col-sm-4 salle_prix"><?php echo $tab_produit[$index]['prix']; ?> €</h5>
							</div>
							<p><?php echo substr($tab_produit[$index]['description'], 0, 26); ?>...</p>
							<p class="glyphicon glyphicon-calendar"> <?php echo substr(change_date($tab_produit[$index]['date_arrivee']), 0, 10) . ' au ' . substr(change_date($tab_produit[$index]['date_depart']), 0, 10); ?></p>
							<div class="row">
                                                            <?php echo '<a class="col-sm-4 col-sm-offset-8" href="fiche_produit.php?id=' . $tab_produit[$index]['id_produit'] . '"><span class="glyphicon glyphicon-search"></span> Voir</a>' ?>
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