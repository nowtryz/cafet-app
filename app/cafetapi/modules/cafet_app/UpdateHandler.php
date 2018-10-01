<?php
namespace cafetapi\modules\cafet_app;

use cafetapi\data\Choice;
use cafetapi\data\Formula;
use cafetapi\data\FormulaOrdered;
use cafetapi\data\Product;
use cafetapi\data\ProductGroup;
use cafetapi\data\ProductOrdered;
use cafetapi\io\DataUpdater;
use cafetapi\user\Perm;
use cafetapi\user\User;

class UpdateHandler extends Handler
{

    public function __construct()
    {
        parent::__construct();
        $this->connection = new DataUpdater();
    }

    /**
     * Insert a product into the database
     *
     * @param array $arguments
     *            an array containing "name" and "group_id"
     * @return Product the product inserted
     * @since API 1.0.0 (2018)
     */
    public final function add_product(array $arguments): Product
    {
        if (! Perm::checkPermission(PERM::CAFET_ADMIN_MANAGE_PRODUCTS, $this->user))
            cafet_throw_error('02_002');

        if (! isset($arguments['name']) || ! isset($arguments['group_id']))
            cafet_throw_error('03-006');
        if (gettype($arguments['group_id']) != 'integer')
            cafet_throw_error('03-005', 'group_id must be an integer');

        $name = $arguments['name'];
        $group_id = $arguments['group_id'];

        return $this->connection->addProduct($name, $group_id);
    }

    /**
     * Insert a product group in the database
     *
     * @param array $arguments
     *            an array containing "name"
     * @return ProductGroup the group inserted
     * @since API 1.0.0 (2018)
     */
    public final function add_product_group(array $arguments): ProductGroup
    {
        if (! Perm::checkPermission(PERM::CAFET_ADMIN_MANAGE_PRODUCTS, $this->user))
            cafet_throw_error('02_002');

        if (! isset($arguments['name']))
            cafet_throw_error('03-006');

        $name = $arguments['name'];

        return $this->connection->addProductGroup($name);
    }

    /**
     * Insert a formula in the database
     *
     * @param array $arguments
     *            an array containig "name"
     * @return Formula the formula inserted
     * @since API 1.0.0 (2018)
     */
    public final function add_formula(array $arguments): Formula
    {
        if (! Perm::checkPermission(PERM::CAFET_ADMIN_MANAGE_FORMULAS, $this->user))
            cafet_throw_error('02_002');

        if (! isset($arguments['name']))
            cafet_throw_error('03-006');

        $name = $arguments['name'];

        return $this->connection->addFormula($name);
    }

    /**
     * Insert a choice in the database for the specified formula
     *
     * @param array $arguments
     *            an array containing "formula_id" and "name"
     * @return Choice the choice inserted
     * @since API 1.0.0 (2018)
     */
    public final function add_choice(array $arguments): Choice
    {
        if (! Perm::checkPermission(PERM::CAFET_ADMIN_MANAGE_FORMULAS, $this->user))
            cafet_throw_error('02_002');

        if (! isset($arguments['formula_id']) || ! isset($arguments['name']))
            cafet_throw_error('03-006');
            if (gettype($arguments['formula_id']) != 'integer')
            cafet_throw_error('03-005', 'formula_id must be an integer');

        $formula_id = $arguments['formula_id'];
        $name = $arguments['name'];

        return $this->connection->addChoice($name, $formula_id);
    }

    /**
     * Register a product for the specified choice
     *
     * @param array $arguments
     *            an array containing "choice_id" and "product_id"
     * @return bool if the query has been correctly completed
     * @since API 1.0.0 (2018)
     */
    public final function add_product_to_choice(array $arguments): bool
    {
        if (! Perm::checkPermission(PERM::CAFET_ADMIN_MANAGE_FORMULAS, $this->user))
            cafet_throw_error('02_002');

        if (! isset($arguments['choice_id']) || ! isset($arguments['product_id']))
            cafet_throw_error('03-006');
            if (gettype($arguments['choice_id']) != 'integer' || gettype($arguments['product_id']) != 'integer')
            cafet_throw_error('03-005', 'ids must be integers');

        $choice_id = $arguments['choice_id'];
        $product_id = $arguments['product_id'];

        return $this->connection->addProductToChoice($choice_id, $product_id);
    }

    /**
     * Unregister a product for the specified choice
     *
     * @param array $arguments
     *            an array containing "choice_id" and "product_id"
     * @return bool if the query have correctly been completed
     * @since API 1.0.0 (2018)
     */
    public final function remove_product_from_choice(array $arguments): bool
    {
        if (! Perm::checkPermission(PERM::CAFET_ADMIN_MANAGE_FORMULAS, $this->user))
            cafet_throw_error('02_002');

        if (! isset($arguments['choice_id']) || ! isset($arguments['product_id']))
            cafet_throw_error('03-006');
            if (gettype($arguments['choice_id']) != 'integer' || gettype($arguments['product_id']) != 'integer')
            cafet_throw_error('03-005', 'ids must be integers');

        $choice_id = $arguments['choice_id'];
        $product_id = $arguments['product_id'];

        return $this->connection->removeProductFromChoice($choice_id, $product_id);
        return true;
    }

    /**
     * Save a purchase in the database
     *
     * @param array $arguments
     *            an array containing "client_id" and "order"
     * @return bool if the query have correctly been completed
     * @since API 1.0.0 (2018)
     */
    public final function save_order(array $arguments): bool
    {
        if (! Perm::checkPermission(PERM::CAFET_ADMIN_ORDER, $this->user))
            cafet_throw_error('02_002');

        if (! isset($arguments['client_id']))              cafet_throw_error('03-006', 'missing client_id');
        if (! isset($arguments['order']))                  cafet_throw_error('03-006', 'missing order');
        if (gettype($arguments['client_id']) != 'integer') cafet_throw_error('03-005', 'client_id must be an integer');
        if (! is_array($arguments['order']))               cafet_throw_error('03-005', 'what\'s order?');

        $client_id = $arguments['client_id'];
        $order = array();

        foreach ($arguments['order'] as $entry) {
            if (! isset($entry['type']) || ! isset($entry['id']) || ! isset($entry['amount']))
                cafet_throw_error('03-006');
            if (gettype($entry['id']) != 'integer')
                cafet_throw_error('03-005', 'ids must be integers');
            if (gettype($entry['amount']) != 'integer')
                cafet_throw_error('03-005', 'amounts must be integers');

            if ($entry['type'] == 'product') {
                $order[] = new ProductOrdered($entry['id'], $entry['amount']);
            } elseif ($entry['type'] == 'formula') {
                if (! isset($entry['products'])) cafet_throw_error('03-006', 'missing products for a formula');
                if (is_associative_array($entry['products'])) cafet_throw_error('03-005', 'products for a formula must not be an associative array');
                
                $products = array();
                foreach ($entry['products'] as $p) $products[] = intval($p, 0);
                $order[] = new FormulaOrdered($entry['id'], $entry['amount'], $products);
            } else
                cafet_throw_error('03-005', $entry['type'] . ' isn\'t a valid type');
        }

        return $this->connection->saveOrder($client_id, $order);
    }

    /**
     * Save a balance reloading for the specified client
     *
     * @param array $arguments
     *            an array containg "client_id" and "amount"
     * @return bool if the query have correctly been completed
     * @since API 1.0.0 (2018)
     */
    public final function save_reload(array $arguments): bool
    {
        if (! Perm::checkPermission(PERM::CAFET_ADMIN_RELOAD, $this->user))
            cafet_throw_error('02_002');

        global $user;
        global $app;
        if (! isset($arguments['amount']) || ! isset($arguments['client_id']))
            cafet_throw_error('03-006');
        if (gettype($arguments['amount']) != 'double' || gettype($arguments['client_id']) != 'integer')
            cafet_throw_error('03-005', 'amount must be a float and client_id an integer');

        $comment = isset($user) && $user instanceof User && isset($app) && $app == 'cafet_app' ? 'by ' . $user->getPseudo() . ': user ' . $user->getId() : 'by WebSite';
        $client_id = $arguments['client_id'];
        $amount = $arguments['amount'];

        if ($amount < 0 && ! $user->hasPermission(Perm::CAFET_ADMIN_NEGATIVERELOAD))
            cafet_throw_error('02-002', 'not allowed to perform negative reloads');

        return $this->connection->saveReload($client_id, $amount, $comment);
    }

    /**
     * Change the display name for the specified group
     *
     * @param array $arguments
     *            an array containg "group_id" and "display_name"
     * @return bool if the query have correctly been completed
     * @since API 1.0.0 (2018)
     */
    public final function set_product_group_display_name(array $arguments): bool
    {
        if (! Perm::checkPermission(PERM::CAFET_ADMIN_MANAGE_PRODUCTS, $this->user))
            cafet_throw_error('02_002');

        if (! isset($arguments['group_id']) || ! isset($arguments['display_name']))
            cafet_throw_error('03-006');
        if (gettype($arguments['group_id']) != 'integer')
            cafet_throw_error('03-005', 'group_id must be an integer');

        $group_id = $arguments['group_id'];
        $display_name = $arguments['display_name'];

        return $this->connection->setProductGroupDisplayName($group_id, $display_name);
    }

    /**
     * Change the name for the specified group
     *
     * @param array $arguments
     *            an array containg "group_id" and "name"
     * @return bool if the query have correctly been completed
     * @since API 1.0.0 (2018)
     */
    public final function set_product_group_name(array $arguments): bool
    {
        if (! Perm::checkPermission(PERM::CAFET_ADMIN_MANAGE_PRODUCTS, $this->user))
            cafet_throw_error('02_002');

        if (! isset($arguments['group_id']) || ! isset($arguments['name']))
            cafet_throw_error('03-006');
        if (gettype($arguments['group_id']) != 'integer')
            cafet_throw_error('03-005', 'group_id must be an integer');

        $group_id = $arguments['group_id'];
        $name = $arguments['name'];

        return $this->connection->setProductGroupName($group_id, $name);
    }

    /**
     * Change the name for the specified product
     *
     * @param array $arguments
     *            an array containg "product_id" and "name"
     * @return bool if the query have correctly been completed
     * @since API 1.0.0 (2018)
     */
    public final function set_product_name(array $arguments): bool
    {
        if (! Perm::checkPermission(PERM::CAFET_ADMIN_MANAGE_FORMULAS, $this->user))
            cafet_throw_error('02_002');

        if (! isset($arguments['product_id']) || ! isset($arguments['name']))
            cafet_throw_error('03-006');
        if (gettype($arguments['product_id']) != 'integer')
            cafet_throw_error('03-005', 'group_id must be an integer');

        $product_id = $arguments['product_id'];
        $name = $arguments['name'];

        return $this->connection->setProductName($product_id, $name);
    }

    /**
     * Change the price for the specified product
     *
     * @param array $arguments
     *            an array containg "product_id" and "price"
     * @return bool if the query have correctly been completed
     * @since API 1.0.0 (2018)
     */
    public final function set_product_price(array $arguments): bool
    {
        if (! Perm::checkPermission(PERM::CAFET_ADMIN_MANAGE_PRODUCTS, $this->user))
            cafet_throw_error('02_002');

        if (! isset($arguments['product_id']) || ! isset($arguments['price']))
            cafet_throw_error('03-006');
        if (gettype($arguments['product_id']) != 'integer')
            cafet_throw_error('03-005', 'group_id must be an integer');
        if (gettype($arguments['price']) != 'double')
            cafet_throw_error('03-005', 'price must be a double');

        $product_id = $arguments['product_id'];
        $price = $arguments['price'];

        return $this->connection->setProductPrice($product_id, $price);
    }

    /**
     * Change the group for the specified product
     *
     * @param array $arguments
     *            an array containg "product_id" and "group_id"
     * @return bool if the query have correctly been completed
     * @since API 1.0.0 (2018)
     */
    public final function set_product_group(array $arguments): bool
    {
        if (! Perm::checkPermission(PERM::CAFET_ADMIN_MANAGE_PRODUCTS, $this->user))
            cafet_throw_error('02_002');

        if (! isset($arguments['product_id']) || ! isset($arguments['group_id']))
            cafet_throw_error('03-006');
        if (gettype($arguments['product_id']) != 'integer' || gettype($arguments['group_id']) != 'integer')
            cafet_throw_error('03-005', 'group_id and product_id must be integers');

        $product_id = $arguments['product_id'];
        $group_id = $arguments['group_id'];

        return $this->connection->setProductGroup($product_id, $group_id);
    }

    /**
     * Change the image of the specified product
     *
     * @param array $arguments
     *            an array containg "product_id" and "image_base64"
     * @return bool if the query have correctly been completed
     * @since API 1.0.0 (2018)
     */
    public final function set_product_image(array $arguments): bool
    {
        if (! Perm::checkPermission(PERM::CAFET_ADMIN_MANAGE_PRODUCTS, $this->user))
            cafet_throw_error('02_002');

        if (! isset($arguments['product_id']) || ! isset($arguments['image_base64']))
            cafet_throw_error('03-006');
        if (gettype($arguments['product_id']) != 'integer')
            cafet_throw_error('03-005', 'product_id must be an integer');

        $product_id = $arguments['product_id'];
        $image = $arguments['image_base64'];

        return $this->connection->setProductImage($product_id, $image);
    }

    /**
     * Change the visibility of the specified product
     *
     * @param array $arguments
     *            an array containg "produc_id" and "flag"
     * @return bool if the query have correctly been completed
     * @since API 1.0.0 (2018)
     */
    public final function set_product_viewable(array $arguments): bool
    {
        if (! Perm::checkPermission(PERM::CAFET_ADMIN_MANAGE_PRODUCTS, $this->user))
            cafet_throw_error('02_002');

        if (! isset($arguments['product_id']) || ! isset($arguments['flag']))
            cafet_throw_error('03-006');
        if (gettype($arguments['product_id']) != 'integer')
            cafet_throw_error('03-005', 'product_id must be an integer');
        if (gettype($arguments['flag']) != 'boolean')
            cafet_throw_error('03-005', 'flag must be a boolean');

        $product_id = $arguments['product_id'];
        $flag = $arguments['flag'];

        return $this->connection->setProductViewable($product_id, $flag);
    }

    /**
     * Change the name of the specified formula
     *
     * @param array $arguments
     *            an array containg "formula_id" and "name"
     * @return bool if the query have correctly been completed
     * @since API 1.0.0 (2018)
     */
    public final function set_formula_name(array $arguments): bool
    {
        if (! Perm::checkPermission(PERM::CAFET_ADMIN_MANAGE_FORMULAS, $this->user))
            cafet_throw_error('02_002');

        if (! isset($arguments['formula_id']) || ! isset($arguments['name']))
            cafet_throw_error('03-006');
        if (gettype($arguments['formula_id']) != 'integer')
            cafet_throw_error('03-005', 'formula_id must be an integer');

        $formula_id = $arguments['formula_id'];
        $name = $arguments['name'];

        return $this->connection->setFormulaName($formula_id, $name);
    }

    /**
     * Change the price of the specified formula
     *
     * @param array $arguments
     *            an array containg "formula_id" and "price"
     * @return bool if the query have correctly been completed
     * @since API 1.0.0 (2018)
     */
    public final function set_formula_price(array $arguments): bool
    {
        if (! Perm::checkPermission(PERM::CAFET_ADMIN_MANAGE_FORMULAS, $this->user))
            cafet_throw_error('02_002');

        if (! isset($arguments['formula_id']) || ! isset($arguments['price']))
            cafet_throw_error('03-006');
        if (gettype($arguments['formula_id']) != 'integer')
            cafet_throw_error('03-005', 'formula_id must be an integer');
        if (gettype($arguments['price']) != 'double')
            cafet_throw_error('03-005', 'price must be a float');

        $formula_id = $arguments['formula_id'];
        $price = $arguments['price'];

        return $this->connection->setFormulaPrice($formula_id, $price);
    }

    /**
     * Change the visibility of the specified formula
     *
     * @param array $arguments
     *            an array containg "formula_id" and "flag"
     * @return bool if the query have correctly been completed
     * @since API 1.0.0 (2018)
     */
    public final function set_formula_viewable(array $arguments): bool
    {
        if (! Perm::checkPermission(PERM::CAFET_ADMIN_MANAGE_FORMULAS, $this->user))
            cafet_throw_error('02_002');

        if (! isset($arguments['formula_id']) || ! isset($arguments['flag']))
            cafet_throw_error('03-006');
        if (gettype($arguments['formula_id']) != 'integer')
            cafet_throw_error('03-005', 'formula_id must be an integer');
        if (gettype($arguments['flag']) != 'boolean')
            cafet_throw_error('03-005', 'flag must be a boolean');

        $formula_id = $arguments['formula_id'];
        $flag = $arguments['flag'];

        return $this->connection->setFormulaViewable($formula_id, $flag);
    }

    /**
     * Change the image of the specified formula
     *
     * @param array $arguments
     *            an array containg "formula_id" and "image_base64"
     * @return bool if the query have correctly been completed
     * @since API 1.0.0 (2018)
     */
    public final function set_formula_image(array $arguments): bool
    {
        if (! Perm::checkPermission(PERM::CAFET_ADMIN_MANAGE_FORMULAS, $this->user))
            cafet_throw_error('02_002');

        if (! isset($arguments['formula_id']) || ! isset($arguments['image_base64']))
            cafet_throw_error('03-006');
        if (gettype($arguments['formula_id']) != 'integer')
            cafet_throw_error('03-005', 'formula_id must be an integer');

        $formula_id = $arguments['formula_id'];
        $image = $arguments['image_base64'];

        return $this->connection->setFormulaImage($formula_id, $image);
    }

    /**
     * Delete a product from the database
     *
     * @param array $arguments
     *            an array containg "product_id"
     * @return bool if the query have correctly been completed
     * @since API 1.0.0 (2018)
     */
    public final function delete_product(array $arguments): bool
    {
        if (! Perm::checkPermission(PERM::CAFET_ADMIN_MANAGE_PRODUCTS, $this->user))
            cafet_throw_error('02_002');

        if (! isset($arguments['product_id']))
            cafet_throw_error('03-006');

        $id = $arguments['product_id'];

        return $this->connection->deleteProduct($id);
    }

    /**
     * Delete a product group from the database
     *
     * @param array $arguments
     *            an array containg "group_id"
     * @return bool if the query have correctly been completed
     * @since API 1.0.0 (2018)
     */
    public final function delete_product_group(array $arguments): bool
    {
        if (! Perm::checkPermission(PERM::CAFET_ADMIN_MANAGE_PRODUCTS, $this->user))
            cafet_throw_error('02_002');

        if (! isset($arguments['group_id']))
            cafet_throw_error('03-006');

        $id = $arguments['group_id'];

        return $this->connection->deleteProductGroup($id);
    }

    /**
     * Delete a formula from the database
     *
     * @param array $arguments
     *            an array containg "formula_id"
     * @return bool if the query have correctly been completed
     * @since API 1.0.0 (2018)
     */
    public final function delete_formula(array $arguments): bool
    {
        if (! Perm::checkPermission(PERM::CAFET_ADMIN_MANAGE_FORMULAS, $this->user))
            cafet_throw_error('02_002');

        if (! isset($arguments['formula_id']))
            cafet_throw_error('03-006');

        $id = $arguments['formula_id'];

        return $this->connection->deleteFormula($id);
    }

    /**
     * Delete a choice from the database
     *
     * @param array $arguments
     *            an array containing "choice_id"
     * @return bool if the query have correctly been completed
     * @since API 1.0.0 (2018)
     */
    public final function delete_formula_choice(array $arguments): bool
    {
        if (! Perm::checkPermission(PERM::CAFET_ADMIN_MANAGE_FORMULAS, $this->user))
            cafet_throw_error('02_002');

        if (! isset($arguments['choice_id']))
            cafet_throw_error('03-006');

        $id = $arguments['choice_id'];

        return $this->connection->deleteFormulaChoice($id);
    }
}

