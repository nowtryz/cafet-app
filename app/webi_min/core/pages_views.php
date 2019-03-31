<?php

namespace cafetapi\webi\Core;

use cafetapi\io\ClientManager;

function show_profile(PageBuilder $builder)
{
    $user = $builder->getUser();
?>
<article id="Accueil">
	<h1><?=$user->getPseudo()?></h1>
	<section style="text-align: center">
		<form style="display: inline-block; margin: 10px;" method="post"
			action="/webi/me/edit">
			<input type="submit" value="Modifier le profil" />
		</form>
		<form style="display: inline-block; margin: 10px;" method="post"
			action="/webi/me/edit/pwd">
			<input type="submit" value="Modifier le mot de passe" />
		</form>
	</section>
	</a>
    Nom : <?=$user->getFamilyName()?><br /> <br />
    Prénom : <?=$user->getFirstname()?><br /> <br />
    Promo : inconnue<br /> <br />
    Mail : <?=$user->getEmail()?><br /> <br />
    Tel : <?=$user->getPhone()?></p>
    <?php if ($client = ClientManager::getInstance()->getClient($user->getId())) :?>
    <br />
    Crédit : <?=$client->getBalance()?>€</p>
    <?php endif;?>
</article>
<?php
}

function edit_profile(PageBuilder $builder) {
?>
<article>
	<form method="post" action="/webi/me/edit" id="Ajout" enctype="multipart/form-data">

		<p>
		<label for="name">Nom :</label>
		<input type="text" name="name" id="Nom" value="<?=$builder->getUser()->getFamilyName()?>"/><br/>
		
		<p>
		<label for="firstname">Prénom :</label>
		<input type="text" name="firstname" id="Prenom" value="<?=$builder->getUser()->getFirstname()?>"/><br/>
		
		<p>
		<label for="phone">Tel :</label>
		<input type="text" name="phone" id="Tel" value="<?=$builder->getUser()->getPhone()?>"/><br/>

		<p><input type="submit" value="Envoyer" /></p>
	</form>
	
</article>
<?php
}

function edit_password(PageBuilder $builder, $message = null) {
?>


<article>
	<?php if ($message) echo '<p>' . $message . '</p>'?>

	<form method="post" action="/webi/me/edit/pwd" id="Ajout" enctype="multipart/form-data">

	<label for="old_MDP">Ancien mot de passe :</label>
	<input type="password" name="old_MDP" id="old_MDP" required /><br/>
	
	<p>
	<label for="MDP">Mot de passe :</label>
	<input type="password" name="MDP" id="MDP" required /><br/></p>
	
	<p>
	<label for="MDP2">Confirmer mot de passe :</label>
	<input type="password" name="MDP2" id="MDP2" required /><br/></p>

	<p>
	<input type="submit" value="Envoyer" /></p>
	</form>
	
</article>
<?php
}

function signin() {
?>
<article>
	<h1>Connexion</h1>
	<?php if(isset($_POST['Pseudo'])) echo '<p>Identifiants incorects</p>';?>
	<form method="post" action="/webi/signin" id="Connexion">
	<p>
	<label for="Pseudo">pseudo:</label>
	<input type="text" name="Pseudo" id="Pseudo" required /><br/>
	
	<label for="MDP">Mot de passe :</label>
	<input type="password" name="MDP" id="MDP" required/><br/>
	<input type="submit" value="Envoyer" /></p>
	</form><br />		
	<a href="/webi/account/reset" onclick="nouvelleFenetre(this.href);return false;" style="text-decoration:none; color:#181818;" align="right" target=_blank>Mot de passe perdu</a>	
</article>
<?php
}

function signup(PageBuilder $builder, $message = null) {
?>
<article id="Inscription">
	<h1>Inscription</h1><br/>
	
	<?php if ($message) echo '<p>' . $message . '</p>' ?>
	
	<form method="post" autocomplete="off" action="/webi/signup">
	<p>
	<label for="Pseudo">Pseudo :</label>
	<input type="text" name="Pseudo" id="Pseudo" required /><br/>

	<p>
	<label for="Prenom">Prénom :</label>
	<input type="text" name="Prenom" id="Prenom" required /><br/>
	
	<p>
	<label for="Nom" autocomplete="off">Nom :</label>
	<input type="text" name="Nom" id="Nom" required /><br/>

	<p>
	<label for="Email">Adresse Email :</label>
	<input type="email" name="Email" id="Email" required /><br/>
	
	 <p>
	<label for="MDP">Mot de passe :</label>
	<input type="password" name="MDP" id="MDP" required /><br/>
	
	<p>
	<label for="MDP2">Confirmer mot de passe :</label>
	<input type="password" name="MDP2" id="MDP2" required /><br/>
	</p>
	<input type="submit" value="Envoyer" />
	</form><br />
	
</article>
<?php
}

function account_reset(PageBuilder $builder, $message = null) {
?>
<article>
	<h1>Demande de mot de passe</h1>
	<br />
	
	<?php if ($message) echo '<p>' . $message . '</p>' ?>

	<form method="get" action="/webi/account/reset" id="Ajout">

		<p>
			<label for="mail">Mail:</label> <input type="text" name="mail"
				id="Mail" required />

		</p>
		<p>
			<input type="submit" value="Envoyer" />
		</p>

	</form>
	<br />

</article>
<?php
}

function members_reset() {
?>
<h1 style="text-align:center;color:green">Toutes les adhésions ont été réinitialisées.</h1>
<?php
}

function need_signin() {
?>
<h1 style="text-align:center;color:red">Vous devez être connecté pour accèder à votre profil.</h1>
<?php
}
