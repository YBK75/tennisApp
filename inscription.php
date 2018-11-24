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
    <p>Aujourd'hui nous sommes le <?php echo date('d/m/Y'); ?>. </p> <!-- date du jour -->
    <br>
    <p>Inscription : </p>

    <form action="ecriture.php" method="post">
    <p>
        <label for="prenom">Prénom</label> : <input type="text" name="prenom" id="prenom" style="text-transform:uppercase"/>
        <label for="nom">Nom</label> : <input type="text" name="nom" id="nom" style="text-transform:uppercase"/>
        <label for="datefin">Date de fin de la cotisation</label> : <input type="date" name="datefin" id="datefin" />
        <input type="submit" name="inscrip" value="Inscription" />
    </p>
    </form>



    </body>
</html>