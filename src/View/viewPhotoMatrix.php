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
        if (isset($_GET["imgId"]) && is_numeric($_GET["imgId"])) {
            $imgId = $_GET["imgId"];
            $img = $imgDAO->getImage($imgId);
        } else {
            // Pas d'image, se positionne sur la première
            $img = $imgDAO->getFirstImage();
            // Conserve son id pour définir l'état de l'interface
            $imgId = $img->getId();
        }

        // Récupère le nombre d'images à afficher
        if (isset($_GET["nbImg"])) {
            $nbImg = $_GET["nbImg"];
        } else {
            # sinon débute avec 2 images
            $nbImg = 2;
        }
        // Regarde si une taille pour l'image est connue
        if (isset($_GET["size"]) && is_numeric($_GET["size"])) {
            $size = $_GET["size"];
        } else {
            # sinon place une valeur de taille par défaut
            $size = 480;
        }

        # Calcul la liste des images à afficher
        $imgMatrixURL = $imgDAO->getImageList($img, $nbImg);

        # Mise en place du menu
        $menu['Home'] = "index.php";
        $menu['A propos'] = "aPropos.php";
        // Pre-calcule la première image
        $newImg = $imgDAO->getFirstImage();
        # Change l'etat pour indiquer que cette image est la nouvelle
        $newImgId = $newImg->getId();
        $menu['First'] = "viewPhotoMatrix.php?imgId=$newImgId&nbImg=$nbImg";
        # Pre-calcule une image au hasard
        $rdmImg = $imgDAO->getRandomImage();
        $menu['Random'] = "viewPhotoMatrix.php?imgId={$rdmImg->getId()}&size=$size&nbImg=$nbImg";
        # Pré-calcule le nouveau nombre d'images à afficher si on en veux plus
        $newNbImg = $nbImg * 2;
        $menu['More'] = "viewPhotoMatrix.php?imgId=$imgId&nbImg=$newNbImg";
        # Pré-calcule le nouveau nombre d'images à afficher si on en veux moins
        $newNbImg = ((int) ($nb = $nbImg / 2)) > 0
            ? $nb
            : 1;
        $menu['Less'] = "viewPhotoMatrix.php?imgId=$imgId&nbImg=$newNbImg";
        // Affichage du menu
        foreach ($menu as $item => $act) {
            print "<li><a href=\"$act\">$item</a></li>\n";
        }
        ?>
    </ul>
</div>

<?php
    $prev = $imgDAO->getImage($imgId - $nbImg)->getId();
    $next = $imgDAO->getImage($imgId + $nbImg)->getId();
    $size = 480 / sqrt(count($imgMatrixURL));
?>

<div id="corps">
    <p>
        <a href="viewPhotoMatrix.php?imgId=<?php echo $prev; ?>&nbImg=<?php echo $nbImg; ?>">Prev</a>
        <a href="viewPhotoMatrix.php?imgId=<?php echo $next; ?>&nbImg=<?php echo $nbImg; ?>">Next</a>
    </p>
    <?php foreach ($imgMatrixURL as $image): ?>
        <a href="viewPhoto.php?imgId=<?php echo $image->getId(); ?>">
            <img src="<?php echo $image->getUrl(); ?>" width="<?php echo $size; ?>" height="<?php echo $size ?>"/>
        </a>
    <?php endforeach; ?>
</div>

<div id="pied_de_page">
</div>
</body>
</html>




