<?php
namespace cafetapi\user;

/**
 * Undergo the permission system
 *
 * @author Damien
 * @since API 0.1.0 (2018)
 */
interface Permissible
{

    /**
     * Return wether the member has the given permission or not
     *
     * @param string $permission
     *            the permission to check
     * @return bool the permission check
     * @since API 0.1.0 (2018)
     */
    public function hasPermission(string $permission): bool;

    /**
     * Return every permission witch the member is liable from
     *
     * @return array a array of permissions
     * @since API 0.1.0 (2018)
     */
    public function getPermissions(): array;

    /**
     * Sets a permission
     *
     * @param string $permission
     *            th permission to set
     * @param bool $ability
     *            the ability
     * @since API 0.1.0 (2018)
     */
    public function setPermission(string $permission, bool $ability);
}

