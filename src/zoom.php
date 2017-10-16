<?php
// Etat de l'interface
// Recupère l'identifiant de l'image courante
if (isset($_GET["imgId"])) {
    $imgId = $_GET["imgId"];
} else {
    // c'est une erreur d'appel de ce calcul
    trigger_error("Etat identifiant de l'image absent");
}
// Regarde si une taille pour l'image est connue
if (isset($_GET["size"])) {
    $size = $_GET["size"];
} else {
    # sinon place une valeur de taille par défaut
    $size = 480;
}

// Parametre de l'action
if (isset($_GET["zoom"])) {
    $zoom = $_GET["zoom"];
} else {
    // c'est une erreur d'appel de ce calcul
    trigger_error("Parametre zoom absent");
}

// Calcule la nouvelle taille
$size *= $zoom;
// Retourne dans le mode d'affichage d'une image
header("Location: viewPhoto.php?imgId=$imgId&size=$size");