<?php

use cafetapi\exceptions\EmailFormatException;
use cafetapi\io\UserManager;
use webi_min\includes\PageBuilder;
use cafetapi\MailManager;

function webi_controller_show_profile()
{
    $b = new PageBuilder();
    
    if (! cafet_get_logged_user()) {
        $b->build(function() {
            echo '<h1 style="text-align:center;color:red">Vous devez être connecté pour accèder à votre profil.</h1>';
        });
    } else {
        $b->build('webi_show_profile');
    }
}

function webi_controller_edit_profile()
{
    $b = new PageBuilder();
    $user = $b->getUser();
    
    if (!$user) {
        $b->build(function() {
            echo '<h1 style="text-align:center;color:red">Vous devez être connecté pour accèder à votre profil.</h1>';
        });
        exit();
    } elseif (isset($_REQUEST['name']) && isset($_REQUEST['firstname']) ||  isset($_REQUEST['phone'])) {
        $manager = UserManager::getInstance();
        
        if ($_REQUEST['name'] != $user->getName()) {
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
        
        $b->build('webi_edit_profile');
    }
}

function webi_controller_edit_password()
{
    if (@$_POST['MDP'] && @$_POST['MDP2'] && $_POST['MDP'] == $_POST['MDP2'] && cafet_verify_password($_POST['old_MDP'], $builder->getUser()->getHash(), $builder->getUser()->getPseudo())) {
        UserManager::getInstance()->setPassword($builder->getUser()->getId(), $_POST['MDP']);
    }
    
    $b = new PageBuilder();
    $b->build('webi_edit_password');
}

function webi_controller_signout() {
    if (isset($_COOKIE[cafet_get_configuration('session_name')])) {
        cafet_init_session();
        cafet_destroy_session();
    }
    header('Location: /webi');
    exit();
}

function webi_controller_signin() {
    if(isset($_POST['Pseudo']) && $user = cafet_check_login(@$_POST['Pseudo'], @$_POST['MDP'])) {
        cafet_init_session();
        cafet_set_logged_user($user);
        header('Location: /webi');
        exit();
    } else {
        $b = new PageBuilder();
        $b->build('webi_signin');
    }
}

function webi_controller_signup() {
    $b = new PageBuilder();
    
    if(isset($_POST['Pseudo']) || isset($_POST['MDP']) || isset($_POST['MDP2']) || isset($_POST['Nom']) || isset($_POST['Prenom']) || isset($_POST['Email'])) {
        if (@$_POST['Pseudo'] && @$_POST['MDP'] && @$_POST['MDP2'] && @$_POST['Nom'] && @$_POST['Prenom'] && @$_POST['Email']) {
            if ($_POST['MDP'] == $_POST['MDP2']) {
                $manager = UserManager::getInstance();
                
                if ($manager->getUser($_POST['Email'])) $b->build('webi_signup', 'L\'addresse email entrée est déjà utilisée !');
                else if ($manager->getUser($_POST['Pseudo'])) $b->build('webi_signup', 'Le nom d\'utilisateur entré est déjà utilisé !');
                else try {
                    $user = UserManager::getInstance()->addUser($_POST['Pseudo'], $_POST['Email'], $_POST['Prenom'], $_POST['Nom'], $_POST['MDP']);
                    UserManager::getInstance()->registerLogin($user->getId());
                    cafet_init_session();
                    cafet_set_logged_user($user);
                    header('Location: /webi');
                    exit();
                } catch (EmailFormatException $e) {
                    $b->build('webi_signup', 'L\'addresse email n\'est pas valide !');
                }
            } else $b->build('webi_signup', 'Les mots de passe ne correspondent pas !');
        } else $b->build('webi_signup', 'Tous les champs doivent être remplis !');
    } else $b->build('webi_signup');
}

function webi_controller_account_reset() {
    $b = new PageBuilder();
    $pass = base64_encode(random_bytes(9));
    $mail = MailManager::message($b->getUser(), "A votre demande, votre mot de passe a été modifié, votre nouveau mot de passe est désormais <strong>$pass</strong>");
    $mail->setSubject('Réinirialisation de votre mot de pass');
    echo $mail;
    exit;
    $b->build('webi_account_reset');
}
