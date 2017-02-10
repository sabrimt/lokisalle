<?php 
if(isset($_GET['action']) && $_GET['action'] == 'deconnexion')
{
  unset($_SESSION['utilisateur']);
  
}

/*if(utilisateur_est_connecte()) 
{
  header("location:".URL."profil.php");                                        ///////////////////
  exit(); 
}*/

if(isset($_POST['pseudo']) && isset($_POST['mdp']) && isset($_GET['action']) && $_GET['action'] == 'connexion')
{
  // debug($_POST);
  foreach($_POST as $indice => $valeur)
  {
    $_POST[$indice] = htmlentities($valeur, ENT_QUOTES);
  }
  extract($_POST);
  
  $selection_membre = execute_requete("SELECT * FROM membre WHERE pseudo='$pseudo' AND mdp='$mdp'");
  
  if($selection_membre->num_rows == 1) // s'il y a au moins une ligne, alors le pseudo et le mdp sont corrects
  {
    
    $_SESSION['utilisateur'] = array();
    $membre = $selection_membre->fetch_assoc(); // on transforme l'objet $selection_membre en tableau array avec la methode fetch_assoc()
    foreach($membre AS $indice => $valeur)
    {
      if($indice != 'mdp')
      {
        $_SESSION['utilisateur'][$indice] = $valeur;
      }
    }
    
    header("location:".URL."profil.php");
  }
  else {
    $msg .= '<div class="erreur">Erreur:<br />Le pseudo ou le mot de passe sont incorrects veuillez vérifier vos saisies</div>';
  }
}

$title = 'connexion'; ?>
    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
         
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
              <li>  <a class="navbar-brand" href="<?php echo URL; ?>index.php"> CavaSalle</a> </li>
              <li>  <a href="<?php echo URL; ?>qui_sommes_nous.php"> Qui sommes nous?</a></li>
              <li>  <a href="<?php echo URL; ?>contact.php">Contact</a></li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
          		
            <li role="presentation" class="dropdown">
              <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-user"></span> <?php 
                if (isset($_SESSION['utilisateur']['pseudo'])){
                  echo '<b>' . $_SESSION['utilisateur']['pseudo'] . '</b>';
                }else {
                  echo 'Membre';
                }
                ?> <span class="caret"></span>
              </a>
              <ul class="dropdown-menu">
                <?php
                  if(utilisateur_est_connecte())
                  {
                    echo '
                          <li' . active(URL . 'profil.php') . '><a href="' . URL . 'profil.php">Profil</a></li>
                          <li><a href="?action=deconnexion">Déconnexion</a></li>';
                    
                  }else {
                    echo '
                          <li><a href="?action=connexion">Connexion</a></li>
                          <li><a href="' . URL . 'inscription.php">Inscription</a></li>';
                  }
                ?>
              </ul>
            </li>

            <?php
              if(utilisateur_est_connecte_et_est_admin())
              {
              ?>

                  <li role="presentation" class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false" <?php active(URL . 'admin/gestion_salle.php') ?>><span class="glyphicon glyphicon-cog"></span> 

                      <?php echo change_lien_menu(); ?><span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">

                      <li <?php echo active(URL . 'admin/gestion_salle.php') ?>><a href="<?php  echo URL;?>admin/gestion_salle.php">Gestion Salles</a></li>
                      <li <?php echo active(URL . 'admin/gestion_produits.php') ?>><a href="<?php echo URL;?>admin/gestion_produits.php">Gestion Produits</a></li>
                      <li <?php echo active(URL . 'admin/gestion_membres.php') ?>><a href="<?php echo URL;?>admin/gestion_membres.php">Gestion Membres</a></li>
                      <li <?php echo active(URL . 'admin/gestion_commandes.php') ?>><a href="<?php echo URL;?>admin/gestion_commandes.php">Gestion Commandes</a></li> 

                    </ul>
                  </li>

              <?php } ?>
            <div class="clear">
            </div> 
          </ul><!-- nav droite -->
        </div><!--/.nav-collapse -->
      </div>
    </nav>
    <?php if(isset($_GET['action']) && $_GET['action'] == 'connexion')
    { ?>
      
        <div class="container_connexion">
        <div class="block_connexion">
        <b class="fermeture"><a href="<?php echo URL; ?>index.php" class=" glyphicon glyphicon-remove-sign"></a></b>

          <div class="starter-template">
            <h1><span class="blueviolet glyphicon glyphicon-lock"></span> Connexion</h1> 
        <?php echo $msg; /* debug($_SESSION); */ // variable initialisée dans le fichier init.inc.php ?>    
          </div>
        
          <div class="row">
            <div class="col-sm-10 col-sm-offset-1">
              <?php echo $msg; ?>
              <form method="post" action="">
              <!--  Faire un formulaire avec les champs pseudo / mdp + bouton validation 
                  Faire les controles: si le pseudo et le mdp sont ok affichez connexion OK sinon affichez connexion NOK
              -->
                <div class="form-group">
                  <label for="pseudo">Pseudo</label>
                  <input type="text" class="form-control" id="pseudo" placeholder="Pseudo..." name="pseudo" />
                </div>
                <div class="form-group">
                  <label for="mdp">Mot de passe</label>
                  <input type="text" class="form-control" id="mdp" placeholder="Mot de passe..." name="mdp" />
                </div>
                <hr />
                <input type="submit" class="form-control btn btn-success" id="connexion" name="connexion" value="Connexion" />
              </form>
            </div>
          </div>
        </div>
    </div><!-- /.container -->
    
    
<!--     Button trigger modal 
<button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal">
  Launch demo modal
</button>

     Modal 
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"></span></button>
                    <div class="starter-template">
                        <h2 class="modal-title" id="myModalLabel"><span class="blueviolet glyphicon glyphicon-lock"></span> Connexion</h2>
                    </div>
                </div>
                <form method="post" action="">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-8 col-sm-offset-2">
                                
                                <div class="form-group">
                                    <label for="pseudo">Pseudo</label>
                                    <input type="text" class="form-control" id="pseudo" placeholder="Pseudo..." name="pseudo" />
                                </div>
                                <div class="form-group">
                                    <label for="mdp">Mot de passe</label>
                                    <input type="text" class="form-control" id="mdp" placeholder="Mot de passe..." name="mdp" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Retour</button>
                        <input type="submit" class="form-control btn btn-success" id="connexion" name="connexion" value="Connexion" />
                        <button type="button" class="btn btn-primary">Se connecter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    -->
    <?php }