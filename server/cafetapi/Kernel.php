<?php
namespace cafetapi;

class Kernel
{
    protected static $options;
    protected static $autoloader;
    protected static $errors = [];
    
    public static function init() {
        if (file_exists(CONTENT_DIR . 'errors.json')) {
            $file = implode('', file(CONTENT_DIR . 'errors.json'));
            self::$errors = json_decode($file, true) ?: array();
        }
    }
    
    public static function option(string $option) {
        if (!self::$options) {
            self::loadOptions();
        }
        
        return self::$options[$option] ?? null;
    }
    
    protected static function loadOptions() {
        // TODO load options
    }
    
    /**
     * Gives the errors messages of the application
     *
     * @return array an array containing all errors lmessages
     * @since API 0.1.0 (2018)
     */
    public static function errorsInfo(): array
    {
        return self::$errors;
    }
    
    /**
     * Returns the application autoloader
     * @return Autoloader
     */
    public static function getAutoloader() : Autoloader
    {
        return self::$autoloader;
    }

    /**
     * Set the application autoloader
     * @param Autoloader $autoloader
     */
    public static function setAutoloader(Autoloader $autoloader)
    {
        self::$autoloader = $autoloader;
    }

}

