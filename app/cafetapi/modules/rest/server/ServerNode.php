<?php
namespace cafetapi\modules\rest\server;

use cafetapi\modules\rest\Rest;
use cafetapi\modules\rest\RestNode;
use cafetapi\modules\rest\errors\ClientError;

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
    public function handle(Rest $request)
    {
        return ClientError::imATeapot();
    }
}

