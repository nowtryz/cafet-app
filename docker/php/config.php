<?php

namespace cafetapi\config;

class Database
{
    const driver = 'mysql';
    const host = 'db';
    const port = '3306';
    const database = 'mecatronesql';
    const username = 'mecatro';
    const password = 'mecatro';
}

class Config
{
    const debug = true;
    const balance_warning = 2.0;
    const balance_limit = 0.0;
    const organisation = 'Essaim';
    const lang = 'fr_FR';
    const hash_algo = 'sha256';
    const salt = 'ISTYmecatronique';
    const url = 'http://cafet/';
    const installer_external = true;
    const installer_url = self::url . 'get/installer.exe';
    const installer_jar_url = self::url . 'get/installer.jar';
    const email_sender = 'cafet@bde-apps.isty.uvsq.fr';
    const email_noreply = 'noreply@bde-apps.isty.uvsq.fr';
    const email_contact = 'contact@bde-apps.isty.uvsq.fr';
    const email_name = 'Cafet\' Essaim';
    // const email_default_subject = '';
    const session_name = '_cafetapp_' . self::organisation . '_session';
    const mail_preferences = [
        'payment_notice' => false,
        'reload_notice' => false,
        'reload_request' => true,
    ];
    const logout_message = 'see you later ;)';
}
