<?php
require_once("inc/init.inc.php");
if(!utilisateur_est_connecte()) // différent de => je rentre dans cette condition si la fonction me renvoi false
{
	header("location:connexion.php");
	exit();
}
// afficher toutes les informations de l'utilisateur proprement sauf son mot de passe
// mettre en forme la page
// extract($_SESSION['utilisateur']);



include("inc/header.inc.php");
include("inc/nav.inc.php");
?>

    <div class="container">

      <div class="starter-template">
        <h1><span class="blueviolet glyphicon glyphicon-user"></span> Bonjour <?php echo $_SESSION['utilisateur']['prenom']; ?></h1>
		<?php echo $msg; /* debug($_SESSION); */ // variable initialisée dans le fichier init.inc.php ?>		
      </div>
	  <div class="row">
		<div class='col-sm-6 col-sm-offset-3'>
			<div class=" panel panel-primary">
				<div class="panel-heading">Profil</div>
				<div class="panel-body">
					<div class='col-sm-9'>
						<p><span class="infos_profil">Pseudo: </span> <?php  echo $_SESSION['utilisateur']['pseudo']; ?></p>
						<p><span class="infos_profil">Prénom: </span> <?php  echo $_SESSION['utilisateur']['prenom']; ?></p>
						<p><span class="infos_profil">Nom: </span> <?php  echo $_SESSION['utilisateur']['nom']; ?></p>
						<p><span class="infos_profil">Email: </span> <?php  echo $_SESSION['utilisateur']['email']; ?></p>
						<p><span class="infos_profil">Civilité: </span> <?php  echo $_SESSION['utilisateur']['civilite']; ?></p>
						
						<?php 
foreach($_SESSION['utilisateur'] AS $indice => $valeur)
{
	if($indice != 'id_membre' || $indice != 'statut')
	{
		// echo '<p><span class="infos_profil">' . ucfirst($indice) . ': </span> ' . $valeur . '</p>';
	}								
}
						?>
					</div>
					<div class='col-sm-3'>
						<?php if(utilisateur_est_connecte_et_est_admin()) { echo '<p style="text-align: center; color: #428bca; margin-top: 10px;">Vous êtes Administrateur</p>'; } ?>
					</div>
				</div>
			</div>
		</div>
	  </div>
	  
	  

    </div><!-- /.container -->

<?php
include("inc/footer.inc.php");