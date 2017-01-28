<?php
require_once("inc/init.inc.php");

$pseudo="";
$mdp="";
$nom="";
$prenom="";
$email="";
$civilite="m";
$statut=0;


if(isset($_POST['pseudo']) && isset($_POST['mdp']) && isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['email']) && isset($_POST['civilite']))
{
  extract($_POST); 

  if(strlen($pseudo)>14 || strlen($pseudo)< 4)
  {
    $msg .= '<div class="erreur">Erreur sur la taille du Pseudo:<br/>Le pseudo doit avoir entre 4 et 14 caractères inclus !</div>';
  }

  if(!preg_match('#^[a-zA-Z0-9._-]+$#', $pseudo))
  {

      $msg .= '<div class="erreur">Erreur sur le Pseudo:<br/>Caractères autorisés: de a - z et de 0 - 9</div>';
  }

  // Controle disponibilité du Pseudo
  $pseudo = htmlentities($pseudo, ENT_QUOTES);
  $mdp = htmlentities($mdp, ENT_QUOTES);
  $nom = htmlentities($nom, ENT_QUOTES);
  $prenom = htmlentities($prenom, ENT_QUOTES);
  $email = htmlentities($email, ENT_QUOTES);
  $civilite = htmlentities($civilite, ENT_QUOTES);

  $controle_pseudo = execute_requete("SELECT * FROM membre WHERE pseudo='$pseudo'"); // On regarde en BDD si le pseudo existe

  if($controle_pseudo->num_rows >= 1)
  {
    $msg .= '<div class="erreur">Erreur sur le Pseudo:<br/>Pseudo indisponible</div>';
  }

  // controle sur l'Email (unique en BDD)
  $controle_email = execute_requete("SELECT * FROM membre WHERE email='$email'");
  if($controle_email->num_rows > 0 ) // s'il y a au moins une ligne dans l'objet $controle_email alors l'email existe déjà en BDD
  {

    $msg .= '<div class="erreur">Erreur: Adresse email: '. $email . ' est déjà enregistrée</div>';
  }

  // Validation et inscription BDD
  if(empty($msg)) // si $msg est vide alors il n'y a pas d'erreurs et nous pouvons lancer l'inscription
  {
    // $mdp = password_hash($mdp, PASSWORD_DEFAULT); // Pour crypter le mot de passe // ou PASSWORD_CRYPT()

    execute_requete("INSERT INTO membre (pseudo, mdp, nom, prenom, email, civilite, statut, date_enregistrement) VALUES ('$pseudo', '$mdp', '$nom', '$prenom', '$email', '$civilite', '$statut', NOW())");

    header("location:index.php?action=connexion");
    $msg .= '<div class="succes">Inscription OK !</div>';

  }
}

debug($_POST);
debug($msg);


include("inc/header.inc.php");
include("inc/nav.inc.php");
?>
    <div class="container">

      <div class="starter-template">
        <h1 style="font-weight: bold;"><span class="glyphicon glyphicon-share-alt"></span> Inscription</h1>
          <?php echo $msg; // variable initialisée dans le fichier init.inc.php ?>
        
      </div>
      <div class="row">
        <div class="col-sm-6 col-sm-offset-3">
        <?php
        //debug($_POST);
        ?>
          <form method="post" action="">
            <div class="form-group">
              <label for="pseudo">Psuedo</label>
              <input type="text" class="form-control" id="pseudo" name="pseudo" placeholder="Pseudo..." value="<?php if(isset($_POST['pseudo'])){echo $_POST['pseudo'];}?>">
            </div>

            <div class="form-group">
              <label for="mdp">Mot de passe</label>
              <input type="text" class="form-control" id="mdp" name="mdp" placeholder="Mot de passe..." value="<?php echo $mdp; ?>">
            </div>

            <div class="form-group">
              <label for="nom">Nom</label>
              <input type="text" class="form-control" id="nom" name="nom" placeholder="Nom..." value="<?php echo $nom; ?>">
            </div>

            <div class="form-group">
              <label for="prenom">Prénom</label>
              <input type="text" class="form-control" id="prenom" name="prenom" placeholder="Prénom..." value="<?php echo $prenom; ?>">
            </div>

            <div class="form-group">
              <label for="email">Email</label>
              <input type="text" class="form-control" id="email" name="email" placeholder="Email..." value="<?php echo $email; ?>">
            </div>

            <div class="radio">
              <label>
                <input type="radio" name="civilite" value="m" <?php if($civilite == 'm'){echo 'checked';} ?>>
                Homme
              </label>
            </div>
            <div class="radio">
              <label>
                <input type="radio" name="civilite" value="f"<?php if($civilite == 'f'){echo 'checked';} ?>>
                Femme
              </label>
            </div>

            <input type="submit" class="form-control btn btn-warning" id="inscription" name="inscription" value="Inscription" />

          </form>
        </div>
      </div>

    </div><!-- /.container -->

<?php
include("inc/footer.inc.php");