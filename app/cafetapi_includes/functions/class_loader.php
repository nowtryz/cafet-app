<?php
use cafetapi\Autoloader;

/**
 * Function file for loader functions
 *
 * @package cafetapi
 * @since API 1.0
 */

require CLASS_DIR . 'Autoloader.php';

if (! defined('loader_functions_loaded') ) {
    define('loader_functions_loaded', true);
    
    function cafet_class_autoload($class)
    {
        static $classlist = [];
        
        if (! $classlist) {
            cafet_list_classes(CLASS_DIR, $classlist);
        }
        
        $name = $class;
        
        while (strpos($name, '\\') !== false) {
            $name = substr($name, strpos($name, '\\') + 1);
        }
        
        if (array_key_exists($name, $classlist))
            foreach ($classlist[$name] as $file)
                require_once $file;
    }
    
    function cafet_list_classes(string $dir, array &$classlist)
    {
        if (is_dir($dir)) {
            $files = scandir($dir);
            
            foreach ($files as $file) {
                if (is_dir($dir . DIRECTORY_SEPARATOR . $file) && ! in_array($file, ['.', '..'])) {
                    cafet_list_classes($dir . $file . DIRECTORY_SEPARATOR, $classlist);
                }
                elseif (strpos($file, '.php') == (strlen($file) - strlen('.php'))) {
                    $classlist[substr($file, 0, - strlen('.php'))][] = $dir . $file;
                }
            }
        }
    }
    
    /**
     * Register the autoloader and all the namespaces
     *
     * @since API 0.3.0 (2019)
     */
    function cafet_register_classloader()
    {
        $loader = cafet_get_class_autoloader();
        $loader->addNamespace('cafetapi\modules\rest', CLASS_DIR . 'modules' . DIRECTORY_SEPARATOR . 'rest');
        $loader->addNamespace('cafetapi\modules\cafet_app', CLASS_DIR . 'modules' . DIRECTORY_SEPARATOR . 'cafet_app');
        $loader->addNamespace('cafetapi\modules', CLASS_DIR . 'modules');
        $loader->addNamespace('cafetapi\data', CLASS_DIR . 'data');
        $loader->addNamespace('cafetapi\exceptions', CLASS_DIR . 'exceptions');
        $loader->addNamespace('cafetapi\io', CLASS_DIR . 'io');
        $loader->addNamespace('cafetapi\user', CLASS_DIR . 'user');
        $loader->addNamespace('cafetapi', CLASS_DIR);
        $loader->register();
    }
    
    /**
     * Return the Autoloader instance registered on the SPL autoloader
     * @return Autoloader
     * @since API 0.3.0 (2019)
     */
    function cafet_get_class_autoloader() : Autoloader
    {
        static $autoloader;
        
        if (!$autoloader) {
            $autoloader = new Autoloader();
        }
        
        return $autoloader;
    }
}