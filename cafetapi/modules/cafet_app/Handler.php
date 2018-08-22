<?php
namespace cafetapi\modules\cafet_app;

use cafetapi\io\DatabaseConnection;
use cafetapi\exceptions\CafetAPIException;

abstract class Handler
{

    protected $connection;

    protected $user;

    public function __construct(string $manager)
    {
        global $user;
        if (is_null($user))
            throw new CafetAPIException('no user connected');
        $this->user = $user;

        $connection = new $manager(DB_INFO);

        if ($connection instanceof DatabaseConnection)
            $this->connection = $connection;
        else {
            $this->connection = new DatabaseConnection(DB_INFO);
            cafet_log('unable to create a ' . $manager . ' object as a database connection');
        }
    }
}

