<?php

namespace webi_min\includes;

use cafetapi\user\Perm;
use cafetapi\io\UserManager;
use cafetapi\user\Group;

function controller_manage_users() {
    $b = new PageBuilder();
    
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
    
    if (isset($_POST['Annee']) && isset($_POST['Test'])) {
        $sql = ("SELECT * FROM users where Annee='" . $_POST['Annee'] . "' order by ID");
        $produit = connect($sql);
        while ($element = $produit->fetch()) {
            $adm = "";
            $caf = "";
            $res = "";
            $adc = "";
            $adh = "";
            $val = 0;
            if (isset($_POST['adm' . $element['ID']])) {
                if ($element['admin'] != $_POST['adm' . $element['ID']]) {
                    $adm = "admin=" . $_POST['adm' . $element['ID']] . ",";
                    $val = $val + 10000;
                }
            } else {
                if ($element['admin'] != 0) {
                    $adm = "admin=0,";
                }
            }
            if (isset($_POST['caf' . $element['ID']])) {
                if ($element['cafet'] != $_POST['caf' . $element['ID']]) {
                    $caf = "cafet=" . $_POST['caf' . $element['ID']] . ",";
                    $val = $val + 1000;
                }
            } else {
                if ($element['cafet'] != 0) {
                    $caf = "cafet=0,";
                    $val = $val - 1000;
                }
            }
            if (isset($_POST['res' . $element['ID']])) {
                if ($element['res_cafet'] != $_POST['res' . $element['ID']]) {
                    $res = "res_cafet=" . $_POST['res' . $element['ID']] . ",";
                    $val = $val + 100;
                }
            } else {
                if ($element['res_cafet'] != 0) {
                    $res = "res_cafet=0,";
                    $val = $val - 100;
                }
            }
            if (isset($_POST['adc' . $element['ID']])) {
                if ($element['adm_cafet'] != $_POST['adc' . $element['ID']]) {
                    $adc = "adm_cafet=" . $_POST['adc' . $element['ID']] . ",";
                    $val = $val + 10;
                }
            } else {
                if ($element['adm_cafet'] != 0) {
                    $adc = "adm_cafet=0,";
                    $val = $val - 10;
                }
            }
            if (isset($_POST['adh' . $element['ID']])) {
                if ($element['adherent'] != $_POST['adh' . $element['ID']]) {
                    $adh = "adherent=" . $_POST['adh' . $element['ID']] . ",";
                    $val = $val + 1;
                }
            } else {
                if ($element['adherent'] != 0) {
                    $adh = "adherent=0,";
                    $val = $val - 1;
                }
            }
            if ($adm != "" || $caf != "" || $res != "" || $adc != "" || $adh != "") {
                $sql2 = ("update users set " . $adm . $caf . $res . $adc . $adh . " regkey=0 where ID=" . $element['ID']);
                $produit2 = connect($sql2);
                $sql3 = ("INSERT INTO actions(type, client, valeur, operateur) VALUES(6, " . $val . "," . $element['ID'] . " , " . $_SESSION['ID'] . ")");
                $produit3 = connect($sql3);
            }
        }
        $produit->closeCursor();
    }
    
    $b->build(__NAMESPACE__ . '\manage_users', [
        0 => 'Invités',
        1 => 'Consommateurs',
        2 => 'Gérant cafet\'',
        3 => 'Administrateurs cafet\'',
        4 => 'Administrateurs',
        5 => 'Super utilisateurs'
    ]);
}

function manage_users(PageBuilder $builder, $groups) {
?>
<form method="post" action="/webi/manage/reset-members"><input type="submit" name="Credit" value="Réinitialiser adhésions"></form>
<form method="get" action="/webi/manage">
	<SELECT name="group_id" style="width:150px; font-size:12pt; text-align:center;" >
<?php foreach ($groups as $group_id => $group_name) :?>
		<option value="<?=$group_id?>"<?=$group_id == @$_REQUEST['group_id']? ' selected' : ''?>><?=$group_name?></option>
<?php endforeach;?>
    </SELECT><br/>
	<input type="submit" name="Valider" value="Valider" />
</form>
        
<article id="Accueil">
	<h1>Gestion utilisateurs</h1>
	<form method="post" action="/webi/manage<?=@$_REQUEST['group_id'] ?? '' ?>">
    	<table>
    	  <tr>
    		<th>Personne</th>
    		<th>Adhesion BDE</th>
    		<th>cafet</th>
    		<th>responsable cafet</th>
    		<th>admin cafet</th>
    		<th>Admin</th>
    	  </tr>
<?php
foreach (UserManager::getInstance()->getUsers() as $user) : if (!isset($_REQUEST['group_id'])  || @$user->getGroup()->getId() == $_REQUEST['group_id']) :// FIXME remove @ for $user->getGroup()->getId()?>
		<tr>
			<td width="25%"><?=$user->getPseudo()?></td>
            <td width="15%"><INPUT type="radio" name="<?=$user->getId()?>" value="0" <?=@$user->getGroup()->getId() === 0 ? 'checked' : ''?>></td>
            <td width="15%"><INPUT type="radio" name="<?=$user->getId()?>" value="1" <?=@$user->getGroup()->getId() === 0 ? 'checked' : ''?>></td>
            <td width="15%"><INPUT type="radio" name="<?=$user->getId()?>" value="2" <?=@$user->getGroup()->getId() === 0 ? 'checked' : ''?>></td>
            <td width="15%"><INPUT type="radio" name="<?=$user->getId()?>" value="3" <?=@$user->getGroup()->getId() === 0 ? 'checked' : ''?>></td>
            <td width="15%"><INPUT type="radio" name="<?=$user->getId()?>" value="4" <?=@$user->getGroup()->getId() === 0 ? 'checked' : ''?>></td>
            <td width="15%"><INPUT type="radio" name="<?=$user->getId()?>" value="5" <?=@$user->getGroup()->getId() === 0 ? 'checked' : ''?>></td>
        </tr>
<?php endif; endforeach;?>
		</table>
    	<input type="hidden" name="Annee" id="Annee" value="<?=$year?>">
    	<input type="hidden" name="Test" id="Test" value="1">
    	<input type="submit" name="Valider" value="Valider" />
    </form>
</article>
<?php
}