<?php
namespace cafetapi\modules\rest;

/**
 *
 * @author damie
 *        
 */
class HttpCodes
{
    /*
     * 2xx Success
     */
    
    /**
     * The request has succeeded
     * @var string
     */
    const HTTP_200 = 'OK';
    /**
     * The request has been fulfilled and resulted in a new resource being created
     * @var string
     */
    const HTTP_201 = 'Created';
    /**
     * The request has been fulfilled but there is no need to return an entity-body
     * @var string
     */
    const HTTP_204 = 'No Content';
    
    
    /*
     * 4xx Client Error
     */
    
    /**
     * Wrong data type, missing data...
     * @var string
     */
    const HTTP_400 = 'Bad Request';
    /**
     * Missing or invalid User-token when needed.
     * <strong>MUST</strong> be return with a WWW-Authenticate header field.
     * @var string
     */
    const HTTP_401 = 'Unauthorized';
    /**
     * The resource is unavailable for the current logged user.
     * @var string
     */
    const HTTP_403 = 'Forbidden';
    /**
     * The ressource cannot be found due to wrong id or malformed URI.
     * Sould be return with a Reason header field.
     * @var string
     */
    const HTTP_404 = 'Resource Not Found';
    /**
     * The resource does not support method with whitch the request was made.
     * <strong>MUST</strong> be return with a Allow header field.
     * @var string
     */
    const HTTP_405 = 'Method Not Allowed';
    /**
     * Existing conflict, usually with ressource id.
     * Sould be return with a Reason header field.
     * @var string
     */
    const HTTP_409 = 'Conflict';
    
    
    /*
     * 5xx Server Error
     */
    
    /**
     * An unexpected error occured while trying to fullfil the request
     * @var string
     */
    const HTTP_500 = 'Internal Server Error';
}

