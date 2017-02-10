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
        $produit["id_salle"] = $salle['id_salle'];

	$tab_produit[] = $produit;
       
}

//
//debug($tab_produit);

include("inc/header.inc.php");
include("inc/nav.inc.php");
?>

    <div class="container">
          <h1 class="hometitre">cAVA sALLE - Réservation de salles pour professionnels </h1>
          <hr/>
            
<!--      <div class="starter-template">
            <?php echo $msg; // variable initialisée dans le fichier init.inc.php ?>
  </div>-->

      <div class="row">
            <div class="col-sm-3 aside" ">
                <div class="recherche" style="background-color: lightskyblue; min-height: 600px">
                    <p> <span class="glyphicon glyphicon-search" aria-hidden="true"></span> ici recherche de salles </p>
                </div>
                <div class="recherche" style="background-color: pink; min-height: 600px">
                    <p><span class="glyphicon glyphicon-eur" aria-hidden="true"></span> ici pub pour gagner de l'argent</p>
                </div>

            </div>
             <div class="col-sm-9">
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
              <?php 
                    // Affichage des différents produits
                    foreach($tab_produit AS $index => $val)
                    {
                        $reftitre = strtolower(substr($tab_produit[$index]['titre'], 0, 3));
                            ?>
                <div class="col-sm-4">
                    <div class=" panel panel-primary">
                        <div class="panel-body">
                            <?php 
                                if(!empty($tab_produit[$index]['photo']))
                                {
                                    echo '<p><a href="fiche_produit.php?id=' . $tab_produit[$index]['id_produit'] . '"><img class="fichepro" src="' . $tab_produit[$index]['photo'] . '" /></a></p>';
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
                                <?php echo '<a href="fiche_produit.php?id=' . $tab_produit[$index]['id_produit'] . '"><button type="button" class="btn btn-info index-bouton-voir"><span class="glyphicon glyphicon-search"></span> Voir</button></a>' ?>
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