<?php

use cafetapi\ErrorPageBuilder;

/**
 * Default entry for the application
 * @package essaim_cafet
 */
/*
 * Created by
 *  _   _____
 * ( ) (  _  )       ____    __    __  __  ____  ____  _  _
 * | |_| | | |      (  _ \  /__\  (  \/  )(_  _)( ___)( \( )
 * (_____) (_)       )(_) )/(__)\  )    (  _)(_  )__)  )  (
 *  _________       (____/(__)(__)(_/\/\_)(____)(____)(_)\_)
 * (___    __)
 *  __/ __ \_
 * (___/  \__)
 *  _________
 * (____  _  )       ____________            _____           ____            ____   ____        ______   ______   ______   _____
 *  ___| |_| |       \           \          |\    \_     ____\_  \__        /    \_/    \       \     \ |\     \ |\     \ |     |
 * (_________)        \           \         \ \     \   /     /     \      /    _   _    \       |     | \|     |\ \     \|     |
 *      _____          |    /\     |         \|      | /     /\      |    /    //   \\    \      |     | /____ /  \ \           |
 *  ___/  ___)         |   |  |    |          |      ||     |  |     |   /    //     \\    \     |     ||\     \   \ \____      |
 * (___  (___          |    \/     |  ______  |      ||     |  |     |  /     \\_____//     \    |     | \|     |   \|___/     /|
 *     \_____)        /           /| /     / /      /||     | /     /| /       \ ___ /       \   |     |  |     |       /     / |
 *  _________        /___________/ ||      |/______/ ||\     \_____/ |/________/|   |\________\ /_____/| /_____/|      /_____/  /
 * (  _____  )      |           | / |\_____\      | / | \_____\   | /|        | |   | |        ||    | ||     | |      |     | /
 * | |_____| |      |___________|/  | |     |_____|/   \ |    |___|/ |________|/     \|________||____|/ |_____|/       |_____|/ 2018
 * (_________)                       \|_____|           \|____|
 *          _
 *  _______| |      ###########################################     */  require 'cafetapi_load.php';
/* (_______  |      ###########################################
 *         |_|      ##                                       ##     */  cafet_listen_app_request();
/*  _______ _       ##  Salutation à toi qui a le courage de ##
 * (_______(_)      ##  vouloir lire mon code !              ##
 *  ____  ___       ##  un retour ? damien.djmb@gmail.com    ##
 * (__  \/  _)      ##  ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~  ##
 *  __)    (_       ###########################################
 * (____/\___)      ###########################################
 */

/**
 * URL adressr of the current script
 *
 * @var string
 * @since API 0.1.0 (2018)
 */
define('URL_LOCATION', substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], '/')));

function echo_page() {
    // DEBUG
    if (URL_REWRITE) {
        if (isset($_GET['path']) && $_GET['module'] == 'debug') {
            if ($_GET['path'] == 'infos') {
                require PAGES_DIR . 'debug.php';
                exit();
            } elseif ($_GET['path'] == 'form') {
                require PAGES_DIR . 'form.html';
                exit();
            } else {
                cafet_http_error(404);
                (new ErrorPageBuilder(404))->print();
            }
        } else {
            cafet_http_error(404);
            (new ErrorPageBuilder(404))->print();
        }
    } else {
        // work on $_SERVER['REQUEST_URI']
    }
}

function echo_index() {
    echo 'nothing to show now<br/><a href="' . URL_LOCATION . '/debug/infos.html">DEBUG</a><br/><a href="' . URL_LOCATION . '/debug/form.html">Request generator</a>';
}

if(cafet_get_configurations()['debug']) {
    if(isset($_GET['module'])) echo_page();
    else                       echo_index();
} else (new ErrorPageBuilder(403))->print();
