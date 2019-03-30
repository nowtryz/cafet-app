<?php
namespace webi_min\pages\views;

class CommonHTMLViews
{
    public static function neddLogin() {
?>
<h1 style="text-align:center;color:red">Vous devez être connecté pour accèder à votre profil.</h1>
<?php
    }
    
    public static function forbiden() {
?>
<h1 style="text-align:center;color:red">Vous n\'avez les permissions nécessaires pour accèder à cette page.</h1>
<?php    
    }
}

