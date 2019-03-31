<?php
namespace cafetapi\webi\pages\views;

use cafetapi\webi\Core\PageBuilder;

abstract class View
{
    public abstract function html(PageBuilder $builder);
    public abstract function css(PageBuilder $builder);
    public abstract function js(PageBuilder $builder);
}

