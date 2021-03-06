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

class Defaults
{
    const debug = true;
    const balance_warning = 2.0;
    const balance_limit = 0.0;
    const organisation = 'Organisation name';
    const lang = 'fr_FR';
    const hash_algo = 'sha256';
    /** @deprecated */
    const salt = 'aKindOfSaltThatShouldBeARandomString';
    const url = 'http://cafet.exemple.com';
    const installer_external = true;
    const installer_url = self::url . 'get/installer.exe';
    const installer_jar_url = self::url . 'get/installer.jar';
    const email_sender = 'cafet@exemple.com';
    const email_noreply = 'noreply@exemple.com';
    const email_contact = 'contact@exemple.com';
    const email_name = 'Cafet\' Essaim';
    // const email_default_subject = '';
    const session_name = '_cafetapp_' . self::organisation . '_session';
    const logout_message = 'see you later ;)';
}
