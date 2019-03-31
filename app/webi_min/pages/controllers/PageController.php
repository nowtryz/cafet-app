<?php
namespace cafetapi\webi\pages\controllers;

use cafetapi\webi\Core\PageBuilder;
use cafetapi\webi\pages\views\View;

abstract class PageController
{
    protected $builder;
    protected $view;
    
    public abstract function buildPage();
    
    public function __construct(View $view) {
        $this->builder = new PageBuilder();
        $this->view = $view;
        
        $this->builder->registerHeadComponents([$view, 'css']);
        $this->builder->registerPostComponents([$view, 'js']);
    }
}

