<?php

namespace cafetapi\webi\Core;

use cafetapi\MailManager;
use cafetapi\config\Config;
use cafetapi\config\Defaults;
use cafetapi\exceptions\EmailFormatException;
use cafetapi\io\ClientManager;
use cafetapi\io\UserManager;
use cafetapi\user\Perm;

require_once WEBI_INCLUDES . 'pages_views.php';

function controller_show_profile()
{
    $b = new PageBuilder();
    
    if (! cafet_get_logged_user()) {
        $b->build(function() {
            echo '<h1 style="text-align:center;color:red">Vous devez être connecté pour accèder à votre profil.</h1>';
        });
    } else {
        $b->build(__NAMESPACE__ . '\show_profile');
    }
}

function controller_edit_profile()
{
    $b = new PageBuilder();
    $user = $b->getUser();
    
    if (!$user) {
        $b->build(__NAMESPACE__ . '\need_signin');
        exit();
    } elseif (isset($_REQUEST['name']) && isset($_REQUEST['firstname']) ||  isset($_REQUEST['phone'])) {
        $manager = UserManager::getInstance();
        
        if ($_REQUEST['name'] != $user->getFamilyName()) {
            $manager->setName($user->getId(), (string) $_REQUEST['name']);
            $user->setName((string) $_REQUEST['name']);
        }
        
        if ($_REQUEST['firstname'] != $user->getFirstname() ) {
            $manager->setFirstname($user->getId(), (string) $_REQUEST['firstname']);
            $user->setFirstname((string) $_REQUEST['firstname']);
        }
        
        if ($_REQUEST['phone'] != $user->getPhone()) {
            $manager->setPhone($user->getId(), (string) $_REQUEST['phone']);
            $user->setPhone((string) $_REQUEST['phone']);
        }
        
        cafet_set_logged_user($user);
    }
        
    $b->build(__NAMESPACE__ . '\edit_profile');
}

function controller_edit_password()
{
    $builder = new PageBuilder();
    
    if (@$_POST['MDP'] && @$_POST['MDP2'] && $_POST['MDP'] == $_POST['MDP2'] && cafet_verify_password($_POST['old_MDP'], $builder->getUser()->getHash(), $builder->getUser()->getPseudo())) {
        UserManager::getInstance()->setPassword($builder->getUser()->getId(), $_POST['MDP']);
    }
    
    if(isset($_POST['MDP'])){
        if(@$_POST['MDP'] && @$_POST['MDP2'] && $_POST['MDP']==$_POST['MDP2']){
            if(cafet_verify_password($_POST['old_MDP'], $builder->getUser()->getHash(), $builder->getUser()->getPseudo())) {
                UserManager::getInstance()->setPassword($builder->getUser()->getId(), $_POST['MDP']);
                $builder->build(function() {
                    echo "<article>Mot de passe modifié.</article>";
                });
            } else $builder->build(__NAMESPACE__ . '\edit_password', "Mot de passe incorrecte.");
        } else $builder->build(__NAMESPACE__ . '\edit_password', "Les mots de passe ne correspondent pas, veuillez réésayer.");
    } else $builder->build(__NAMESPACE__ . '\edit_password');
}

function controller_signout() {
    if (isset($_COOKIE[Config::session_name])) {
        cafet_init_session();
        cafet_destroy_session();
    }
    header('Location: /webi');
    exit();
}

function controller_signin() {
    if(isset($_POST['Pseudo']) && $user = cafet_check_login(@$_POST['Pseudo'], @$_POST['MDP'])) {
        cafet_init_session();
        cafet_set_logged_user($user);
        header('Location: /webi');
        exit();
    } else {
        $b = new PageBuilder();
        $b->build(__NAMESPACE__ . '\signin');
    }
}

function controller_signup() {
    $b = new PageBuilder();
    
    if(isset($_POST['Pseudo']) || isset($_POST['MDP']) || isset($_POST['MDP2']) || isset($_POST['Nom']) || isset($_POST['Prenom']) || isset($_POST['Email'])) {
        if (@$_POST['Pseudo'] && @$_POST['MDP'] && @$_POST['MDP2'] && @$_POST['Nom'] && @$_POST['Prenom'] && @$_POST['Email']) {
            if ($_POST['MDP'] == $_POST['MDP2']) {
                $manager = UserManager::getInstance();
                
                if ($manager->getUser($_POST['Email'])) $b->build(__NAMESPACE__ . '\signup', 'L\'addresse email entrée est déjà utilisée !');
                else if ($manager->getUser($_POST['Pseudo'])) $b->build(__NAMESPACE__ . '\signup', 'Le nom d\'utilisateur entré est déjà utilisé !');
                else try {
                    $user = UserManager::getInstance()->addUser($_POST['Pseudo'], $_POST['Email'], $_POST['Prenom'], $_POST['Nom'], $_POST['MDP']);
                    UserManager::getInstance()->registerLogin($user->getId());
                    cafet_init_session();
                    cafet_set_logged_user($user);
                    header('Location: /webi');
                    exit();
                } catch (EmailFormatException $e) {
                    $b->build(__NAMESPACE__ . '\signup', 'L\'addresse email n\'est pas valide !');
                }
            } else $b->build(__NAMESPACE__ . '\signup', 'Les mots de passe ne correspondent pas !');
        } else $b->build(__NAMESPACE__ . '\signup', 'Tous les champs doivent être remplis !');
    } else $b->build(__NAMESPACE__ . '\signup');
}

function controller_account_reset() {
    $b = new PageBuilder();
    
    if ($b->getUser()) {
        $b->build(function() {
            echo '<h1 style="text-align:center;color:red">Vous êtes déjà connecté(e) à votre profil.</h1>';
        });
    } elseif (isset($_REQUEST['mail'])) {
        if ( filter_var($_REQUEST['mail'], FILTER_VALIDATE_EMAIL) && $user = UserManager::getInstance()->getUser($_REQUEST['mail'])) {
            $pass = base64_encode(random_bytes(9));
            $mail = MailManager::message($user, '<p>' .
                'A votre demande, votre mot de passe a été modifié, ' . 
                "votre nouveau mot de passe est désormais <strong>$pass</strong>, " .
                'notez bien ce mot de passe, il vous sera demandé lors de votre prochaine connexion.</p>' .
                '<p>Cliquez <a href="' . Config::url . 'webi/signin">ici</a> pour vous connecter.</p>');
            $mail->setSubject('Réinirialisation de votre mot de pass');
            if (@$mail->send()) {
                UserManager::getInstance()->setPassword($user->getId(), $pass);
                $b->build(__NAMESPACE__ . '\account_reset', 'Votre mot de passe a été modifié, vous allez recevoir un mail avec votre nouveau mot de passe.');
            } else $b->build(function() {
               echo '<article>Nous n\'avons pas pu réinitialiser votre mot de passe.</article>'; 
            });
        } else $b->build(__NAMESPACE__ . '\account_reset', 'Nous ne connaissons aucun compte lié à l\'adresse email entrée.');
    } else $b->build(__NAMESPACE__ . '\account_reset');
}

function controller_members_reset() {
    $b = new PageBuilder();
    
    /*
     * Permissions Check
     */
    if (!$b->getUser()) {
        $b->build(function() {
            echo '<h1 style="text-align:center;color:red">Vous devez être connecté pour accèder à votre profil.</h1>';
        });
            return;
    } elseif (!$b->getUser()->hasPermission(Perm::SITE_MANAGE_USERS)) {
        $b->build(function() {
            echo '<h1 style="text-align:center;color:red">Vous n\'avez les permissions nécessaires pour accèder à cette page.</h1>';
        });
            return;
    }
    
    $manager = ClientManager::getInstance();
    foreach ($manager->getClients() as $client) $manager->setMember($client->getId(), false);
    ;
    $b->build(__NAMESPACE__ . '\members_reset');
}
