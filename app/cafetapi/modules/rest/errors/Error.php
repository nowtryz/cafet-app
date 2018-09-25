<?php
namespace cafetapi\modules\rest\errors;

/**
 *
 * @author damie
 *        
 */
class Error
{
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
     * @var string
     */
    const HTTP_401 = 'Unauthorized';
    /**
     * The resource is unavailable for the current logged user.
     * <br/><strong>MUST</strong> be return with a WWW-Authenticate header field.
     * @var string
     */
    const HTTP_403 = 'Forbidden';
    /**
     * The ressource cannot be found due to wrong id or malformed URI.
     * <br/>Sould be return with a Reason header field.
     * @var string
     */
    const HTTP_404 = 'Resource Not Found';
    /**
     * The resource does not support method with whitch the request was made.
     * <br/><strong>MUST</strong> be return with a Allow header field.
     * @var string
     */
    const HTTP_405 = 'Method Not Allowed';
    /**
     * Existing conflict, usually with ressource id.
     * <br/>Sould be return with a Reason header field.
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

