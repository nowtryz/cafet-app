<?php

namespace webi_min\includes;

use cafetapi\data\Client;
use cafetapi\io\ClientManager;
use cafetapi\io\UserManager;
use cafetapi\user\Group;
use cafetapi\user\Perm;
use cafetapi\user\User;

function controller_manage_users() {
    $b = new PageBuilder();
    $b->registerHeadComponents(__NAMESPACE__ . '\manage_users_css');
    $b->registerPostComponents(__NAMESPACE__ . '\manage_users_js');
    $groups = [
        0 => 'Invités',
        1 => 'Consommateurs',
        2 => 'Gérant cafet\'',
        3 => 'Administrateurs cafet\'',
        4 => 'Administrateurs',
        5 => 'Super utilisateurs'
    ];
    
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
    
    if (isset($_REQUEST['userid'])) {
        if (array_key_exists(intval(@$_REQUEST['setgroup']), Group::GROUPS)) {
            $manager = UserManager::getInstance();
            
            if ($user = $manager->getUserById($_REQUEST['userid'])) {
                
                
                if (isset($_REQUEST['setgroup'])) {
                    
                    if ($user->getGroup()->getId() != $_REQUEST['setgroup']) {
                        $manager->setGroup($user->getId(), $_REQUEST['setgroup']);
                        $b->build(__NAMESPACE__ . '\manage_users', $groups, $user->getPseudo() . ' a été mis à jour.');
                    } else $b->build(__NAMESPACE__ . '\manage_users', $groups, $user->getPseudo() . ' est déjà dans le groupe demandé.');
                    
                }  elseif (isset($_REQUEST['member'])) {
                    $clientManager = ClientManager::getInstance();
                    if ( $client = $clientManager->getClient($user->getId()) ) {
                        $clientManager->setMember($client->getId(), boolval($_REQUEST['member']));
                        $b->build(__NAMESPACE__ . '\manage_users', $groups, $user->getPseudo() . ' a été mis à jour.');
                        
                    } else $b->build(__NAMESPACE__ . '\manage_users', $groups, 'Il n\'existe aucun compte client associé avec l\'utilisateur sélectionné.');
                    
                } elseif (isset($_REQUEST['client'])) {
                    $clientManager = ClientManager::getInstance();
                    $client = $clientManager->getClient($user->getId());
                    
                    
                    if ( $client && ! $_REQUEST['client']) {
                        
                        $b->build(__NAMESPACE__ . '\manage_users', $groups, 'Cette action n\'est pas implémentée.');
                        
                        
                    } elseif( !$client && $_REQUEST['client']) {
                        
                        
                        $clientManager->createCustomer($user->getId());
                        $b->build(__NAMESPACE__ . '\manage_users', $groups, $user->getPseudo() . ' a été mis à jour.');
                        
                    } else $b->build(__NAMESPACE__ . '\manage_users', $groups, 'Action impossible.');
                }
                
                
            } else $b->build(__NAMESPACE__ . '\manage_users', $groups, 'Aucun utilisateur avec l\'id ' . $_REQUEST['userid'] . 'n\'a été trouvé.');
        } else $b->build(__NAMESPACE__ . '\manage_users', $groups, 'Il n\'existe aucun groupe avec l\'id ' . $_REQUEST['setgroup'] . '.');
    } else $b->build(__NAMESPACE__ . '\manage_users', $groups);
    
    
}

function manage_users_css() {
?>
<style type="text/css">
    a.validate-checkbox {
        display: block;
    }
    
    .tooltip {
      position: relative;
    }
    
    .tooltip .tooltiptext {
      visibility: hidden;
      width: 120px;
      background-color: black;
      color: #fff;
      text-align: center;
      border-radius: 6px;
      padding: 5px 0;
      position: absolute;
      z-index: 1;
      top: 150%;
      left: 50%;
      margin-left: -60px;
    }
    
    .tooltip .tooltiptext::after {
      content: "";
      position: absolute;
      bottom: 100%;
      left: 50%;
      margin-left: -5px;
      border-width: 5px;
      border-style: solid;
      border-color: transparent transparent black transparent;
    }
    
    .tooltip:hover .tooltiptext {
      visibility: visible;
    }
    
    @keyframes validated {
      0%   {background-color: #40A04000;}
      25%  {background-color: #40A04050;}
      100% {background-color: #40A04000;}
    }
    
    tr.modified_user {
      animation-name: validated;
      animation-duration: 4s;
    }
    
    tr:not(.table-head):hover {
      background: #AAAAAA50
    }
</style>
<?php
}

function manage_users_js() {
?>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js" ></script>
<script type="text/javascript">
	$("input.change-user-info").click(function() {
		window.location = $(this).attr("data-link");
	});
</script>
<?php
}

function set_group_link(User $user, $group_to_set, $group_page = null) {
    return '/webi/manage?' . ($group_page ? 'group_id=' . $group_page . '&' : '') . 'userid=' . $user->getId() . '&setgroup=' . $group_to_set . '#user' . $user->getId();
}

function set_member_link(User $user, ?Client $client, $group_page = null) {
    if ($client) {
        return '/webi/manage?' . ($group_page ? 'group_id=' . $group_page . '&' : '') . 'userid=' . $user->getId() . '&member=' . !$client->isMember() . '#user' . $user->getId();
    }
}

function set_client_link(User $user, ?Client $client, $group_page = null) {
        return '/webi/manage?' . ($group_page ? 'group_id=' . $group_page . '&' : '') . 'userid=' . $user->getId() . '&client=' . !boolval($client) . '#user' . $user->getId();
}

function manage_users(PageBuilder $builder, $groups, string $message = null) {
?>
<form method="post" action="/webi/manage/reset-members"><input type="submit" name="Credit" value="Réinitialiser adhésions"></form><br>
<form method="get" action="/webi/manage">
	<SELECT name="group_id" style="width:150px; font-size:12pt; text-align:center;" >
		<option value=""<?=!isset($_REQUEST['group_id'])? ' selected' : ''?>>TOUS</option>
		<?php foreach ($groups as $group_id => $group_name) :?>
		<option value="<?=$group_id?>"<?=$group_id == @$_REQUEST['group_id']? ' selected' : ''?>><?=$group_name?></option>
		<?php endforeach;?>
    </SELECT><br/>
</form>
        
<article id="Accueil" style="
    width: 97%;
">
	<h1>Gestion utilisateurs</h1>
	<?php if ($message) echo '<p>' . $message . '</p>'?>
	<table>
        <tr class="table-head">
            <th style="padding:3px">Pseudo</th>
            <?php foreach ($groups as $group_id => $group_name) :?>
            <th style="padding:3px"><?=$group_name?></th>
            <?php endforeach;?>
            <th style="padding:3px">Compte Cafet'</th>
            <th style="padding:3px">Adhérent</th>
	  	</tr>
		<?php foreach (UserManager::getInstance()->getUsers() as $user) : if (!@$_REQUEST['group_id']  || $user->getGroup()->getId() == $_REQUEST['group_id']) :?>
		<tr id="user<?=$user->getId() + 10?>" <?=$user->getId() == @$_REQUEST['userid'] ? 'class="modified_user"' : ''?>>
			<td width="20%"><?=$user->getPseudo()?></td>
			<?php foreach ($groups as $group_id => $group_name) :?>
			<td style="text-align: center">
				<a class="validate-checkbox tooltip" href="<?=set_group_link($user, $group_id, @$_REQUEST['group_id'])?>">
					<INPUT class="change-user-info" data-link="<?=set_group_link($user, $group_id, @$_REQUEST['group_id'])?>" type="radio" name="<?=$user->getId()?>" value="<?=$group_id?>" <?=$user->getGroup()->getId() === $group_id ? 'checked' : ''?>>
					<span class="tooltiptext"><?=$group_name?></span>
				</a>
			</td>
			<?php endforeach;?>
			<?php $client = ClientManager::getInstance()->getClient($user->getId()) ?>
			<td style="text-align: center">
				<a class="validate-checkbox tooltip" href="<?=set_client_link($user, $client, @$_REQUEST['group_id'])?>">
					<INPUT class="change-user-info" data-link="<?=set_client_link($user, $client, @$_REQUEST['group_id'])?>" type="checkbox" <?=$client ? 'checked' : ''?>>
					<span class="tooltiptext">Compte Cafet'</span>
				</a>
			</td>
			<td style="text-align: center">
				<a class="validate-checkbox tooltip" href="<?=set_member_link($user, $client, @$_REQUEST['group_id'])?>">
					<INPUT class="change-user-info" data-link="<?=set_member_link($user, $client, @$_REQUEST['group_id'])?>" type="checkbox" <?=$client ? ($client->isMember() ? 'checked' : '') : 'disabled'?>>
					<span class="tooltiptext">Adhérent</span>
				</a>
			</td>
		</tr>
		<?php endif; endforeach;?>
	</table>
</article>
<?php
}