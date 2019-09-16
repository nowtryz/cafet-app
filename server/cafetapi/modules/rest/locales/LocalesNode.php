<?php
namespace cafetapi\modules\rest\locales;

use cafetapi\config\Config;
use cafetapi\modules\rest\HttpCodes;
use cafetapi\modules\rest\Rest;
use cafetapi\modules\rest\RestNode;
use cafetapi\modules\rest\RestResponse;
use cafetapi\modules\rest\errors\ClientError;

/**
 *
 * @author damie
 *
 */
class LocalesNode implements RestNode
{
    
    /**
     * (non-PHPdoc)
     *
     * @see \cafetapi\modules\rest\RestNode::handle()
     */
    public static function handle(Rest $request) : RestResponse
    {
        $langCode = $request->shiftPath();
        $locales = scandir(LANGUAGES_DIR);
        
        if (!$langCode) return LocalesNode::listLocales($request, $locales);
        if (in_array($langCode, $locales)) {
            $dir = $request->shiftPath();
            
            if (!$dir) return LocalesNode::getLocale($request, $langCode);
        }
        
        
    }
    
    private static function listLocales(Rest $request, array $locales) : RestResponse
    {
        $request->allowMethods('GET');
        $localesInformation = [];
        $baseUrl = Config::url . 'api/v' . $request->getVersion() . '/locales/';
        
        foreach ($locales as $locale) {
            $filename = LANGUAGES_DIR . $locale . DIRECTORY_SEPARATOR . 'app.json';
            if (file_exists($filename)) {
                $lang = json_decode(file_get_contents($filename), true);
                $localesInformation[$locale] = [
                    'lang_code' => $lang['lang_code'],
                    'html_lang' => $lang['html_lang'],
                    'url'       => $baseUrl . $locale
                ];
            }
        }
        
        return new RestResponse(200, HttpCodes::HTTP_200, $localesInformation);
    }
    
    private static function getLocale(Rest $request, string $locale) : RestResponse
    {
        $request->allowMethods('GET');
        $filename = LANGUAGES_DIR . $locale . DIRECTORY_SEPARATOR . 'app.json';
        if (!file_exists($filename)) return ClientError::resourceNotFound('Unknown ' . $locale . ' locale');
        else return new RestResponse(200, HttpCodes::HTTP_200, json_decode(file_get_contents($filename), true));
    }
}

