<?php
// fonctions divers
function active($url)
{
	if($_SERVER['PHP_SELF'] == $url)
	{
		return ' class="active" ';
	}
	// $_SERVER['PHP_SELF'] nous renvoie l'url du script en cours.
	// pour l'utilisation, voir sur nav.inc.php
}

// change le nom du lien de nav en nom de la page sur l'en-tête du menu déroulant
function change_lien_menu()
{
	$result = "";
	if($_SERVER['PHP_SELF'] == URL.'admin/gestion_salle.php')
    {
        $result = 'Gestion Salles';

    }
      elseif ($_SERVER['PHP_SELF'] == URL.'admin/gestion_produits.php') 
    {
        $result = 'Gestion Produits';

    }
      elseif ($_SERVER['PHP_SELF'] == URL.'admin/gestion_membres.php') 
    {
        $result = 'Gestion Membres';

    }
    elseif ($_SERVER['PHP_SELF'] == URL.'admin/gestion_commandes.php') 
    {
        $result = 'Gestion Commandes';
    }
    else 
    {
    	$result = 'Gestion Admin';
    }

    return $result;
	// $_SERVER['PHP_SELF'] nous renvoie l'url du script en cours.
	// pour l'utilisation, voir sur nav.inc.php
}

function execute_requete($req)
{
	global $mysqli; // on récupère la variable représentant la connexion BDD
	$resultat = $mysqli->query($req); // on exécute la requete reçue en argument.
	if(!$resultat) // ou if($resultat == false) // si cela renvoie false, c'est qu'il y a une erreur sur la requete
	{
		die ("Erreur sur la req: " . $req . "<br />Message: " . $mysqli->error . '<hr />'); // si la req echoue, die + affichage de l'erreur
	}
	return $resultat; // on retourne le résultat de la requete
}

function debug($var, $mode = 1)
{
	echo '<div style="background-color: orange; padding: 10px;">';
	$trace = debug_backtrace(); // la fonction debug_backtrace retourne un tableau ARRAY contenant des informations tel que la ligne et le fichier où est exécutée cette fonction	
	$trace = array_shift($trace); // extrait la première valeur d'un tableau ARRAY et la retourne. En raccourcissant le tableau et réordonnant les éléments du tableau.
	echo '<p>Debug demandé dans le fichier: ' . $trace['file'] . ' à la ligne: ' . $trace['line'] . '</p>';
	if($mode === 1)
	{
		echo '<pre>'; var_dump($var); echo '</pre>';
	}
	else {
		echo '<pre>'; print_r($var); echo '</pre>';
	}
	echo '</div>';
}

// debug($arg); => var_dump
// debug($arg, $arg2); => si $arg2 vaut autre chose que 1 => print_r 

// FONCTIONS UTILISATEUR
function utilisateur_est_connecte()
{ // cette fonction m'indique si l'utilisateur est connecté
	if(isset($_SESSION['utilisateur']))
	{
		return true;
	}else {
		return false;
	}	
}
//---
function utilisateur_est_connecte_et_est_admin()
{ // cette fonction m'indique si l'utilisateur est connecté mais aussi admin
	if(utilisateur_est_connecte() && $_SESSION['utilisateur']['statut'] == 1)
	{
		return true;
	}else {
		return false;
	}
}

// FONCTIONS GESTION BOUTIQUE
function verif_extension_photo($col_name)
{   
	$extension = strrchr($_FILES[$col_name]['name'], '.'); // permet de retourner la chaine de caractères contenue après le '.' fourni en 2eme argument. Cette fonction part de la fin de la chaine puis remonte pour couper la chaine lorsqu'elle tombe sur la première occurence du caractère fourni en 2eme argument. Par exemple on récupère .jpg
	$extension = strtolower(substr($extension, 1)); // ici on transforme la chaine en minuscule (strtolower) au cas ou et on coupe le point du début de la chaine (substr) au final le .jpg ou .JPG devient => jpg
	$extension_valide = array('jpg', 'jpeg', 'png', 'gif'); // on place dans un tableau array les extensions accepetées.
	
	$verif_extension = in_array($extension, $extension_valide); // in_array va tester si le premier argument correspond à une des valeurs contenues dans le tableau array fourni en 2eme argument // on récupère true ou false
	
	return $verif_extension; // on renvoi donc true ou false.
}
/****** redimensionnement des images ******/
function accorder_image($source)
{   
    // Je renomme le fichier de sortie
    $explode_src = explode('.', $source);
    $src_name = $explode_src[0];
    $renamed = $src_name . time() . '.jpg';
    
    // je récupère le type mime
    $mime = mime_content_type('../' . $source);
    $explode_mime = explode('/', $mime);
    $ext = $explode_mime[count($explode_mime) - 1];
    
    $image_create = 'imagecreatefrom' . $ext; // détermine le nom de la function en fonction du mime type du fichier
    $largeur = 300;// définition des nouvelles valeurs 
    $hauteur = 200;
    
    
    $image = $image_create('../' . $source);
    $taille = getimagesize('../' . $source);
    $sortie = imagecreatetruecolor($largeur,$hauteur);

    $coef = min($taille[0]/$largeur,$taille[1]/$hauteur);

    $deltax = $taille[0]-($coef * $largeur);
    $deltay = $taille[1]-($coef * $hauteur);

    imagecopyresampled($sortie,$image,0,0,$deltax/2,$deltay/2,$largeur,$hauteur,$taille[0]-$deltax,$taille[1]-$deltay);
    
    imagejpeg($sortie, '../' . $renamed, 100);
       
    /*
      Libération de la mémoire allouée aux deux images (sources et nouvelle).
    */
    imagedestroy($sortie);
    imagedestroy($image);
    
    unlink('../' . $source); // Je supprime l'image chargée temporairement
    
    return $renamed;
}


/* FONCTIONS PANIER */
//--- création du panier
function creation_panier()
{
	if(!isset($_SESSION['panier']))
	{
		$_SESSION['panier'] = array();
		$_SESSION['panier']['titre'] = array();
		$_SESSION['panier']['id_article'] = array();
		$_SESSION['panier']['quantite'] = array();
		$_SESSION['panier']['prix'] = array();
	}
	return true;
}

//--- ajout au panier
function ajouter_article_au_panier($titre, $id_article, $quantite, $prix)
{ // 4 arguments à fournir depuis la pange panier.php
	// nous devons savoir si l'article n'est pas déjà présent dans le panier afin de ne changer que sa quantité si c'est le cas.
	$position_article = array_search($id_article, $_SESSION['panier']['id_article']); // la fonction array_search cherche une valeur dans un tableau array et nous renvoie l'indice correspondant. Si la valeur n'est pas trouvée, on récupère false.
	if($position_article !== false) // comparaison strict sur la valeur mais aussi le type (!== au lieu de !=) car si l'on récupère l'indice 0 cela serait interprété comme false.
	{ // si l'on rentre dans cette condition, le produit est déjà présent dans le panier. Nous ne changeons donc que sa quantité.
		$_SESSION['panier']['quantite'][$position_article] += $quantite; 
	}else {
		// si on rentre dans le else alors l'article n'est pas déjà dans le panier et nous le rajoutons.
		$_SESSION['panier']['quantite'][] = $quantite;
		$_SESSION['panier']['titre'][] = $titre;
		$_SESSION['panier']['prix'][] = $prix;
		$_SESSION['panier']['id_article'][] = $id_article;
	}
}

//--- Montant total du panier
function montant_total()
{
	$total = 0;
	$nb_article = count($_SESSION['panier']['titre']); // on récupère le nombre d'article dans le panier.
	for($i = 0; $i < $nb_article; $i++)
	{
		$total += $_SESSION['panier']['prix'][$i]*$_SESSION['panier']['quantite'][$i]; // on multiplie le prix par la quantité de chaque article présent dans le panier et on ajoute ses valeurs dans la variable $total
	}
	return round($total, 2); // round() permet d'arrondir la valeur, le deuxième argument précise le nombre de décimales.
}

//--- Pour retirer un article du panier
function retirer_un_article_du_panier($id_article)
{
	$position_article = array_search($id_article, $_SESSION['panier']['id_article']); // retourne un chiffre correspondant à l'indice ou se trouve cette valeur.
	if($position_article !== false) // si le produit est présent dans le panier => on le retire
	{
		array_splice($_SESSION['panier']['id_article'], $position_article, 1);
		array_splice($_SESSION['panier']['prix'], $position_article, 1);
		array_splice($_SESSION['panier']['titre'], $position_article, 1);
		array_splice($_SESSION['panier']['quantite'], $position_article, 1);
		// array_splice() permet de retirer un élément du tableau array mais aussi de réordonner les indices afin qu'il n'y a pas un trou dans notre tableau(pas de décalage dans les indices).
		// http://php.net/manual/fr/function.array-splice.php
	}
}





//===================

//---- FONCTION AFFICHAGE TABLES

// ------ afficher_table_avec_boucle ( requete SELECT de la table ,  ajout ou non de colonnes modifier/suppr ,  nom d'indice de l'id pour boutons suppr/modif )

// $req => requete de récupération des champs en BDD (ex: SELECT * FROM article)

//$nom_id => Pour l'affichage des colonnes suppression et modification, si cet argument est défini, et la création des boutons modifier et supprimer:  informer le nom d'indice des données de la table (le Primary-key) (ex: id_salle / id_article / id_membre ...) / Si celui-ci n'est pas spécifié c'est qu'il n'y a pas de boutons suppr/modif à créer.

// -----exemple
// afficher_table_avec_boucle("SELECT * FROM article", 31, "id_article");       ==> affichera, dans un tableau, la table article avec colonnes et boutons modifier / supprimer grace à "id_article" 
// afficher_table_avec_boucle("SELECT * FROM article");        ==> affichera simplement le tableau sans boutons de gestion


function afficher_table_avec_boucle($req, $nom_id = "")
{
	global $mysqli;
	
	echo '<div class="col-sm-12">';

	$resultat = $mysqli->query($req);// Requete de récupération de table
	echo '<table border="2" class="table table-striped" style="border-collapse: collapse; width: 100%;">';
	echo '<tr>';
	while($colonne = $resultat->fetch_field()) 
	{
		echo '<th class="affichage">' . $colonne->name . '</th>';
	}
	if($nom_id !== "") // Si l'argument $nom_id est différent de "", ajouter les colonnes suppression et modification
	{
		echo '<th class="affichage">Modif</th><th class="affichage">Suppr</th>';
	}
	echo '</tr>';

	while($entree = $resultat->fetch_assoc())
	{
		echo '<tr>';
		foreach($entree AS $indice => $valeur)
		{
			// afficher les images dans un img src !
			if(($indice == 'photo') || ($indice == 'photo_2') || ($indice == 'photo_3'))
			{
                            if (!empty($valeur))
                            {
				echo '<td  class="affichage"><img src="' . URL . $valeur . '" alt="'. $valeur .'" width="90" /></td>';
                            } else {
                                echo '<td  class="affichage">Pas d\'image</td>';
                            }
			}elseif($indice == 'description')
			{
				echo '<td  class="affichage">' . substr($valeur, 0, 26) . ' <a href="">... lire la suite</a></td>';
			} elseif ($indice == 'date_arrivee' || $indice == 'date_depart' || $indice == 'date_enregistrement')
			{
				echo '<td  class="affichage">' . change_date($valeur) . '</td>';// S'il y a une date dans le tableau, la traiter pour l'afficher en format français
			}else {
				echo '<td  class="affichage">' . $valeur . '</td>';
			}
			
		}
		if($nom_id !== "") // Si l'argument $nom_id est différent de "" alors les boutons suppression et modification s'affichent
		{
			echo '<td  class="affichage"><a href="?action=modification&id=' . $entree[$nom_id] . '" class="btn btn-warning"><span class="glyphicon glyphicon-refresh"></span></a></td>';
			
			echo '<td class="affichage"><a onClick="return(confirm(\'Etes-vous sur ?\'));" href="?action=suppression&id=' . $entree[$nom_id] . '" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span></a></td>';
		}
		
		echo '</tr>';
	}
	echo '</table>';
	echo '</div>';
}

//------
//Traitement de la date

function change_date($date_bdd)
{

    $date = new DateTime($date_bdd);

    //Retourne la date au format francophone:
    return $date->format('d/m/Y H:i');
}





