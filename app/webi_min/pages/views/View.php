<?php
namespace webi_min\pages\views;

use webi_min\includes\PageBuilder;

abstract class View
{
    public abstract function html(PageBuilder $builder);
    public abstract function css(PageBuilder $builder);
    public abstract function js(PageBuilder $builder);
}

