<?php
// init session if asked
if (isset($_GET['session']) && $_GET['session'])
    cafet_init_session(true, $_GET['session']);

// form to show an image
$products = cafetapi\io\DataFetcher::getInstance()->getProducts(true);
?>
<h2>Show product image</h2>

<form action="../image/product" method="get" target="_blank">
	<label for="id">Product's id: </label><input type="number" name="id"
		id="id" required /> <input type="checkbox" name="dl" id="dl-id" /><label
		for="dl-id">Download</label> <input type="submit" value="Send" />
</form>

<form action="../image/product" method="get" target="_blank">
	<label for="id">Product's name: </label>

<?php
echo '  <select name="id" id="id" required/>' . "\n\n";
foreach ($products as $p)
    echo '        <option value="' . $p->getId() . '">' . $p->getName() . '</option>' . "\n";
echo "\n" . '		<option value=0 selected> -- SELECT --</option>' . "\n\n" . '	</select>';
?>

	<input type="checkbox" name="dl" id="dl-name" /><label for="dl-name">Download</label>
	<input type="submit" value="Send" />
</form>

<h2>SESSION:</h2>

<?php
// send sesion id form
?>
<form action="" method="get">
	<label for="session">Session: </label><input type="text" name="session"
		id="session"
		value="<?php echo isset($_GET['session']) && $_GET['session'] ? $_GET['session'] : '' ?>"
		required /> <input type="submit" value="Send" />
</form>

<?php

// dump server vars
cafet_dump_server_vars();