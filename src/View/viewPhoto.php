<!DOCTYPE html>
<html>
<head>
    <title>Site SIL3</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link rel="stylesheet" type="text/css" href="../../public/assets/style/style.css" media="screen" title="Normal"/>
</head>
<body>
<div id="entete">
    <h1>Site SIL3</h1>
</div>
<div id="menu">
    <h3>Menu</h3>
    <ul>
        <?php
        # Utilisation du modèle

        require_once("../vendor/autoload.php");
        // Débute l'acces aux images
        $imgDAO = new \App\Model\Dao\DbImageDAO();

        // Construit l'image courante
        // et l'ID courant
        // NB un id peut être toute chaine de caractère !!
        if (isset($_GET["imgId"]) && is_string($_GET["imgId"])) {
            $imgId = $_GET["imgId"];
            $img = $imgDAO->getImage($imgId);
        } else {
            // Pas d'image, se positionne sur la première
            $img = $imgDAO->getFirstImage();
            // Conserve son id pour définir l'état de l'interface
            $imgId = $img->getId();
        }

        // Regarde si une taille pour l'image est connue
        if (isset($_GET["size"]) && is_numeric($_GET["size"])) {
            $size = $_GET["size"];
        } else {
            # sinon place une valeur de taille par défaut
            $size = 480;
        }


        # Mise en place du menu
        $menu['Home'] = "index.php";
        $menu['A propos'] = "aPropos.php";
        // Pre-calcule la première image
        $newImg = $imgDAO->getFirstImage();
        # Change l'etat pour indiquer que cette image est la nouvelle
        $menu['First'] = "viewPhoto.php?imgId={$newImg->getId()}&size=$size";
        # Pre-calcule une image au hasard

        $rdmImg = $imgDAO->getRandomImage();
        $menu['Random'] = "viewPhoto.php?imgId={$rdmImg->getId()}&size=$size";
        # Pour afficher plus d'image passe à une autre page
        $menu['More'] = "viewPhotoMatrix.php?imgId=$imgId";
        // Demande à calculer un zoom sur l'image
        $menu['Zoom +'] = "zoom.php?zoom=1.25&imgId=$imgId&size=$size";
        // Demande à calculer un zoom sur l'image
        $menu['Zoom -'] = "zoom.php?zoom=0.75&imgId=$imgId&size=$size";
        // Affichage du menu
        foreach ($menu as $item => $uri) {
            print "<li><a href=\"$uri\">$item</a></li>\n";
        }
        ?>
    </ul>
</div>

<?php
    $prev = $imgDAO->getPrevImage($img)->getId();
    $next = $imgDAO->getNextImage($img)->getId();

?>

<div id="corps">
    <p>
        <a href="viewPhoto.php?imgId=<?php echo $prev; ?>&size=<?php echo $size; ?>">Prev</a>
        <a href="viewPhoto.php?imgId=<?php echo $next; ?>&size=<?php echo $size; ?>">Next</a>
        <a href="zoom.php?zoom=1.25&imgId=<?php echo $imgId; ?>&size=<?php echo $size; ?>">
            <img src="<?php echo $img->getURL(); ?>" width="<?php echo $size; ?>">
        </a>
    </p>
</div>

<div id="pied_de_page">
</div>
</body>
</html>




