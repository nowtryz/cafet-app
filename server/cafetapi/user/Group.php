<?php
namespace cafetapi\user;

use cafetapi\data\Data;
use cafetapi\data\JSONParsable;

/**
 *
 * @author Damien
 *        
 */
class Group extends JSONParsable implements Permissible, Data, \Serializable
{
    const GUEST = [
        Perm::GLOBAL_CONNECT => true,
        Perm::CAFET_ADMIN_GET_PRODUCTS => true,
        Perm::CAFET_ADMIN_GET_FORMULAS => true
    ];

    const SUPER_USER = [
        Perm::ALL => true
    ];

    const ADMIN = [
        Perm::ALL => true,
        Perm::GLOBAL_HIDDEN => false,
        Perm::GLOBAL_UNTOUCHABLE => false
    ];

    const CAFET_ADMIN = [
        Perm::GLOBAL_CONNECT => true,
        Perm::CAFET => true
    ];

    const CAFET_MANAGER = [
        Perm::GLOBAL_CONNECT => true,
        Perm::CAFET_ADMIN_PANELACCESS => true,
        Perm::CAFET_ADMIN_ORDER => true,
        Perm::CAFET_ADMIN_RELOAD => true,
        Perm::CAFET_ADMIN_STATS => true,
        Perm::CAFET_ADMIN_GET => true,
        Perm::CAFET_PURCHASE => true,
        Perm::CAFET_ME => true
    ];

    const CONSUMER = [
        Perm::GLOBAL_CONNECT => true,
        Perm::CAFET_PURCHASE => true,
        Perm::CAFET_ME => true
    ];
    
    const GROUPS = [
        0 => self::GUEST,
        1 => self::CONSUMER,
        2 => self::CAFET_MANAGER,
        3 => self::CAFET_ADMIN,
        4 => self::ADMIN,
        5 => self::SUPER_USER
    ];

    private $name;
    private $id;

    private $permissions = [];

    /**
     *
     * @param string $name
     * @param array $permissions
     * @since API 0.1.0 (2018)
     */
    public function __construct(string $name, array $permissions, int $id = 0)
    {
        $this->name = $name;
        $this->id = $id;

        if (is_associative_array($permissions))
            foreach ($permissions as $permission => $value)
                if (is_string($permission))
                    $this->permissions[$permission] = (bool) $value;
    }

    public function hasPermission(string $permission): bool
    {
        return Perm::checkPermission($permission, $this);
    }

    public function getPermissions(): array
    {
        return $this->permissions;
    }

    /**
     * Return the name of the group
     * @return string
     * @since API 0.3.0 (2019)
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * Return the id of the group
     * @return int
     * @since API 0.3.0 (2019)
     */
    public function getId() : int
    {
        return $this->id;
    }

    public function setPermission(string $permission, bool $ability)
    {
        // TODO set permission
    }

    public function __toString(): string
    {
        return $this->parse_JSON(get_object_vars($this));
    }
    
    public function serialize()
    {
        return serialize(get_object_vars($this));
    }
    
    public function unserialize($serialized)
    {
        $array = unserialize($serialized);
        
        foreach ($array as $name => $value) {
            $this->$name = $value;
        }
        
        return $this;
    }
    
    public function getProperties(): array
    {
        return array_merge(['type' => get_simple_classname($this)], get_object_vars($this));
    }
}

