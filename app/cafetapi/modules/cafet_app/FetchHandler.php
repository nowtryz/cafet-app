<?php
namespace cafetapi\modules\cafet_app;

use cafetapi\data\Client;
use cafetapi\data\Product;
use cafetapi\io\DataFetcher;
use cafetapi\data\ProductGroup;
use cafetapi\data\Formula;
use cafetapi\user\Perm;
use cafetapi\data\ProductBought;
use cafetapi\data\FormulaBought;

class FetchHandler extends Handler
{

    public function __construct()
    {
        parent::__construct();
    }

    public final function get_clients(array $arguments): array
    {
        if (! Perm::checkPermission(PERM::CAFET_ADMIN_GET_CLIENTS, $this->user))
            cafet_throw_error('02_002');

        $return = array();

        foreach (DataFetcher::getInstance()->getClients() as $client)
            $return[] = json_decode($client->__toString());

        return $return;
    }

    public final function get_client(array $arguments): ?Client
    {
        if (! Perm::checkPermission(PERM::CAFET_ADMIN_GET_CLIENTS, $this->user))
            cafet_throw_error('02_002');

        if (! isset($arguments['id']))
            cafet_throw_error('03-006');
        if (gettype($arguments['id']) != 'integer')
            cafet_throw_error('03-005', 'id must be an integer');

        $id = $arguments['id'];

        return DataFetcher::getInstance()->getClient($id);
    }

    public final function get_client_reloads(array $arguments): array
    {
        if (! Perm::checkPermission(PERM::CAFET_ADMIN_GET_RELOADS, $this->user))
            cafet_throw_error('02_002');

        if (! isset($arguments['id']))
            cafet_throw_error('03-006');
        if (gettype($arguments['id']) != 'integer')
            cafet_throw_error('03-005', 'id must be an integer');

        $id = $arguments['id'];

        $return = array();

        foreach (DataFetcher::getInstance()->getClientReloads($id) as $reload)
            $return[] = json_decode($reload->__toString());

        return $return;
    }

    public final function get_client_expenses(array $arguments): array
    {
        if (! Perm::checkPermission(PERM::CAFET_ADMIN_GET_EXPENSES, $this->user))
            cafet_throw_error('02_002');

        if (! isset($arguments['id']))
            cafet_throw_error('03-006');
        if (gettype($arguments['id']) != 'integer')
            cafet_throw_error('03-005', 'id must be an integer');

        $id = $arguments['id'];

        $return = array();

        foreach (DataFetcher::getInstance()->getClientExpenses($id) as $expense)
            $return[] = json_decode($expense->__toString());

        return $return;
    }

    public final function get_expense_details(array $arguments): array
    {
        if (! Perm::checkPermission(PERM::CAFET_ADMIN_GET_EXPENSES, $this->user))
            cafet_throw_error('02_002');

        if (! isset($arguments['id']))
            cafet_throw_error('03-006');
        if (gettype($arguments['id']) != 'integer')
            cafet_throw_error('03-005', 'id must be an integer');

        $id = $arguments['id'];

        $return = array();

        foreach (DataFetcher::getInstance()->getExpenseDetails($id) as $detail)
            $return[] = json_decode($detail->__toString());

        return $return;
    }
    
    public final function get_product_bought(array $arguments): ?ProductBought
    {
        if (! Perm::checkPermission(PERM::CAFET_ADMIN_GET_EXPENSES, $this->user))
            cafet_throw_error('02_002');
            
        if (! isset($arguments['id']))
            cafet_throw_error('03-006');
        if (gettype($arguments['id']) != 'integer')
            cafet_throw_error('03-005', 'id must be an integer');
                    
        $id = $arguments['id'];
        
        return DataFetcher::getInstance()->getProductBought($id);
    }
    
    public final function get_formula_bought(array $arguments): ?FormulaBought
    {
        if (! Perm::checkPermission(PERM::CAFET_ADMIN_GET_EXPENSES, $this->user))
            cafet_throw_error('02_002');
            
        if (! isset($arguments['id']))
            cafet_throw_error('03-006');
        if (gettype($arguments['id']) != 'integer')
            cafet_throw_error('03-005', 'id must be an integer');
                
        $id = $arguments['id'];
        
        return DataFetcher::getInstance()->getFormulaBought($id);
    }

    public final function get_product_groups(array $arguments): array
    {
        if (! Perm::checkPermission(PERM::CAFET_ADMIN_GET_PRODUCTS, $this->user))
            cafet_throw_error('02_002');

        $return = array();

        foreach (DataFetcher::getInstance()->getProductGroups() as $group)
            $return[] = json_decode($group->__toString());

        return $return;
    }

    public final function get_product_group(array $arguments): ?ProductGroup
    {
        if (! Perm::checkPermission(PERM::CAFET_ADMIN_GET_PRODUCTS, $this->user))
            cafet_throw_error('02_002');

        if (! isset($arguments['id']))
            cafet_throw_error('03-006');
        if (gettype($arguments['id']) != 'integer')
            cafet_throw_error('03-005', 'id must be an integer');

        $id = $arguments['id'];

        return DataFetcher::getInstance()->getProductGroup($id);
    }

    public final function get_group_products(array $arguments): array
    {
        if (! Perm::checkPermission(PERM::CAFET_ADMIN_GET_PRODUCTS, $this->user))
            cafet_throw_error('02_002');

        if (! isset($arguments['id']))
            cafet_throw_error('03-006');
        if (gettype($arguments['id']) != 'integer')
            cafet_throw_error('03-005', 'id must be an integer');
        if (isset($arguments['show_hiddens']) && gettype($arguments['show_hiddens']) != 'boolean')
            cafet_throw_error('03-005', 'show_hiddens must be a boolean');

        $id = $arguments['id'];
        $show_hiddens = isset($arguments['show_hiddens']) ? $arguments['show_hiddens'] : false;

        $return = array();

        foreach (DataFetcher::getInstance()->getGroupProducts($id, $show_hiddens) as $product)
            $return[] = json_decode($product->__toString());

        return $return;
    }

    public final function get_formula_bought_products(array $arguments): array
    {
        if (! Perm::checkPermission(PERM::CAFET_ADMIN_GET_EXPENSES, $this->user))
            cafet_throw_error('02_002');

        if (! isset($arguments['id']))
            cafet_throw_error('03-006');
        if (gettype($arguments['id']) != 'integer')
            cafet_throw_error('03-005', 'id must be an integer');

        $id = $arguments['id'];

        $return = array();

        foreach (DataFetcher::getInstance()->getFormulaBoughtProducts($id) as $product)
            $return[] = json_decode($product->__toString());

        return $return;
    }

    public final function get_product(array $arguments): ?Product
    {
        if (! Perm::checkPermission(PERM::CAFET_ADMIN_GET_PRODUCTS, $this->user))
            cafet_throw_error('02_002');

        if (! isset($arguments['id']))
            cafet_throw_error('03-006');
        if (gettype($arguments['id']) != 'integer')
            cafet_throw_error('03-005', 'id must be an integer');

        $id = $arguments['id'];

        return DataFetcher::getInstance()->getProduct($id);
    }

    public final function get_formulas(array $arguments): array
    {
        if (! Perm::checkPermission(PERM::CAFET_ADMIN_GET_FORMULAS, $this->user))
            cafet_throw_error('02_002');

        if (isset($arguments['show_hiddens']) && gettype($arguments['show_hiddens']) != 'boolean')
            cafet_throw_error('03-005', 'show_hiddens must be a boolean');

        $show_hiddens = isset($arguments['show_hiddens']) ? $arguments['show_hiddens'] : false;

        $return = array();

        foreach (DataFetcher::getInstance()->getFormulas($show_hiddens) as $formula)
            $return[] = json_decode($formula->__toString());

        return $return;
    }

    public final function get_formula(array $arguments): ?Formula
    {
        if (! Perm::checkPermission(PERM::CAFET_ADMIN_GET_FORMULAS, $this->user))
            cafet_throw_error('02_002');

        if (! isset($arguments['id']))
            cafet_throw_error('03-006');
        if (gettype($arguments['id']) != 'integer')
            cafet_throw_error('03-005', 'id must be an integer');

        $id = $arguments['id'];

        return DataFetcher::getInstance()->getFormula($id);
    }

    public final function get_formula_choices(array $arguments): array
    {
        if (! Perm::checkPermission(PERM::CAFET_ADMIN_GET_FORMULAS, $this->user))
            cafet_throw_error('02_002');

        if (! isset($arguments['id']))
            cafet_throw_error('03-006');
        if (gettype($arguments['id']) != 'integer')
            cafet_throw_error('03-005', 'id must be an integer');

        $id = $arguments['id'];

        $return = array();

        foreach (DataFetcher::getInstance()->getFormulaChoices($id) as $choice)
            $return[] = json_decode($choice->__toString());

        return $return;
    }

    public final function search_client(array $arguments): array
    {
        if (! Perm::checkPermission(PERM::CAFET_ADMIN_GET_CLIENTS, $this->user))
            cafet_throw_error('02_002');

        if (! isset($arguments['expression']))
            cafet_throw_error('03-006');
        if (gettype($arguments['expression']) != 'string')
            cafet_throw_error('03-005', 'expression must be a string');

        $expression = $arguments['expression'];

        $return = array();

        foreach (DataFetcher::getInstance()->searchClient($expression) as $client)
            $return[] = json_decode($client->__toString());

        return $return;
    }
}

