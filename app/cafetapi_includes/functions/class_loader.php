<?php
/**
 * Function file for loader functions
 *
 * @package cafetapi
 * @since API 1.0
 */


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
     * Load all class in a directory and its subfolders.
     * Warning it loads every php files
     *
     * @param string $dir
     *            the directory to analyse
     * @since API 1.0.0 (2018)
     */
    function cafet_load_class_folder(string $dir)
    {
        if (is_dir($dir)) {
            $files = scandir($dir);
            
            foreach ($files as $file) {
                if (is_dir($dir . DIRECTORY_SEPARATOR . $file) && ! in_array($file, ['.', '..']))
                    cafet_load_class_folder($dir . $file . DIRECTORY_SEPARATOR);
                elseif (strpos($file, '.php'))
                require_once $dir . $file;
            }
        }
    }
}