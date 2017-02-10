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
$photo_exists_2 = FALSE;
$photo_exists_3 = FALSE;

$photo_action = 'Ajouter '; // variables qui permettent de modifier le label pour la selection de photo s'il y'en a déjà
$photo_action_2 = 'Ajouter ';// Si pas de photo j'affiche 'ajouter photo'
$photo_action_3 = 'Ajouter ';
        
$cols = ['photo', 'photo_2', 'photo_3'];

/* SUPPRESSION */
if(isset($_GET['action']) && $_GET['action'] == 'suppression')
{
	if(isset($_GET['id']))
	{
		$salle_a_supprimer = execute_requete("SELECT photo FROM salle WHERE id_salle='$_GET[id]'");
		$photo_a_supprimer = $salle_a_supprimer->fetch_assoc();
                foreach($cols as $col)
                {
                    if(!empty($photo_a_supprimer[$col]) && file_exists(RACINE_SERVER . URL . $photo_a_supprimer[$col]))
                    {
                        unlink(RACINE_SERVER . URL . $photo_a_supprimer[$col]); // pour supprimer la photo de cet salle.
                    }
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

        $action = 'photo_action';
        $exists = 'photo_exists';
        $i = 1;
        foreach ($cols as $col) {
            if(!empty($$col)) // si la photo existe
            {
                if($i > 1){
                    $n = '_' . $i;
                }else{
                    $n = "";
                }

                ${$action . $n} = 'Changer ';// j'affiche 'changer photo'
                ${$exists . $n} = TRUE;// Je donne l'information qu'il existe déjà une photo pour adapter l'affichage

                $i++;
            } 
        }
}

// controles sur la validité des saisies du formulaire

if(isset($_POST['titre']) && isset($_POST['description']) && isset($_POST['pays']) && isset($_POST['ville']) && isset($_POST['adresse']) && isset($_POST['cp']) && isset($_POST['capacite']) && isset($_POST['categorie']))
{
//	debug($_POST);
	foreach($_POST as $indice => $valeur)
	{
		$_POST[$indice] = htmlentities($valeur, ENT_QUOTES);
	}
	extract($_POST);

	// controle sur le titre(unique en BDD)
	$controle_titre = execute_requete("SELECT * FROM salle WHERE titre='$titre'");
	if($controle_titre->num_rows > 0 && isset($_GET['action']) && $_GET['action'] == 'ajout') // s'il y a au moins une ligne dans l'objet $controle_titre alors le titre existe déjà en BDD
	{
		$msg .= '<div class="erreur">Erreur: La référence: '. $titre . ' est indisponible</div>';
	}else {
	// VERIFICATION PHOTOS
		$photo_bdd = ""; // pour éviter une erreur undefined si l'utilisateur ne charge pas de photo.
		$photo_bdd_2 = "";
		$photo_bdd_3 = "";
                
		if(isset($_GET['action']) && $_GET['action'] == 'modification')
		{
                    $i=1;
                    foreach ($cols as $col)
                    {
                        if($i > 1){
                            $n = '_' . $i;
                        }else{
                            $n = "";
                        }
                        $i++;
			if(isset($_POST["photo_actuelle$n"]))
                        {
                            ${'photo_bdd' . $n} = $_POST['photo_actuelle' . $n]; // dans le cas d'une modif, on place le src de l'ancienne photo dans $photo_bdd avant de tester si une nouvelle photo a été postée qui écrasera la valeur de $photo_bdd
                        }
                        // vérif sur photo
                        if(!empty($_FILES['photo'.$n]['name'])) // si une photo a été postée
                        {
                                $photon = 'photo'.$n;
                                if(verif_extension_photo($photon))
                                {

                                        // il faut vérifier le nom de la photo car si une photo possède le même nom, cela pourrait l'écraser.
                                        // on concatène donc la référence qui est unique avec le nom de la photo.
                                        $nom_photo = $_FILES[$photon]['name'];
                                        $photo_tmp = 'img/' . $nom_photo; // $photo_bdd représente le src que nous allons enregistrer en BDD
                                        $chemin_dossier = RACINE_SERVER . URL . 'img/' . $nom_photo; // représente le chemin absolu pour enregistrer la photo.
                                        copy($_FILES[$photon]['tmp_name'], $chemin_dossier); // copy() permet de copier un fichier d'un endroit vers un autre. ici tmp_name est l'emplacement temporaire ou la photo est conservée après l'avoir chargée dans un formulaire.
                                        
                                        ${'photo_bdd'.$n} = accorder_image($photo_tmp);


                                }else { // l'extension de la photo n'est pas valide
                                        $msg .= '<div class="erreur">L\'extension de la photo n\'est pas valide<br />Extensions acceptées: jpg / jpeg / png /gif</div>';
                                }
                        }
                    }
		}
                // Vérification photos END
			
		if(empty($msg) && !empty($titre) && !empty($categorie) ) // s'il n'y a pas d'erreur au préalable, alors on lance l'enregistrement en BDD et champs requis remplis
		{
			
			if(isset($_GET['action']) && $_GET['action'] == 'ajout')
			{
				execute_requete("INSERT INTO salle (titre, description, pays, ville, adresse, cp, capacite, categorie, photo, photo_2, photo_3) VALUES ('$titre', '$description', '$pays', '$ville', '$adresse', '$cp', '$capacite', '$categorie', '$photo_bdd', '$photo_bdd_2', '$photo_bdd_3')");

					//echo '<h1>Test ok</h1>';
			}elseif (isset($_GET['action']) && $_GET['action'] == 'modification' )
			{
				execute_requete("UPDATE salle SET titre='$titre', description='$description', pays='$pays', ville='$ville', adresse='$adresse', cp='$cp', capacite='$capacite', categorie='$categorie', photo='$photo_bdd', photo_2='$photo_bdd_2', photo_3='$photo_bdd_3' WHERE id_salle='$id_salle'");
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
							<option value="Reunion" <?php if( (isset($_POST['categorie']) && $_POST['categorie'] == "Réunion" ) || ($categorie == "Réunion") ) echo 'selected'; ?> >Réunion</option>
							<option value="Bureau" <?php if( (isset($_POST['categorie']) && $_POST['categorie'] == "Bureau" ) || ($categorie == "Bureau") ) echo 'selected'; ?> >Bureau</option>
                                                        <option value="Formation" <?php if( (isset($_POST['categorie']) && $_POST['categorie'] == "Formation" ) || ($categorie == "Formation") ) echo 'selected'; ?> >Formation</option>
                                                        <option value="Conference" <?php if( (isset($_POST['categorie']) && $_POST['categorie'] == "Conférence" ) || ($categorie == "Conférence") ) echo 'selected'; ?> >Conférence</option>
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
                                <?php
                                $i=1;
                                foreach ($cols as $col)
                                {
                                    if($i > 1){
                                        $n = '_' . $i;
                                    }else{
                                        $n = "";
                                    }
                                    $i++;
                                ?>
                                <div class="form-group col-sm-4">

                                    <input type="file" class="input-file form-control" id="photo<?= $n ?>" name="photo<?= $n ?>"/>
                                    <label for="photo<?= $n ?>" id="photo_lab<?= $n ?>" class="btn btn-label btn-info"><?php echo ${'photo_action' . $n} ?>photo...<span class="glyphicon glyphicon-save"></span></label>
                                <?php
                                if(${'photo_exists' . $n})
                                {?>
                                    <div class="photo-actuelle col-sm-10 col-sm-offset-1">
                                        <label for="photo_actuelle<?= $n ?>">Photo actuelle</label><br/>
                                        <img src="<?php echo URL . ${'photo' . $n} ?>" alt="<?php echo ${'photo' . $n} ?>" width="100%" />
                                        <input type="hidden" name="photo_actuelle<?= $n ?>" value="<?php echo ${'photo' . $n} ?>"/>
                                    </div>
                                <?php
                                }?>
                                </div>
                                <?php } ?><!-- fermeture foreach-->


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