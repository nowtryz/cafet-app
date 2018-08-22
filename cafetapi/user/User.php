<?php
namespace cafetapi\user;

use cafetapi\data\JSONParsable;

/**
 *
 * @author Damien
 *        
 */
class User extends JSONParsable implements Permissible, \Serializable
{

    private $id;

    private $pseudo;

    private $firstname;

    private $name;

    private $password;

    private $email;

    private $phone;

    private $permissions;

    private $group;

    /**
     */
    public function __construct(int $id, string $pseudo, string $firstname, string $name, string $password, string $email, $phone, Group $group, array $additional_permissions = null)
    {
        $this->id = $id;
        $this->pseudo = $pseudo;
        $this->firstname = $firstname;
        $this->name = $name;
        $this->password = $password;
        $this->email = $email;
        $this->phone = $phone;
        $this->group = $group;

        $this->permissions = $group->getPermissions();

        if (isset($additional_permissions))
            foreach ($additional_permissions as $name => $permission) {
                $this->permissions[$name] = $permission;
            }
    }

    /**
     * Returns the $id
     *
     * @return int the $id
     * @since API 1.0.0 (2018)
     */
    public final function getId()
    {
        return $this->id;
    }

    /**
     * Returns the $pseudo
     *
     * @return string the $pseudo
     * @since API 1.0.0 (2018)
     */
    public final function getPseudo()
    {
        return $this->pseudo;
    }

    /**
     * Returns the $firstname
     *
     * @return string the $firstname
     * @since API 1.0.0 (2018)
     */
    public final function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Returns the $name
     *
     * @return string the $name
     * @since API 1.0.0 (2018)
     */
    public final function getName()
    {
        return $this->name;
    }

    /**
     * Returns the $hash
     *
     * @return string the $hash
     * @since API 1.0.0 (2018)
     */
    public final function getHash()
    {
        return $this->password;
    }

    /**
     * Returns the $email
     *
     * @return string the $email
     * @since API 1.0.0 (2018)
     */
    public final function getEmail()
    {
        return $this->email;
    }

    /**
     * Returns the $phone
     *
     * @return mixed the $phone
     * @since API 1.0.0 (2018)
     */
    public final function getPhone()
    {
        return $this->phone;
    }

    /**
     * Returns the $group
     *
     * @return Group the $group
     * @since API 1.0.0 (2018)
     */
    public final function getGroup()
    {
        return $this->group;
    }

    /**
     *
     * {@inheritdoc}
     * @see \cafetapi\user\Permissible::hasPermission()
     */
    public function hasPermission(string $permission): bool
    {
        Perm::checkPermission($permission, $this);
    }

    /**
     *
     * {@inheritdoc}
     * @see \cafetapi\user\Permissible::getPermissions()
     */
    public function getPermissions(): array
    {
        return $this->permissions;
    }

    /**
     *
     * {@inheritdoc}
     * @see \cafetapi\user\Permissible::setPermission()
     */
    public function setPermission(string $permission, bool $ability)
    {
        $this->permissions[$permission] = $ability;
    }

    public function serialize()
    {
        return $this->__toString();
    }

    public function unserialize($serialized)
    {
        $array = json_decode($serialized, true);

        $this->group = new Group($array['group']['name'], $array['group']['permissions']);
        unset($array['group']);

        foreach ($array as $name => $value) {
            $this->$name = $value;
        }

        return $this;
    }

    public function __toString(): string
    {
        $vars = get_object_vars($this);
        if (isset($vars['password']))
            unset($vars['password']);

        return $this->parse_JSON($vars);
    }
}

