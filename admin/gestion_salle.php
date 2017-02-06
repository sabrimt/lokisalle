<?php
require_once("../inc/init.inc.php");

if(!utilisateur_est_connecte_et_est_admin()) 
{
	header("location:".URL."profil.php");
	exit(); 
}

// $id_salle = "";
$titre = "";
$description = "";
$photo = "";
$photo_2 = "";
$photo_3 = "";
$pays = "";
$ville = "";
$adresse = "";
$cp = "";
$capacite = "";
$categorie = "";

/* VARIABLES INITIALISEES POUR LES VERIFICATIONS D'AFFICHAGE DES PHOTOS ACTUELLES */
        $photo_exists = FALSE; // variables qui vérifient l'existance de photo en BDD et permettent ou non l'affichage de l'élément 'photo actuelle'
        $photo2_exists = FALSE;
        $photo3_exists = FALSE;
        
        $photo_action = 'Ajouter '; // variables qui permettent de modifier le label pour la selection de photo s'il y'en a déjà
        $photo2_action = 'Ajouter ';// Si pas de photo j'affiche 'ajouter photo'
        $photo3_action = 'Ajouter ';
        
        
/* SUPPRESSION */
if(isset($_GET['action']) && $_GET['action'] == 'suppression')
{
	if(isset($_GET['id']))
	{
		$salle_a_supprimer = execute_requete("SELECT photo FROM salle WHERE id_salle='$_GET[id]'");
		$photo_a_supprimer = $salle_a_supprimer->fetch_assoc();
		if(!empty($photo_a_supprimer['photo']) && file_exists(RACINE_SERVER . URL . $photo_a_supprimer['photo']))
                {
                    unlink(RACINE_SERVER . URL . $photo_a_supprimer['photo']); // pour supprimer la photo de cet salle.
                }elseif(!empty($photo_a_supprimer['photo_2']) && file_exists(RACINE_SERVER . URL . $photo_a_supprimer['photo_2']))
                {
                    unlink(RACINE_SERVER . URL . $photo_a_supprimer['photo_2']);
                }elseif(!empty($photo_a_supprimer['photo_3']) && file_exists(RACINE_SERVER . URL . $photo_a_supprimer['photo_3']))
                {
                    unlink(RACINE_SERVER . URL . $photo_a_supprimer['photo_3']);
                }
		
		execute_requete("DELETE FROM salle WHERE id_salle='$_GET[id]'");
	}	
	$_GET['action'] = 'affichage';
}

/* MODIFICATION */

$disp_photo = "";// variable qui nous sert à gérer la disposition du champ photo et la photo précedente à l'affichage du form de modification

if(isset($_GET['action']) && $_GET['action'] == 'modification')
{
	if(isset($_GET['id']))
	{
		$resultat = execute_requete("SELECT * FROM salle WHERE id_salle='$_GET[id]'"); // dans le cas d'une modif, on récupère en bdd toutes les informations de l'article.
		$salle_actuelle = $resultat->fetch_assoc(); // on transforme la ligne de l'objet resultat en tableau array
		extract($salle_actuelle);
	}
        
        /* VERIFICATIONS POUR AFFICHAGE DES PHOTOS ACTUELLES */
        
        if(!empty($photo)) // si la photo exite
        {
            $photo_action = 'Changer ';// j'affiche 'changer photo'
            $photo_exists = TRUE;// Je donne l'information qu'il existe déjà une photo pour adapter l'affichage
        }
        
        if(!empty($photo_2))
        {
            $photo2_action = 'Changer ';
            $photo2_exists = TRUE;
        }
        
        if(!empty($photo_3))
        {
            $photo3_action = 'Changer ';
            $photo3_exists = TRUE;
        }
}

// controles sur la validité des saisies du formulaire

if(isset($_POST['titre']) && isset($_POST['description']) && isset($_POST['pays']) && isset($_POST['ville']) && isset($_POST['adresse']) && isset($_POST['cp']) && isset($_POST['capacite']) && isset($_POST['categorie']))
{
	debug($_POST);
	/*foreach($_POST as $indice => $valeur)
	{
		$_POST[$indice] = htmlentities($valeur, ENT_QUOTES);
	}*/
	extract($_POST);

	// controle sur le titre(unique en BDD)
	$controle_titre = execute_requete("SELECT * FROM salle WHERE titre='$titre'");
	if($controle_titre->num_rows > 0 && isset($_GET['action']) && $_GET['action'] == 'ajout') // s'il y a au moins une ligne dans l'objet $controle_titre alors le titre existe déjà en BDD
	{
		$msg .= '<div class="erreur">Erreur: La référence: '. $titre . ' est indisponible</div>';
	}else {
	// VERIFICATION PHOTOS	
		$photo_bdd = ""; // pour éviter une erreur undefined si l'utilisateur ne charge pas de photo.
		$photo_2_bdd = "";
		$photo_3_bdd = "";
		if(isset($_GET['action']) && $_GET['action'] == 'modification')
		{
			$photo_bdd = $_POST['photo_actuelle']; // dans le cas d'une modif, on place le src de l'ancienne photo dans $photo_bdd avant de tester si une nouvelle photo a été postée qui écrasera la valeur de $photo_bdd
			$photo_2_bdd = $_POST['photo_2_actuelle'];
			$photo_3_bdd = $_POST['photo_3_actuelle'];
                        
		}
                // vérif sur photo
		if(!empty($_FILES['photo']['name'])) // si une photo a été postée
		{
                    
			if(verif_extension_photo('photo'))
			{

				// il faut vérifier le nom de la photo car si une photo possède le même nom, cela pourrait l'écraser.
				// on concatène donc la référence qui est unique avec le nom de la photo.
				$nom_photo = $_FILES['photo']['name'];
				$photo_bdd = 'img/' . $nom_photo; // $photo_bdd représente le src que nous allons enregistrer en BDD
				$chemin_dossier = RACINE_SERVER . URL . 'img/' . $nom_photo; // représente le chemin absolu pour enregistrer la photo.
				copy($_FILES['photo']['tmp_name'], $chemin_dossier); // copy() permet de copier un fichier d'un endroit vers un autre. ici tmp_name est l'emplacement temporaire ou la photo est conservée après l'avoir chargée dans un formulaire.
				
				
			}else { // l'extension de la photo n'est pas valide
				$msg .= '<div class="erreur">L\'extension de la photo n\'est pas valide<br />Extensions acceptées: jpg / jpeg / png /gif</div>';
			}
		}
                // vérif sur photo_2
                if(!empty($_FILES['photo_2']['name'])) // si une photo a été postée
		{
                    
			if(verif_extension_photo('photo_2'))
			{

				// il faut vérifier le nom de la photo car si une photo possède le même nom, cela pourrait l'écraser.
				// on concatène donc la référence qui est unique avec le nom de la photo.
				$nom_2_photo = $_FILES['photo_2']['name'];
				$photo_2_bdd = 'img/' . $nom_2_photo; // $photo_bdd représente le src que nous allons enregistrer en BDD
				$chemin_dossier = RACINE_SERVER . URL . 'img/' . $nom_2_photo; // représente le chemin absolu pour enregistrer la photo.
				copy($_FILES['photo_2']['tmp_name'], $chemin_dossier); // copy() permet de copier un fichier d'un endroit vers un autre. ici tmp_name est l'emplacement temporaire ou la photo est conservée après l'avoir chargée dans un formulaire.
				
				
			}else { // l'extension de la photo n'est pas valide
				$msg .= '<div class="erreur">L\'extension de la photo n\'est pas valide<br />Extensions acceptées: jpg / jpeg / png /gif</div>';
			}
		}
                // vérif sur photo_3
                if(!empty($_FILES['photo_3']['name'])) // si une photo a été postée
		{
                    
			if(verif_extension_photo('photo_3'))
			{

				// il faut vérifier le nom de la photo car si une photo possède le même nom, cela pourrait l'écraser.
				// on concatène donc la référence qui est unique avec le nom de la photo.
				$nom_3_photo = $_FILES['photo_3']['name'];
				$photo_3_bdd = 'img/' . $nom_3_photo; // $photo_bdd représente le src que nous allons enregistrer en BDD
				$chemin_dossier = RACINE_SERVER . URL . 'img/' . $nom_3_photo; // représente le chemin absolu pour enregistrer la photo.
				copy($_FILES['photo_3']['tmp_name'], $chemin_dossier); // copy() permet de copier un fichier d'un endroit vers un autre. ici tmp_name est l'emplacement temporaire ou la photo est conservée après l'avoir chargée dans un formulaire.
				
				
			}else { // l'extension de la photo n'est pas valide
				$msg .= '<div class="erreur">L\'extension de la photo n\'est pas valide<br />Extensions acceptées: jpg / jpeg / png /gif</div>';
			}
		}
            // Vérification photos END
			
		if(empty($msg) && !empty($titre) && !empty($categorie) ) // s'il n'y a pas d'erreur au préalable, alors on lance l'enregistrement en BDD et champs requis remplis
		{
			
			if(isset($_GET['action']) && $_GET['action'] == 'ajout')
			{
				execute_requete("INSERT INTO salle (titre, description, pays, ville, adresse, cp, capacite, categorie, photo, photo_2, photo_3) VALUES ('$titre', '$description', '$pays', '$ville', '$adresse', '$cp', '$capacite', '$categorie', '$photo_bdd', '$photo_2_bdd', '$photo_3_bdd')");

					//echo '<h1>Test ok</h1>';
			}elseif (isset($_GET['action']) && $_GET['action'] == 'modification' )
			{
				execute_requete("UPDATE salle SET titre='$titre', description='$description', pays='$pays', ville='$ville', adresse='$adresse', cp='$cp', capacite='$capacite', categorie='$categorie', photo='$photo_bdd', photo_2='$photo_2_bdd', photo_3='$photo_3_bdd' WHERE id_salle='$id_salle'");
			}
			$msg .= '<div class="succes">' . ucfirst($_GET['action']) . ' OK !</div>';
			$_GET['action'] = 'affichage'; // on bascule sur l'affichage du tableau pour voir l'enregsitrement ou la modification qui vient d'être exécutée.			 
		}
	}
	
}



include("../inc/header.inc.php");
include("../inc/nav.inc.php");
?>

    
    <div class="container">
      <div class="starter-template">
        <h1><span class="blueviolet glyphicon glyphicon-cog"></span> Gestion des Salles</h1>
		<?php echo $msg; ?>	
		
      </div>

    
		<div class="row">
			<div class="col-sm-4 col-sm-offset-4" style="text-align: center;">
				<a href="?action=ajout" class="btn btn-primary">Ajouter une Salle</a>
				<a href="?action=affichage" class="btn btn-info">Afficher les Salles</a>
                                <hr />
			</div>
		</div>
			<?php 
				if(isset($_GET['action']) && ($_GET['action'] == 'ajout' || $_GET['action'] == 'modification'))
				{
			?>
                <div class="row">
                    <div class="col-sm-10 col-sm-offset-1">
			<form method="post" action="" enctype="multipart/form-data"> 
                            <legend><?php
                            if(isset($_GET['action']) && $_GET['action'] == 'modification')
                            {
                                    echo 'Modification de la salle ' . $titre;
                            }else {
                                    echo 'Ajout de Salle';
                            }
                            ?></legend>
                            <div class="row">
				<div class="col-sm-6">
					<div class="form-group">

						<label for="titre">Titre</label>
						<input type="text" class="form-control" id="titre" placeholder="Titre..." name="titre" value="<?php if(isset($_POST['titre'])){echo $_POST['titre']; } else { echo $titre; } ?>" />


					</div>
					<div class="form-group">
						<label for="categorie">Catégorie</label>
						<select name="categorie" id="categorie" class="form-control" >
							<option style="text-align: center;" selected disabled >--- Selectionnez une catégorie ---</option>
							<option value="reunion" <?php if( (isset($_POST['categorie']) && $_POST['categorie'] == "reunion" ) || ($categorie == "reunion") ) echo 'selected'; ?> >Réunion</option>
							<option value="bureau" <?php if( (isset($_POST['categorie']) && $_POST['categorie'] == "bureau" ) || ($categorie == "bureau") ) echo 'selected'; ?> >Bureau</option>
                                                        <option value="formation" <?php if( (isset($_POST['categorie']) && $_POST['categorie'] == "formation" ) || ($categorie == "formation") ) echo 'selected'; ?> >Formation</option>
						</select>
					</div>

					<div class="form-group">
						<label for="capacite">Capacité</label>
						<select name="capacite" id="capacite" class="form-control" >
							<option selected disabled>--- Selectionnez une capacité ---</option>
							<?php 

							for ($cap = 1; $cap <= 100; $cap++)
							{
								echo '<option'; if( $capacite == $cap || (isset($_POST['capacite']) && $_POST['capacite'] == $cap ) ) { echo ' selected'; } echo '>' . $cap . '</option>';
							}
							?>
                                                </select>
					
					</div>

					<div class="form-group">
						<label for="description">Description</label>
						<textarea class="form-control" id="description" placeholder="Description..." name="description"><?php if(isset($_POST['description'])){ echo $_POST['description']; } else { echo $description; } ?></textarea>
					</div>
				</div><!-- col-sm-6 -->

				<div class="col-sm-6">

					<div class="form-group">
						<label for="pays">Pays</label>
						<input type="text" class="form-control" id="pays" placeholder="Pays..." name="pays" value="<?php if(isset($_POST['pays'])){ echo $_POST['pays']; } else { echo  $pays; } ?>" />
					</div>
				  
					<div class="form-group">
						<label for="ville">Ville</label>
						<input type="text" class="form-control" id="ville" placeholder="Ville..." name="ville" value="<?php if(isset($_POST['ville'])){ echo $_POST['ville']; } else { echo $ville; } ?>" />
					</div>

					<div class="form-group">
						<label for="cp">Code Postal</label>
						<input type="text" class="form-control" id="cp" placeholder="Code Postal..." name="cp"  value="<?php if(isset($_POST['cp'])){ echo $_POST['cp']; } else { echo $cp; } ?>" maxlength="5" pattern="[0-9]{5}"/>
					</div>

					<div class="form-group">
						<label for="adresse">Adresse</label>
						<textarea class="form-control" id="adresse" placeholder="Adresse..." name="adresse"><?php if(isset($_POST['adresse'])){ echo $_POST['adresse']; } else { echo $adresse; } ?></textarea>
					</div>
				  
		  		</div>
                            </div><!-- row -->
                            
                            <div class="row bloc-photos ">
                                <legend>Photos de la salle</legend>
                                <div class="form-group col-sm-4">

                                    <input type="file" class="input-file form-control" id="photo" name="photo"/>
                                    <label for="photo" id="photo1" class="btn btn-label btn-info"><?php echo $photo_action ?>photo...<span class="glyphicon glyphicon-save"></span></label>
                                <?php
                                if($photo_exists)
                                {?>
                                    <div class="photo-actuelle col-sm-10 col-sm-offset-1">
                                        <label for="photo_actuelle">Photo actuelle</label><br/>
                                        <img src="<?php echo URL . $photo ?>" alt="<?php echo $photo ?>" width="100%" />
                                        <input type="hidden" name="photo_actuelle" value="<?php echo $photo ?>" />
                                    </div>
                                <?php
                                }?>

                                </div>

                                <div class="form-group col-sm-4">

                                    <input type="file" class="input-file form-control" id="photo_2" name="photo_2"/>
                                    <label for="photo_2" id="photo2" class="btn btn-label btn-info"><?php echo $photo2_action ?>photo...<span class="glyphicon glyphicon-save"></span></label>

                                <?php
                                if($photo2_exists)
                                {?>
                                    <div class="photo-actuelle col-sm-10 col-sm-offset-1">
                                        <label for="photo_2_actuelle">Photo actuelle</label><br/>
                                        <img src="<?php echo URL . $photo_2 ?>" alt="<?php echo $photo_2 ?>" width="100%" />
                                        <input type="hidden" name="photo_2_actuelle" value="<?php echo $photo_2 ?>" />
                                    </div>
                                <?php
                                }?>

                                </div>
                                <div class="form-group col-sm-4">

                                    <input type="file" class="input-file form-control" id="photo_3" name="photo_3"/>
                                    <label for="photo_3" id="photo3" class="btn btn-label btn-info"><?php echo $photo3_action ?>photo...<span class="glyphicon glyphicon-save"></span></label>

                                <?php
                                if($photo3_exists)
                                {?>
                                    <div class="photo-actuelle col-sm-10 col-sm-offset-1">
                                        <label for="photo_3_actuelle">Photo actuelle</label><br/>
                                        <img src="<?php echo URL . $photo_3 ?>" alt="<?php echo $photo_3 ?>" width="100%" />
                                        <input type="hidden" name="photo_3_actuelle" value="<?php echo $photo_3 ?>" />
                                    </div>
                                <?php 
                                } ?>
                                </div>
                            </div><!-- row bloc_photos -->
                            <div class="row">
                                <div class="col-sm-6 col-sm-offset-3">

                                        <input type="submit" class="form-control btn btn-primary" id="enregistrer" name="enregistrer" value="Enregistrer Salle" /><hr />

                                </div>
                            </div>
			</form>
<?php 	}	

if(isset($_GET['action']) && $_GET['action'] == 'affichage')
{

	// Création de tableau pour afficher la table
	afficher_table_avec_boucle("SELECT * FROM salle", "id_salle");
	
}
?>
                    </div><!-- fermeture container dic col-sm-10 -->
                </div><!-- fermetutre row principale -->
    </div><!-- fermeture container -->
<?php
include("../inc/footer.inc.php");