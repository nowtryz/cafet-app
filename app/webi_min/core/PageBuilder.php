<?php
namespace cafetapi\webi\Core;

use cafetapi\user\Perm;
use cafetapi\user\User;
use cafetapi\config\Config;

class PageBuilder
{

    private $header = [
        self::class,
        'header'
    ];

    private $footer = [
        self::class,
        'footer'
    ];

    private $nav = [
        self::class,
        'nav'
    ];

    private $head = [
        self::class,
        'head'
    ];

    private $post_page = [
        [
            self::class,
            'post'
        ]
    ];
    
    private $headComponents = [];

    private $title = null;

    private $user = null;

    public function __construct()
    {
        if (isset($_COOKIE[Config::session_name])) {
            if (!isset($_SESSION)) cafet_init_session();
            $this->user = cafet_get_logged_user();
        }
    }

    public function build(callable $body, ...$args)
    {
        array_unshift($args, $this);
?>
<!DOCTYPE html>
<html lang="fr">
    <?php call_user_func($this->head, $this)?>
    <body>
    	<div id="bloc_page">
            <?php call_user_func($this->header, $this)?>
            <section>
            	<?php call_user_func_array($body, $args) ?>
            </section>
            <?php call_user_func($this->footer, $this)?>
        </div>
    </body>
</html>
<?php

        foreach ($this->post_page as $callable) {
            call_user_func($callable, $this);
        }
    }

    /**
     *
     * @return multitype:string
     */
    public function getHeader(): callable
    {
        return $this->header;
    }

    /**
     *
     * @return multitype:string
     */
    public function getFooter(): callable
    {
        return $this->footer;
    }

    /**
     *
     * @return multitype:string
     */
    public function getNav(): callable
    {
        return $this->nav;
    }

    /**
     *
     * @return multitype:string
     */
    public function getHead(): callable
    {
        return $this->head;
    }

    /**
     *
     * @return mixed
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     *
     * @return mixed
     */
    public function getUser(): ?User
    {
        return $this->user;
    }
    
    /**
     *
     * @return mixed
     */
    public function getHeadComponents(): array
    {
        return $this->headComponents;
    }

    /**
     *
     * @param multitype:string $header
     */
    public function setHeader(callable $header)
    {
        $this->header = $header;
    }

    /**
     *
     * @param multitype:string $footer
     */
    public function setFooter(callable $footer)
    {
        $this->footer = $footer;
    }

    /**
     *
     * @param multitype:string $nav
     */
    public function setNav(callable $nav)
    {
        $this->nav = $nav;
    }

    /**
     *
     * @param multitype:string $head
     */
    public function setHead(callable $head)
    {
        $this->head = $head;
    }

    /**
     *
     * @param mixed $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     *
     * @param mixed $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }
    
    public function registerHeadComponents(callable $component) {
        $this->headComponents[] = $component;
    }
    
    public function registerPostComponents(callable $component) {
        $this->post_page[] = $component;
    }

    public static function head(PageBuilder $builder)
    {
        ?>
<head>
<meta charset="utf-8" />
<link rel="stylesheet" href="/webi_min/style.css" />
<?php foreach ($builder->getHeadComponents() as $component) call_user_func($component, $builder)?>
<title><?=$builder->title ? $builder->title . ' | ' : ''?>Site du BDE Isty M&eacute;catronique</title>
<meta http-equiv="Content-Type" content="text/html;charset=iso-8859-1" />
<link rel="shortcut icon" href="/webi_min/images/favicon.ico"
	type="image/x-icon" />
<link rel="icon" href="/webi_min/images/favicon.ico" type="image/x-icon" />
</head>
<?php
    }

    public static function header(PageBuilder $builder)
    {
        ?>
<header>
	<div id="titre_principal">
		<a href="/webi"> <img src="/webi_min/images/Logo.png"
			style="margin-top: 20px" alt="Logo de l'ISTY" id="logo" /></a> <br />
		<br /> <br /> <br /> <br /> <br /> <br /> <br /> <br /> <br />
    	<?php if ($builder->user): ?>
    		Vous êtes connecté
    		<a href='/webi/me' style="color: #181818;"><?=$builder->user->getPseudo()?></a>
		<h3>
			<?php if ($builder->user->hasPermission(Perm::SITE_MANAGE)): ?><a
				href="/webi/manage" style="text-decoration: none; color: #181818;"
				align="right">Gestion</a> - <?php endif;?>
			<a href="/webi/signout"
				style="text-decoration: none; color: #181818;" align="right">Deconnexion</a>
		</h3>
    	<?php else: ?>
    		<h3>
			<a href="/webi/signin" style="text-decoration: none; color: #181818;">Connexion</a>
			- <a href="/webi/signup"
				style="text-decoration: none; color: #181818;">Inscription</a>
		</h3>
    	<?php endif; ?>
	
</div>
<?php call_user_func($builder->nav, $builder)?>
</header>

<?php
    }

    public static function nav(PageBuilder $builder)
    {
        
    }

    public static function footer(PageBuilder $builder)
    {
        ?>
<footer>
	<section>
		<div id="Version">
			<h1>Version 4.0</h1>
		</div>
		<div id="Site_du_BDE">
			<h1>Site de l'ESSAIM</h1>
		</div>
		<div id="Questions_Bug">
			<h1>
				<a href="mailto:essaim@gmail.com" style="text-decoration: none">Questions/Bug</a>
			</h1>
		</div>
	</section>
	<section style="text-align: center">
		<a
			style="font-size: 1em; color: #990000; text-decoration: none; text-align: center;"
			href="https://www.facebook.com/essaim.isty" target="_blank">Retrouvez
			nous sur Facebook !!!</a>
	</section>
</footer>
<?php
    }

    public static function post()
    {
        ?>
<SCRIPT language="Javascript">
	//au chargement de la page, on appelle la fonction montre()
window.onload=montre;
 
//affichage du menu déroulant et placement de ce dernier
function montre(id,affiche)
{
	var d = document.getElementById(id);
	//si on quitte un élément du menu
	if (d && !affiche) 
	{
		d.style.display='none'; //on l'efface
		var c=d.parentNode; //son parent
	}
	//sinon si on se mets sur un élément du menu
	else if (d && affiche)
	{ 
		d.style.display='block'; //on l'affiche
		var c=d.parentNode; //son parent
	}
}
	
</SCRIPT>
<?php
    }
}

