<?php
namespace cafetapi\io;

class OptionManager extends DatabaseConnection
{
    /**
     * Returns every configuration stored in the database
     *
     * @return array - an array of key-value
     * @since API 0.1.0 (2018)
     */
    public static final function getConfigurations(): array
    {
        $conf = [];
        $statement = DatabaseConnection::getDatabaseConnectionInstance()->getPDOObject()->query('SELECT name, value FROM ' . self::CONFIG . ' ORDER BY name');
        
        while ($data = $statement->fetch()) {
            $conf[$data['name']] = $data['value'];
        }
        
        $statement->closeCursor();
        
        return $conf;
    }
    
    /**
     * Return the configuration value stored in the database for the give key
     *
     * @param String $name
     *            the key
     * @return string the value
     * @since API 0.1.0 (2018)
     */
    public static final function getConfiguration(String $name): string
    {
        $statement = DatabaseConnection::getDatabaseConnectionInstance()->getPDOObject()->prepare('SELECT value FROM ' . self::CONFIG . ' WHERE name = :name ORDER BY edit DESK LIMIT 1');
        $statement->execute([
            'name' => $name
        ]);
        
        $conf = null;
        if ($data = $statement->fetch()) $conf = $data['value'];
        $statement->closeCursor();
        return $conf;
    }
}

