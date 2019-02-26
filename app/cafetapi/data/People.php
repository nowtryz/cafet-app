<?php
namespace cafetapi\data;

use cafetapi\exceptions\EmailFormatException;

abstract class People extends JSONParsable
{
    protected $email;
    protected $familyName;
    protected $firstname;
    
    
    public function __construct($email, $familyName, $firstname)
    {
        $this->email = $email;
        $this->familyName = $familyName;
        $this->firstname = $firstname;
    }
    
    /**
     * Return the email
     *
     * @return string the email
     * @since API 1.0.0 (2018)
     */
    public final function getEmail(): string
    {
        return $this->email;
    }
    
    /**
     * Return the family name
     *
     * @return string the family name
     * @since API 1.0.0 (2018)
     */
    public final function getFamilyName(): string
    {
        return $this->familyName;
    }
    
    /**
     * Return the surname
     *
     * @return string the surname
     * @since API 1.0.0 (2018)
     */
    public final function getFirstname(): string
    {
        return $this->firstname;
    }
    
    /**
     * @param string $surname
     */
    public function setFirstname(string $firstname)
    {
        $this->firstname = $firstname;
    }
    
    /**
     * @param string $familyName
     */
    public function setFamilyName(string $familyName)
    {
        $this->familyName = $familyName;
    }
    
    /**
     * @param string $email
     */
    public function setEmail(string $email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) throw new EmailFormatException('"' . $email . '" is not valid!');
        $this->email = $email;
    }
}

