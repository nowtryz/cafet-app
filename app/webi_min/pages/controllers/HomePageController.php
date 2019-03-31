<?php
namespace cafetapi\webi\pages\controllers;

use cafetapi\webi\pages\views\HomePageView;

class HomePageController extends PageController
{
    public function __construct() {
        parent::__construct(new HomePageView());
    }

    public function buildPage()
    {
        $this->builder->build([$this->view, 'html']);
    }
}

