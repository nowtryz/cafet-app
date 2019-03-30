<?php
namespace webi_min\pages\controllers;

use webi_min\includes\PageBuilder;

abstract class PageController
{
    protected $builder;
    
    public abstract function buildPage();
    
    public function __construct() {
        $this->builder = new PageBuilder();
    }
}

