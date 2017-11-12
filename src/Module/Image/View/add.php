<?php
/**
 * @var $renderer \App\Core\Renderer
 */
/**
 * @var $router \App\Core\Http\Router\Router
 */
/**
 * @var $context \App\Core\Utils\Context the current scope context (merged with parent's one)
 */

/**
 * @var $error bool is there an error on the form
 */
/**
 * @var $message string the error message if there's one
 */

/**
 * @var $image \App\Module\Image\Model\Entity\Image the current image or null if adding
 */
/**
 * @var $categories \App\Module\Category\Model\Entity\Category[]
 */
?>

<?= $renderer->render('@layout/header') ?>

    <h1>
        <?= $context->get('image') ? 'Edition' : 'Ajout' ?> d'une Image
    </h1>

    <?php if($context->get('error')): ?>
        <div class="error">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <form action="<?= $router->build('image.add') ?>" method="post" enctype="multipart/form-data">

        <?php if($context->get('image')): ?>
            <input type="hidden" name="id" value="<? $image->getId() ?>" />
            <img src="<?= $image->getURL() ?>"/>
            <br/>
        <?php endif; ?>

        <label>
            Image : <br/>
            Par upload : <input type="file" name="image" required id="by-upload"/> <br/>
            Ou par URL : <input type="text" name="image" id="by-url">
        </label>

        <br/>

        <label>
            Catégorie :
            <select name="category" id="category" required>
                <option value="0">Nouvelle catégorie</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category->getId() ?>"
                    <?= (
                            $context->get('image')
                            && $image->getCategory() !== null
                            && $image->getCategory()->getId() === $category->getId()
                        ) ? 'selected' : ''
                    ?>><?= $category->getName() ?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <div class="add-category" style="display: none;">
            <label>
                Nouvelle catégorie : <input type="text" name="new-category" id="n-cat" required disabled/>
            </label>
        </div>

        <br/>

        <label>
            Description :
            <textarea name="description" required cols="20" rows="4"><?= $context->get('image') ? $image->getDescription() : 'Description de l\'image' ?></textarea>
        </label>
        <br/>

        <input type="submit" value="Envoyer">
    </form>

    <script type="text/javascript">
        window.addEventListener('DOMContentLoaded', function() {
            var upload = document.getElementById('by-upload'),
                url = document.getElementById('by-url'),
                cat = document.getElementById('category'),
                nCat = document.getElementById('n-cat'),
                nContainer = document.getElementsByClassName('add-category')[0];

            cat.addEventListener('change', function(e){
                if(cat.value === '0'){
                    nContainer.style.display = 'block';
                    nCat.removeAttribute('disabled');
                } else {
                    nContainer.style.display = 'none';
                    nCat.setAttribute('disabled', 'true');
                }
            })

            upload.addEventListener('change', function(e) {
                upload.files.length
                    ? url.setAttribute('disabled', 'true')
                    : url.removeAttribute('disabled');
            });

            url.addEventListener('change', function(e) {
                url.value.length
                    ? upload.setAttribute('disabled', 'true')
                    : upload.removeAttribute('disabled');
            })

            cat.dispatchEvent(new Event('change'));
        })
    </script>

<?= $renderer->render('@layout/footer') ?>