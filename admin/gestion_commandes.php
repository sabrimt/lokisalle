<?php
require_once("../inc/init.inc.php");

if(!utilisateur_est_connecte_et_est_admin()) 
{
	header("location:".URL."profil.php");
	exit(); // bloque l'exécution du script
}


include("../inc/header.inc.php");
include("../inc/nav.inc.php");
?>

    <div class="container">

      <div class="starter-template">
        <h1><span class="blueviolet glyphicon glyphicon-cog"></span> Gestion Commandes</h1> 
		<?php echo $msg; ?>	
		
      </div>

    
	<div class="row">
		<div class="col-sm-4 col-sm-offset-4" style="text-align: center;">
			<a href="?action=ajout" class="btn btn-primary">Ajouter un article</a>
			<a href="?action=affichage" class="btn btn-info">Afficher les articles</a>
		<hr />
	</div>

	
<?php 
echo '</div></div><!-- /.container -->'; // fermeture du row et du container
include("../inc/footer.inc.php");
