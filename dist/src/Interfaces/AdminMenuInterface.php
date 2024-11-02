<?php

declare(strict_types=1);

namespace LogAction\Interfaces;

/**
 * Interface AdminMenuInterface
 *
 * Defines a contract for classes that add admin menus.
 */
interface AdminMenuInterface {

    /**
     * Register the menu and submenus.
     */
    public function registerMenus(): void;

    /**
     * Add the main menu and submenus in the WordPress admin dashboard.
     */
    public function addAdminMenus(): void;
}
