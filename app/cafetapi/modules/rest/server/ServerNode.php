<?php
namespace cafetapi\modules\rest\server;

use cafetapi\modules\rest\RestNode;

/**
 *
 * @author damie
 *        
 */
class ServerNode implements RestNode
{

    /**
     * (non-PHPdoc)
     *
     * @see \cafetapi\modules\rest\RestNode::handle()
     */
    protected function handle(array $path, array $body, string $method, array $headers)
    {}
}

