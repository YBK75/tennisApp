<?php  // On démarre la session AVANT d'écrire du code HTML
session_start();
?>

<!DOCTYPE html>

<html>
    <head>
        <meta charset="utf-8" />
        <title>Réservation Tennis</title>
    </head>
    <body>

    <h1>Réservation des Terrains de Tennis</h1>
    <br>
 <?php  
if (isset($_GET['connect']) AND $_GET['connect'] == 'deco') {
?>
        <p><em>Déconnexion réussie !</em></p>

<?php 
session_destroy();
}
if (isset($_GET['inscript']) AND $_GET['inscript'] == 'ok') {
?>
        <p><em>Inscription réussie ! Connectez-vous pour continuez. </em></p>
<?php } ?>
    <p>Aujourd'hui nous sommes le <?php echo date('d/m/Y'); ?>. </p> <!-- date du jour -->
    <br>
    <p>Veuillez vous connecter :</p>

    <form action="connexion.php" method="post">
    <p>
        <label for="prenom">Prénom</label> : <input type="text" name="prenom" id="prenom" style="text-transform:uppercase" />
        <label for="nom">Nom</label> : <input type="text" name="nom" id="nom" style="text-transform:uppercase" />
        <input type="submit" name="valider" value="Connexion" />
        <input type="submit" name="valadmin" value="Connexion Administrateur" />
    </p>
    </form>
    <br>

<!-- Connexion à la base de données -->
<?php
    try
    {
        // On se connecte à MySQL
        $bdd = new PDO('mysql:host=localhost;dbname=projetinfo;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
        
    }
    catch (Exception $e)
    {
        // En cas d'erreur, on affiche un message et on arrête tout
            die('Erreur : ' . $e->getMessage());
    }

    
?>
<?php //Pour vérifier que l'étudiant est bien inscrit dans la base de données
if (isset($_POST['prenom']) AND isset($_POST['nom']) AND isset($_POST['valider'])) {
		$reqinscr = $bdd->prepare("SELECT id_etu, COUNT(*) AS nblignes FROM etudiants WHERE nom = ? AND prenom =  ?");
        $reqinscr->execute(array($_POST['nom'],$_POST['prenom'])); //pour mettre des variables
        
        // On affecte la réponse dans un tableau
        while ($datainscr = $reqinscr->fetch())
        { //on affiche les résultats
        if ($datainscr['nblignes'] > 0) { // si il y a déja des entrées dans la base de données
			$_SESSION['prenom'] = $_POST['prenom'];
			$_SESSION['nom'] = $_POST['nom'];
			$_SESSION['id'] = $datainscr['id_etu'];
			header('Location: accueil.php?connect=ok');
            

        $reqinscr->closeCursor(); // Termine le traitement de la requête 
        }
        else{
        	echo "<em> Vous n'êtes pas inscrit dans la base des étudiants. Procédez à l'inscription svp. </em>";
        }
    	}
}
?>

<?php //Pour vérifier que l'étudiant est bien administrateur
if (isset($_POST['prenom']) AND isset($_POST['nom']) AND isset($_POST['valadmin'])) {
		$reqinscr = $bdd->prepare("SELECT id_etu, admin, COUNT(*) AS nblignes FROM etudiants WHERE admin = ? AND nom = ? AND prenom =  ?");
        $reqinscr->execute(array(1, $_POST['nom'],$_POST['prenom'])); //pour mettre des variables
        
        // On affecte la réponse dans un tableau
        while ($datainscr = $reqinscr->fetch())
        { //on affiche les résultats
        if ($datainscr['nblignes'] > 0) { // si il y a déja des entrées dans la base de données
			$_SESSION['prenom'] = $_POST['prenom'];
			$_SESSION['nom'] = $_POST['nom'];
			$_SESSION['id'] = $datainscr['id_etu'];
			$_SESSION['admin'] = 'oui';
			header('Location: admin.php');
            

        $reqinscr->closeCursor(); // Termine le traitement de la requête 
        }
        else{
        	echo "<em> Vous n'avez pas les droits administrateur. </em>";
        }
    	}
}
?>
	
	<p>Pas encore inscrit ?</p>
	<form action="connexion.php" method="post">
    <p>
        <input type="submit" name="inscri" value="Inscription" />
    </p>
    </form>
    <?php
    if (isset($_POST['inscri'])) {
		header('Location: inscription.php');
    }
    ?>

    </body>
</html>