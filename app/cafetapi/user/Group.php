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
    const GUEST = array(
        Perm::GLOBAL_CONNECT => true,
        Perm::CAFET_ADMIN_GET_PRODUCTS => true,
        Perm::CAFET_ADMIN_GET_FORMULAS => true
    );

    const SUPER_USER = array(
        Perm::ALL => true
    );

    const ADMIN = array(
        Perm::ALL => true,
        Perm::GLOBAL_HIDDEN => false,
        Perm::GLOBAL_UNTOUCHABLE => false
    );

    const CAFET_ADMIN = array(
        Perm::CAFET => true
    );

    const CAFET_MANAGER = array(
        Perm::CAFET_ADMIN_PANELACCESS => true,
        Perm::CAFET_ADMIN_ORDER => true,
        Perm::CAFET_ADMIN_RELOAD => true,
        Perm::CAFET_ADMIN_STATS => true,
        Perm::CAFET_ADMIN_GET => true,
        Perm::CAFET_PURCHASE => true,
        Perm::CAFET_GET_CLIENTS_ME => true
    );

    const CONSUMER = array(
        Perm::CAFET_PURCHASE => true,
        Perm::CAFET_GET_CLIENTS_ME => true
    );

    private $name;

    private $permissions;

    /**
     *
     * @param string $name
     * @param array $permissions
     * @since API 1.0.0 (2018)
     */
    public function __construct(string $name, array $permissions)
    {
        $this->name = $name;

        if (is_associative_array($permissions))
            foreach ($permissions as $permission => $value) {
                if (is_string($permission)) {
                    $this->permissions[$permission] = (bool) $value;
                }
            }
    }

    public function hasPermission(string $permission): bool
    {
        return Perm::checkPermission($permission, $this);
    }

    public function getPermissions(): array
    {
        return $this->permissions;
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

//     public function serialize()
//     {
//         return $this->__toString();
//     }

//     public function unserialize($serialized)
//     {
//         $array = json_decode($serialized);

//         foreach ($array as $name => $value) {
//             $this->$name = $value;
//         }

//         return $this;
//     }
    
    public function getProperties(): array
    {
        return array_merge(array('type' => get_simple_classname($this)), get_object_vars($this));
    }
}

