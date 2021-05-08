<?php
namespace cafetapi\data;

use cafetapi\io\ExpenseManager;
use cafetapi\io\ReloadManager;

/**
 * The Client object is the wich stores every client information for later use
 *
 * @author Damien
 * @since API 0.1.0 (2018)
 *       
 */
class Client extends People implements Data
{
    private $id;
    private $alias;
    private $member;
    private $balance;
    private $registrationYear;

    /**
     * The Client object is the wich stores every client information for later use,
     * it's generaly generated by a Data static method (such as
     * Data::getClient(int $id))
     *
     * @param int $id
     *            an integer, the ID of the client in the DataBase
     * @param string $email
     *            the email of the client
     * @param string $alias
     *            the username of the client
     * @param string $familyNane
     *            the familyname of the client
     * @param string $surname
     *            the surname of the client
     * @param bool $member
     *            a boolean that represents whether or not the client is a member of the association and can benefits for discounts
     * @param float $balance
     *            the actual balance of the client
     * @param int $registrationYear
     *            the year of the client's registration
     * @see \cafetapi\io\ClientManager::getClient()
     * @since API 0.1.0 (2018)
     */
    public function __construct(int $id, string $email, string $alias, string $familyName, string $surname, bool $member, float $balance, int $registrationYear)
    {
        parent::__construct($email, $familyName, $surname);
        $this->id = $id;
        $this->alias = $alias;
        $this->member = $member;
        $this->balance = $balance;
        $this->registrationYear = $registrationYear;
    }

    /**
     * Return the id
     *
     * @return int the id
     * @since API 0.1.0 (2018)
     */
    public final function getId(): int
    {
        return $this->id;
    }

    /**
     * Return the alias
     *
     * @return string the alias
     * @since API 0.1.0 (2018)
     */
    public final function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * Return is the client is a member
     *
     * @return bool is member
     * @since API 0.1.0 (2018)
     */
    public final function isMember(): bool
    {
        return $this->member;
    }

    /**
     * Return the balance
     *
     * @return float the balance
     * @since API 0.1.0 (2018)
     */
    public final function getBalance(): float
    {
        return $this->balance;
    }

    /**
     * Return the registration yean
     *
     * @return int the registration year
     * @since API 0.1.0 (2018)
     */
    public final function getRegistrationYear(): int
    {
        return $this->registrationYear;
    }

    /**
     * Return a list of client's expenses
     *
     * @return array a list of client's expenses
     * @since API 0.1.0 (2018)
     */
    public final function getExpenses(): array
    {
        return ExpenseManager::getInstance()->getClientExpenses($this->id);
    }

    /**
     * Return a list of latest client's expenses
     *
     * @return array a list of client's expenses
     * @since API 0.1.0 (2018)
     */
    public final function getLastExpenses(): array
    {
        return ExpenseManager::getInstance()->getClientLastExpenses($this->id);
    }

    /**
     * Return a list of latest client's reloads
     *
     * @return array a list of client's reloads
     * @since API 0.1.0 (2018)
     */
    public final function getReloads(): array
    {
        return ReloadManager::getInstance()->getClientReloads($this->id);
    }

    /**
     * Returns the name like Suname NAME
     *
     * @return string the formated name
     * @since API 0.1.0 (2018)
     */
    public final function getFormatedName(): string
    {
        return capitalize_first_letter($this->surname) . ' ' . strtoupper($this->familyName);
    }

    public function __toString(): string
    {
        return $this->parse_JSON(get_object_vars($this));
    }
    
    public function getProperties(): array
    {
        return array_merge(['type' => get_simple_classname($this)], get_object_vars($this));
    }
}
