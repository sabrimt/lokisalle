<?php
require_once("../inc/init.inc.php");

if(!utilisateur_est_connecte_et_est_admin()) 
{
	header("location:".URL."profil.php");
	exit(); // bloque l'exécution du script
}
// unlink();
// $id_membre = "";
$pseudo = "";
$mdp = "";
$nom = "";
$prenom = "";
$email = "";
$civilite = "m";
$statut = "0";
$date_enregistrement = "";
/* SUPPRESSION DUN MEMBRE */
if(isset($_GET['action']) && $_GET['action'] == 'suppression')
{
	execute_requete("DELETE FROM membre WHERE id_membre='$_GET[id]'");
	$_GET['action'] = 'affichage';
	echo '<div style="padding:5px; background_color:darkred; color:white;"> Membre bien supprimé ! </div>';
}

/* MODIFICATION DUN MEMBRE*/
if(isset($_GET['action']) && $_GET['action'] == 'modification')
{
	if(isset($_GET['id']))
	{
		$resultat = execute_requete("SELECT * FROM membre WHERE id_membre='$_GET[id]'"); // dans le cas d'une modif, on récupère en bdd toutes les informations de l'article.
		$membre_actuel = $resultat->fetch_assoc(); // on transforme la ligne de l'objet resultat en tableau array
		extract($membre_actuel);
	}
}


if(isset($_POST['pseudo']) && isset($_POST['mdp']) && isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['email']) && isset($_POST['civilite']) && isset($_POST['statut']))
{
	
	foreach($_POST as $indice => $valeur)
	{
		$_POST[$indice] = htmlentities($valeur, ENT_QUOTES);
	}
	extract($_POST);
	// debug($_POST);

	
	
	// controle sur l'Email (unique en BDD)
	$controle_email = execute_requete("SELECT * FROM membre WHERE email='$email'");
	if($controle_email->num_rows > 0 && isset($_GET['action']) && $_GET['action'] == 'ajout') // s'il y a au moins une ligne dans l'objet $controle_email alors l'email existe déjà en BDD
	{

		$msg .= '<div class="erreur">Erreur: Adresse email: '. $email . ' est déjà enregistrée</div>';
	} elseif(empty($msg) && !empty($pseudo) && !empty($mdp) && !empty($email)) {
			
		
			if(isset($_GET['action']) && $_GET['action'] == 'ajout')
			{
				execute_requete("INSERT INTO membre (pseudo, mdp, nom, prenom, email, civilite, statut, date_enregistrement) VALUES ('$pseudo', '$mdp', '$nom', '$prenom', '$email', '$civilite', '$statut', NOW())");
				
			} elseif (isset($_GET['action']) && $_GET['action'] == 'modification')
			{
				execute_requete("UPDATE membre SET pseudo='$pseudo', mdp='$mdp', prenom='$prenom', email='$email', civilite='$civilite', statut='$statut', date_enregistrement=NOW()  WHERE id_membre='$id_membre'");
			}
			$msg .= '<div class="succes">' . ucfirst($_GET['action']) . ' OK !</div>';
			$_GET['action'] = 'affichage'; // on bascule sur l'affichage du tableau pour voir l'enregsitrement ou la modification qui vient d'être exécutée.			 
		}
	}
	// debug($_GET);
	// debug($_POST);

include("../inc/header.inc.php");
include("../inc/nav.inc.php");
?>

    <div class="container">

      <div class="starter-template">
        <h1><span class="blueviolet glyphicon glyphicon-cog"></span> Gestion Membres</h1> 
		<?php echo $msg;  // variable initialisée dans le fichier init.inc.php //debug($_POST);//debug($_SERVER);  debug($_FILES);?>	
		
      </div>

    
	<div class="row">
		<div class="col-sm-4 col-sm-offset-4" style="text-align: center;">
			<a href="?action=ajout" class="btn btn-primary">Ajouter un membre</a>
			<a href="?action=affichage" class="btn btn-info">Afficher les membres</a>
		<hr />
		</div>
		<?php 
			if(isset($_GET['action']) && ($_GET['action'] == 'ajout' || $_GET['action'] == 'modification'))
			{			
		?> 
		<div class="addmember col-sm-6 col-sm-offset-3">		
			<form method="post" action="" enctype="multipart/form-data"><!-- l'attribut enctype avec la valeur "multipart/form-data" permet le chargement de fichier dans un formulaire -->		
			<legend><?php
				if(isset($_GET['action']) && $_GET['action'] == 'modification')
				{
					echo 'Modifications pour le membre ' . $pseudo;
				}else {
					echo 'Ajout de Membre';
				}
			?></legend>
			  

			  <div class="row">		  
				  <div class="radio col-sm-8">
				  <p><b>Statut</b></p>
					<label for="statut">
					  <input type="radio" name="statut" value="1" <?php if($statut == '1') { echo 'checked'; } ?> >Admin					
					</label>
					<label>
					  <input type="radio" name="statut" value="0" <?php if($statut == '0') { echo 'checked'; } ?> >Victime
					</label>
				  </div>
			  </div>
			  <div class="form-group">
				<label for="categorie">Pseudo</label>
				<input type="text" class="form-control" id="pseudo" placeholder="Pseudo..." name="pseudo" value="<?php if(isset($_POST['pseudo'])) { echo $_POST['pseudo']; } else { echo $pseudo; } ?>" />
			  </div>
			  <div class="form-group">
				<label for="mdp">Mot de passe</label>
				<input type="text" class="form-control" id="mdp" placeholder="mdp..." name="mdp" value="<?php echo $mdp; ?>" />
			  </div>
			  <div class="form-group">
				<label for="nom">Nom</label>
				<input type="text" class="form-control" id="nom" placeholder="Nom..." name="nom" value="<?php if(isset($_POST['nom'])) { echo $_POST['nom']; } else { echo $nom; } ?>" />
			  </div>
			  <div class="form-group">
				<label for="prenom">Prenom</label>
				<input type="text" class="form-control" id="prenom" placeholder="Prenom..." name="prenom" value="<?php if(isset($_POST['prenom'])) { echo $_POST['prenom']; } else { echo $prenom; } ?>" />
			  </div>
			  <div class="form-group">
				<label for="email">Email</label>
				<input type="text" class="form-control" id="email" placeholder="Email..." name="email" value="<?php if(isset($_POST['email'])) { echo $_POST['email']; } else { echo $email; } ?>" />
			  </div>
			  <div class="row">		  
				  <div class="radio col-sm-8">
				  <p><b>Civilité</b></p>
					<label for="civilite">
					  <input type="radio" name="civilite" value="m" <?php if( (isset($_POST['civilite']) && $_POST['civilite'] == "m" ) || $civilite == 'm') { echo 'checked'; } ?> >Homme					
					</label>
					<label>
					  <input type="radio" name="civilite" value="f" <?php if( (isset($_POST['civilite']) && $_POST['civilite'] == "f" ) || $civilite == 'f') { echo 'checked'; } ?> >Femme
					</label>
				  </div>
			  </div>
			  <hr />
			  <input type="submit" class="form-control btn btn-info" id="enregistrer" name="action" value="Enregistrer" />
			</form>
			
		</div>
<?php 
		}	


if(isset($_GET['action']) && $_GET['action'] == 'affichage')
{
	// Création de tableau pour afficher la table
	afficher_table_avec_boucle("SELECT * FROM membre", "id_membre");	
}






echo '</div></div><!-- /.container -->'; // fermeture du row et du container
include("../inc/footer.inc.php");
