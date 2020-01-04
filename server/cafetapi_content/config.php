<?php
/**
 * API config file
 *
 * @package configurations
 * @since API 0.1.0 (2018)
 */
namespace cafetapi\config;

/**
 * Configuration de la base de données.
 * Ne doit être modifié QUE SI LE SERVEUR
 * CHANGE.
 *
 * @author Damien
 *
 * @since API 0.1.0 (2018)
 */
class Database
{

    /**
     * SGBD du serveur de base de données
     *
     * @var string
     * @since API 0.1.0 (2018)
     */
    const driver = 'mysql';

    /**
     * Adresse du serveur
     *
     * @var string
     * @since API 0.1.0 (2018)
     */
    const host = 'localhost';

    /**
     * Port du serveur
     *
     * @var string
     * @since API 0.1.0 (2018)
     */
    const port = '3306';

    /**
     * Nom de la base de données à utiliser
     *
     * @var string
     * @since API 0.1.0 (2018)
     */
    const database = 'mecatronesql';

    /**
     * Nom d'utilisateur, identifiant de connexion
     *
     * @var string
     * @since API 0.1.0 (2018)
     */
    const username = 'mecatro';

    /**
     * Mot de passe
     *
     * @var string
     * @since API 0.1.0 (2018)
     */
    const password = '';
}

/**
 * Configurations du serveur et de l'API.
 *
 * @author Damien
 *
 * @since API 0.1.0 (2018)
 */
class Config
{

    /**
     * Si le serveur serveur doit se comporter en mode debug.
     *
     * Cela signifie afficher les erreurs, autoriser l'accès aux
     * pages de debug et aux phpinfo pour les environements docker
     * @var boolean
     * @since API 0.1.0 (2018)
     */
    const debug = true;

    /**
     * Defini si le serveur doit se comporter comme en production
     * ou en développement
     * @var boolean
     * @since API 0.3.0 (2019)
     */
    const production = false;

    /**
     * Seuil en dessous duquel un mail pour inciter le rechargement est envoyé à chaque achat
     *
     * @var float
     * @since API 0.1.0 (2018)
     */
    const balance_warning = 2.0;

    /**
     * Solde minimum possible sur un compte
     *
     * @var float
     * @since API 0.1.0 (2018)
     */
    const balance_limit = 0.0;

    /**
     * Nom de l'organisation
     *
     * @var string
     * @since API 0.1.0 (2018)
     */
    const organisation = 'Essaim';

    /**
     * Langue du de l'api (=> langue par defaut lors de l'installation des logiciel)
     *
     * @var string
     * @since API 0.1.0 (2018)
     */
    const lang = 'fr_FR';

    /**
     * Hash utilisé pour l'encryption des mots de passes
     *
     * @var string
     * @since API 0.1.0 (2018)
     */
    const hash_algo = 'sha256';

    /**
     * DÉCONSEILLÉ - Salt utilisé pour l'encryption des mots de passe, desormais aléatoire
     * Utilisé pour conserver la compatibilité avec les anciens comptes,
     *
     * @var string
     * @deprecated
     * @since API 0.1.0 (2018)
     */
    const salt = 'ISTYmecatronique';

    /**
     * Adresse URL de l'application
     *
     * @var string
     * @since API 0.1.0 (2018)
     */
    const url = 'http://cafet/';

    /**
     * Si l'installateur se trouve sur un serveur distant
     *
     * @var string
     * @since API 0.1.0 (2018)
     */
    const installer_external = true;

    /**
     * Adresse (relative a la racine de l'API ou lien vers le serveur distant) de l'installateur .exe ou .msi
     *
     * @var string
     * @since API 0.1.0 (2018)
     */
    const installer_url = self::url . 'get/installer.exe';

    /**
     * Adresse (relative a la racine de l'API ou lien vers le serveur distant) de l'installateur .jar
     *
     * @var string
     * @since API 0.1.0 (2018)
     */
    const installer_jar_url = self::url . 'get/installer.jar';

    /**
     * Adresse e-mail utilisée pour l'envoie de mails
     *
     * @var string
     * @since API 0.1.0 (2018)
     */
    const email_sender = 'cafet@exemple.com';

    /**
     * Adresse e-mail utilisée pour le pour le champ reply-to
     *
     * @var string
     * @since API 0.1.0 (2018)
     */
    const email_noreply = 'noreply@exemple.com';

    /**
     * Adresse e-mail de contact de l'organisation
     *
     * @var string
     * @since API 0.1.0 (2018)
     */
    const email_contact = 'contact@exemple.com';

    /**
     * Nom de l'expediteur des mails
     *
     * @var string
     * @since API 0.1.0 (2018)
     */
    const email_name = 'Cafet\' Essaim';

    /**
     * Sujet par defaut des mails
     *
     * @var string
     * @since API 0.1.0 (2018)
     */
    // const email_default_subject = '';

    /**
     * Nom des cookies de session
     *
     * @var string
     * @since API 0.1.0 (2018)
     */
    const session_name = '_cafetapp_' . self::organisation . '_session';

    /**
     * Préférences de reception de mail par défaut pour les utilisateurs
     *
     * @var string
     * @since API 0.3.0 (2019)
     */
    const mail_preferences = [
        // send a mail after each purchase, providing a recap of it
        'payment_notice' => false,
        // send a mail after each acount reload
        'reload_notice' => false,
        // send a mail to warn the user that it sold passed under a threshold, providing a recap of last expenses
        'reload_request' => false,
    ];

    /**
     * Message envoyé par l'API lors d'une déconnexion
     *
     * @var string
     * @since API 0.1.0 (2018)
     */
    const logout_message = 'see you later ;)';

    /**
     * The symbole de la devise utilisée par l'application
     * @var string
     * @since API 0.3.0
     */
    const currency = '€';

    /**
     * Le code de la devise (ISO 4217), ce code est utilisé pour afficher les devises avec le bon format
     * @var string
     * @since API 0.3.0
     */
    const currency_code = 'EUR';
}
