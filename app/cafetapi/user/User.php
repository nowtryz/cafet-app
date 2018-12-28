<?php
namespace cafetapi\user;

use cafetapi\data\Calendar;
use cafetapi\data\Data;
use cafetapi\data\JSONParsable;
use cafetapi\exceptions\EmailFormatException;

/**
 *
 * @author Damien
 *        
 */
class User extends JSONParsable implements Permissible, Data, \Serializable
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
    
    private $signin_count;
    private $last_signin;
    private $registration;

    /**
     */
    public function __construct(int $id, string $pseudo, string $firstname, string $name, string $password, string $email, $phone, 
        Calendar $last_signin, Calendar $registration, int $signin_count, Group $group, array $additional_permissions = null)
    {
        $this->id = $id;
        $this->pseudo = $pseudo;
        $this->firstname = $firstname;
        $this->name = $name;
        $this->password = $password;
        $this->email = $email;
        $this->phone = $phone;
        $this->group = $group;
        
        $this->signin_count = $signin_count;
        $this->last_signin = $last_signin;
        $this->registration = $registration;

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
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $pseudo
     */
    public function setPseudo(string $pseudo)
    {
        $this->pseudo = $pseudo;
    }

    /**
     * @param string $firstname
     */
    public function setFirstname(string $firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) throw new EmailFormatException('"' . $email . '" is not valid!');
        $this->email = $email;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone(string $phone)
    {
        $this->phone = $phone;
    }

    /**
     * @param Group $group
     */
    public function setGroup(Group $group)
    {
        $this->group = $group;
    }

    /**
     *
     * {@inheritdoc}
     * @see \cafetapi\user\Permissible::hasPermission()
     */
    public function hasPermission(string $permission): bool
    {
        return Perm::checkPermission($permission, $this);
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

    public function __toString(): string
    {
        $vars = get_object_vars($this);
        if (isset($vars['password']))
            unset($vars['password']);

        return $this->parse_JSON($vars);
    }
    
    public function getProperties(): array
    {
        $vars = get_object_vars($this);
        $vars['group']=$this->group->getProperties();
        $vars['last_signin']=$this->last_signin->getProperties();
        $vars['registration']=$this->registration->getProperties();
        unset($vars['password']);
        return array_merge(array('type' => get_simple_classname($this)), $vars);
    }
}

