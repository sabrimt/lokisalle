<?php
require_once("../inc/init.inc.php");

if(!utilisateur_est_connecte_et_est_admin()) 
{
	header("location:".URL."profil.php");
	exit(); // bloque l'exécution du script
}
$date_arrivee="";
$date_depart="";
$prix="";
$etat="";

$id_salle ="";
/* SUPPRESSION */
if(isset($_GET['action']) && $_GET['action'] == 'suppression')
{
	if(isset($_GET['id']))
	{
		$produit_a_supprimer = execute_requete("SELECT id_produit FROM produit WHERE id_produit='$_GET[id]'");
		
		execute_requete("DELETE FROM produit WHERE id_produit='$_GET[id]'");
	}	
	$_GET['action'] = 'affichage';
}

/* MODIFICATION */
if(isset($_GET['action']) && $_GET['action'] == 'modification')
{
	if(isset($_GET['id']))
	{
		$resultat = execute_requete("SELECT * FROM produit WHERE id_produit='$_GET[id]'"); // dans le cas d'une modif, on récupère en bdd toutes les informations de l'article.
		$produit_actuel = $resultat->fetch_assoc(); // on transforme la ligne de l'objet resultat en tableau array
		extract($produit_actuel);
	}
}

/* AJOUT DE PRODUIT MAGGLE */
if(isset($_POST['date_arrivee']) && isset($_POST['date_depart']) && isset($_POST['id_salle']) && isset($_POST['prix']) && isset($_POST['etat']))
{
	foreach($_POST as $indice => $valeur)
	{
		$_POST[$indice] = htmlentities($valeur, ENT_QUOTES);
	}
	extract($_POST);
	// debug($_POST);

	// Contrôle de la date

	$dates = execute_requete("SELECT date_arrivee, date_depart FROM produit WHERE id_salle='$id_salle'");
	while ($controle_date = $dates->fetch_assoc())
	{
		if( (($date_arrivee >= $controle_date['date_arrivee']) && ($date_arrivee <= $controle_date['date_depart']))  ||  (($date_depart >= $controle_date['date_arrivee']) && ($date_depart <= $controle_date['date_depart']))  ||  (($date_arrivee <= $controle_date['date_arrivee']) && ($date_depart >= $controle_date['date_depart'])))
		{// Afficher erreur si les dates saisies ne sont pas libres
			$msg .= '<div class="erreur">Cette salle n\'est pas libre pour les dates que vous avez choisi !</div>';
		}
	}


	if(strlen($date_arrivee) != 16 || strlen($date_depart) != 16 || ($date_arrivee >= $date_depart))
	{// Afficher erreur si les dates saisies sont complètes et qu'elles soient saisies dans le bon ordre
		$msg .= '<div class="erreur">Veuillez entrer une date valide ou assurez vous que la date d\'arrivée soit bien antérieure à celle de départ!</div>';
	} elseif ($date_arrivee < date("Y-m-d H:i:s"))
	{// Afficher erreur si les dates saisies sont antérieures à aujourd'hui
		$msg .= '<div class="erreur">Veuillez entrer une date valide, ces dates sont déjà passées !</div>';
	}
	
	if(empty($msg))
	{
		if(isset($_GET['action']) && $_GET['action'] == 'ajout')
		{
			execute_requete("INSERT INTO produit(id_salle, date_arrivee, date_depart, prix, etat) VALUES ('$id_salle', '$date_arrivee', '$date_depart', '$prix', '$etat')");
		} elseif (isset($_GET['action']) && $_GET['action'] == 'modification')
		{
			execute_requete("UPDATE produit SET id_salle='$id_salle', date_arrivee='$date_arrivee', date_depart='$date_depart', prix='$prix', etat='$etat' WHERE id_produit='$id_produit'");
		}
		$msg .= '<div class="succes">' . ucfirst($_GET['action']) . ' OK !</div>';
		$_GET['action'] = 'affichage'; // on bascule sur l'affichage du tableau pour voir l'enregsitrement ou la modification qui vient d'être exécutée.			 
	}
}

// Boucle pour selection de salle ds le formulaire 
$req = execute_requete("SELECT * FROM salle");
$options = "";
while ($salle = $req->fetch_assoc())
{
	$selected = "";

	if($id_salle == $salle['id_salle'])
	{
		$selected = ' selected ';
	}
	
	$options .= '<option value="'. $salle['id_salle'] . '"' . $selected . '>' .  $salle['id_salle'] . ' - ' . $salle['titre'] . ' - ' . $salle['ville'] . '</option>';
}

include("../inc/header.inc.php");
include("../inc/nav.inc.php");
?>


<div class="container">

      <div class="starter-template">
        <h1><span class="blueviolet glyphicon glyphicon-cog"></span> Gestion des Produits</h1>
		<?php echo $msg; ?>
		
      </div>
     	<div class="row">
			<div class="col-sm-8 col-sm-offset-2" style="text-align: center;">
				<a href="?action=ajout" class="btn btn-primary">Ajouter un Produit</a>
				<a href="?action=affichage" class="btn btn-info">Afficher les Produits</a>
				<hr />
			</div>
		</div>

		<?php 
			if(isset($_GET['action']) && ($_GET['action'] == 'ajout' || $_GET['action'] == 'modification'))
			{			
		?> 
		<div class="col-sm-10 col-sm-offset-1">
			<form method="post" action="" enctype="multipart/form-data"> 
				<legend><?php
					if(isset($_GET['action']) && $_GET['action'] == 'modification')
					{
						echo 'Modifications pour le produit ' . $id_produit;
					}else {
						echo 'Ajout de produit';
					}
				?></legend>
				<div class="row">
					<div class='col-sm-6'>

						<label for="to">Date d'arrivée :</label>
						<input type="text" id="to" name="date_arrivee" class="form-control datepicker" placeholder="choisir date de sortie..." value="<?php if(isset($_POST['date_arrivee'])) { echo $_POST['date_arrivee']; } else { echo $date_arrivee; } ?>"><br />
			   
			            <label for="from">Date de départ :</label>
						<input type="text" id="from" name="date_depart" class="form-control olive datepicker" placeholder="choisir date d'entrée..." value="<?php if(isset($_POST['date_depart'])) { echo $_POST['date_depart']; } else { echo $date_depart; } ?>">
				
		      		</div>

					<div class="col-sm-6">
						<div class="form-group">
							<label for="salle">Salle</label>
							<select name="id_salle" id="salle" class="form-control" required >
							
								<option selected disabled>--- Selectionnez une salle ---</option>
								<?php echo $options; ?>
								
							</select>
					 	</div>
						<div class="form-group">
							<label for="description">Tarif (€)</label>
							<input type="number" class="form-control nn" id="prix" placeholder="Tarif..." name="prix" value="<?php if(isset($_POST['prix'])) { echo $_POST['prix']; } else { echo $prix; } ?>" required />
						</div>
						<div class="form-group">
							<label for="description">Etat</label>
							<select name="etat" id="etat" class="form-control" >
								<option <?php if($etat == "Libre") echo 'selected'; ?> >Libre</option>
								<option <?php if($etat == "Réservée") echo 'selected'; ?> >Réservée</option>
							</select>
						</div>
					</div>
				</div>

				<div class="col-sm-6 col-sm-offset-3">
					<hr/>
					<input type="submit" class="form-control btn btn-primary" id="enregistrer" name="enregistrer" value="Enregistrer Produit" /><hr />
				</div>
			</form>
						
					
		<?php }

				
		if(isset($_GET['action']) && $_GET['action'] == 'affichage')
		{
			afficher_table_avec_boucle("SELECT p.id_produit, p.date_arrivee, p.date_depart, s.description, s.titre, s.photo, s.photo_2, s.photo_3, s.ville, p.prix, p.etat FROM produit p JOIN salle s ON p.id_salle = s.id_salle", 'id_produit');
			
		}


include("../inc/footer.inc.php");