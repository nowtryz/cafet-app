<?php
namespace webi_min\pages\views;

use cafetapi\data\Client;
use cafetapi\io\ClientManager;
use cafetapi\io\UserManager;
use cafetapi\user\User;
use webi_min\includes\PageBuilder;
use function webi_min\pages\views\ManageUsers\set_client_link;
use function webi_min\pages\views\ManageUsers\set_group_link;
use function webi_min\pages\views\ManageUsers\set_member_link;
use cafetapi\config\Defaults;

require_once WEBI_PAGE_VIEWS . 'View.php';
require_once WEBI_PAGE_VIEWS . 'MessageHolder.php';

class ManageUsersView extends View implements MessageHolder
{
    private $message;
    private $groups;
    
    public function __construct(array $groups) {
        $this->groups = $groups;
    }
    
    public function getMessage(): string
    {
        return $this->message;
    }
    
    public function setMessage(string $message)
    {
        $this->message = $message;
    }
    
    private function generateLink($page, $params, $anchor = null, $use_current_params = true) {
        $host = Defaults::url;
        $chains = [];
        
        if ($use_current_params) $params = array_merge($_GET, $params);
        
        foreach ($params as $key => $value) $chains[] = $key . '=' . $value;
        
        $param_chain = implode('&', $chains);
        
        return $host . $page . ($param_chain ? '?' . $param_chain : '') . ($anchor ? '#' . $anchor : '');
    }
    
    private function set_group_link(User $user, $group_to_set, $group_page = null)
    {
        $group = @$_REQUEST['group_id'] ? 'group_id=' . $_REQUEST['group_id'] . '&' : '';
        $uid = $user->getId();
        return "/webi/manage?${group}userid=${uid}&setgroup=$group_to_set#user${uid}";
    }
    
    private function set_member_link(User $user, ?Client $client, $group_page = null)
    {
        if ($client) {
            $group = @$_REQUEST['group_id'] ? 'group_id=' . $_REQUEST['group_id'] . '&' : '';
            $uid = $user->getId();
            $member = ! $client->isMember();
            return "/webi/manage?${group}userid=${uid}&member=${member}#user${uid}";
        }
    }
    
    private function set_client_link(User $user, ?Client $client, $group_page = null)
    {
        $group = @$_REQUEST['group_id'] ? 'group_id=' . $_REQUEST['group_id'] . '&' : '';
        $uid = $user->getId();
        $status = ! boolval($client);
        return "/webi/manage?${group}userid=${uid}&client=${status}#user${uid}";
    }
    
    
    

    public function css(PageBuilder $builder)
    {
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
    0% { background-color: #40A04000; }
    25% { background-color: #40A04050; }
    100% { background-color: #40A04000; }
}

tr.modified_user {
	animation-name: validated;
	animation-duration: 4s;
}

tr:not(.table-head ):hover {
	background: #AAAAAA50
}
</style>
<?php
    }
    
    
    

    public function js(PageBuilder $builder)
    {
        ?>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script type="text/javascript">
	$("input.change-user-info").click(function() {
		window.location = $(this).attr("data-link");
	});

	$("#reset-members").submit(function(e) {
		if(!confirm("Voulez-vous vraiment réinitialiser toutes les adhésions ?")) e.preventDefault();
	});
</script>
<?php
    }
    
    
    

    public function html(PageBuilder $builder)
    {
        ?>
<form method="post" action="/webi/manage/reset-members"
	id="reset-members">
	<input type="submit" name="Credit" value="Réinitialiser adhésions">
</form>
<br>
<form method="get" action="/webi/manage">
	<SELECT name="group_id"
		style="width: 150px; font-size: 12pt; text-align: center;">
		<option value="" <?=!isset($_REQUEST['group_id'])? ' selected' : ''?>>TOUS</option>
		<?php foreach ($this->groups as $group_id => $group_name) :?>
		<option value="<?=$group_id?>"
			<?=$group_id == @$_REQUEST['group_id']? ' selected' : ''?>><?=$group_name?></option>
		<?php endforeach;?>
    </SELECT><br /> <input type="submit" value="Valider">
</form>

<article id="Accueil" style="width: 97%;">
	<h1>Gestion utilisateurs</h1>
	<?php if ($this->message) echo '<p>' . $this->message . '</p>'?>
	<table>
		<tr class="table-head">
			<th style="padding: 3px">Pseudo</th>
            <?php foreach ($this->groups as $group_id => $group_name) :?>
            <th style="padding: 3px"><?=$group_name?></th>
            <?php endforeach;?>
            <th style="padding: 3px">Compte Cafet'</th>
			<th style="padding: 3px">Adhérent</th>
		</tr>
		<?php foreach (UserManager::getInstance()->getUsers() as $user) : if (!@$_REQUEST['group_id']  || $user->getGroup()->getId() == $_REQUEST['group_id']) :?>
		<tr id="user<?=$user->getId() + 10?>"
			<?=$user->getId() == @$_REQUEST['userid'] ? 'class="modified_user"' : ''?>>
			<td class="tooltip">
    			<span><?=$user->getFirstname()?> <?=$user->getFamilyName()?></span>
    			<span class="tooltiptext"><?=$user->getPseudo()?> #<?=$user->getId()?></span>
			</td>
			<?php foreach ($this->groups as $group_id => $group_name) :?>
			<td style="text-align: center">
    			<a class="validate-checkbox tooltip" href="<?=$this->set_group_link($user, $group_id)?>">
					<input class="change-user-info"
					data-link="<?=$this->set_group_link($user, $group_id)?>"
					type="radio" name="<?=$user->getId()?>" value="<?=$group_id?>"
					<?=$user->getGroup()->getId() === $group_id ? 'checked' : ''?>>
					<span class="tooltiptext"><?=$group_name?></span>
    			</a>
			</td>
			<?php endforeach;?>
			<?php $client = ClientManager::getInstance()->getClient($user->getId()) ?>
			<td style="text-align: center">
    			<a class="validate-checkbox tooltip" href="<?=$this->set_client_link($user, $client)?>">
					<input class="change-user-info"
					data-link="<?=$this->set_client_link($user, $client)?>"
					type="checkbox" <?=$client ? 'checked' : ''?>>
					<span class="tooltiptext">Compte Cafet'</span>
    			</a>
			</td>
			<td style="text-align: center"><a class="validate-checkbox tooltip"
				href="<?=$this->set_member_link($user, $client)?>">
					<input class="change-user-info"
					data-link="<?=$this->set_member_link($user, $client)?>"
					type="checkbox"
					<?=$client ? ($client->isMember() ? 'checked' : '') : 'disabled'?>>
					<span class="tooltiptext">Adhérent</span>
			</a></td>
		</tr>
		<?php endif; endforeach;?>
	</table>
</article>
<?php
    }

}

