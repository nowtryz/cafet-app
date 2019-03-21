<?php
namespace cafetapi\data;

use cafetapi\exceptions\EmailFormatException;
use cafetapi\config\Defaults;

abstract class People extends JSONParsable
{
    protected $email;
    protected $familyName;
    protected $firstname;
    protected $mail_preferences;
    
    
    public function __construct($email, $familyName, $firstname, $mail_preferences = [])
    {
        $this->email = $email;
        $this->familyName = $familyName;
        $this->firstname = $firstname;
        $this->mail_preferences = array_merge(Defaults::mail_preferences, $mail_preferences);
    }
    
    /**
     * Return the email
     *
     * @return string the email
     * @since API 0.1.0 (2018)
     */
    public final function getEmail(): string
    {
        return $this->email;
    }
    
    /**
     * Return the family name
     *
     * @return string the family name
     * @since API 0.1.0 (2018)
     */
    public final function getFamilyName(): string
    {
        return $this->familyName;
    }
    
    /**
     * Return the surname
     *
     * @return string the surname
     * @since API 0.1.0 (2018)
     */
    public final function getFirstname(): string
    {
        return $this->firstname;
    }
    
    /**
     * @return array
     * @since API 0.3.0 (2019)
     */
    public function getMailPreference(string $preference) : bool
    {
        return (bool) $this->mail_preferences[$preference] ?? false;
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

