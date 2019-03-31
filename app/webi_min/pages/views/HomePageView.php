<?php
namespace cafetapi\webi\pages\views;

use cafetapi\io\ProductManager;
use cafetapi\webi\Core\PageBuilder;

class HomePageView extends View
{

    public function css(PageBuilder $builder)
    {
?>
<style>
.product-list * {
	color: #990000;
}

.product-list {
	background: white;
	width: 630px;
	margin: 25px auto;
	padding: 20px;
	padding-top: 10px;
	font-family: Comic Sans MS;
	font-size: 20px
}

.product-list h2 {
	text-align: center;
	margin: 0;
	font-size: 45px;
	font-style: italic;
}

.product-list h3 {
    margin: 20px 0;
	font-size: 27px;
	font-style: italic;
	text-decoration: underline;
}


.product-list li {
    display: list-item;
}

.product-list ul.leaders {
	padding: 0;
	overflow-x: hidden;
	list-style: none;
	width: 80%;
	margin: auto;
}

.product-list ul.leaders li:before {
	float: left;
	width: 0;
	white-space: nowrap;
	content: ". . . . . . . . . . . . . . . . . . . . "
		". . . . . . . . . . . . . . . . . . . . "
		". . . . . . . . . . . . . . . . . . . . "
		". . . . . . . . . . . . . . . . . . . . "
}

.product-list ul.leaders span:first-child {
	padding-right: 0.33em;
	background: white
}

.product-list ul.leaders span+span {
	float: right;
	padding-left: 0.33em;
	background: white
}
</style>
<?php
    }

    public function js(PageBuilder $builder)
    {}

    public function html(PageBuilder $builder)
    {
?>
<article>
	<h1>La Cafet</h1>

	<br/>

	<p> A chaque pause et tous les jours, nous vendons aux étudiants ainsi qu'aux enseignants des boissons fraiches, chaudes, et des confiseries. N'hésitez pas à venir nous voir
	 au deuxième étage du bâtiment de l'ISTY à Mantes la ville !<p>

	<div class="product-list">
		<h2>Cafétaria</h2>
<?php foreach(ProductManager::getInstance()->getProductGroups() as $group) :?>

			<h3><?=$group->getName()?> :</h3>
			<ul class="leaders">
<?php foreach ($group->getAllProducts() as $product) : if ($product->getViewable()) : ?>
				<li>
					<span><?=$product->getName()?></span>
					<span><?=number_format($product->getPrice(), 2, ',', ' ')?> €</span>
				</li>
<?php endif; endforeach;?>
			</ul>
		<?php endforeach;?>
	</div>
	
</article>
<?php
    }
}

