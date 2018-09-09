<?php
namespace cafetapi\modules\cafet_app;

use cafetapi\exceptions\CafetAPIException;

abstract class Handler
{

    protected $connection;

    protected $user;

    public function __construct()
    {
        global $user;
        if (is_null($user))
            throw new CafetAPIException('no user connected');
        $this->user = $user;
    }
}

