<?php
namespace cafetapi\webi\pages\controllers;

use cafetapi\io\ClientManager;
use cafetapi\io\UserManager;
use cafetapi\user\Group;
use cafetapi\user\Perm;
use cafetapi\webi\pages\views\ManageUsersView;
use cafetapi\webi\pages\views\CommonHTMLViews;

class ManageUsersController extends PageController
{

    private const GROUPS = [
        0 => 'Invités',
        1 => 'Consommateurs',
        2 => 'Gérant cafet\'',
        3 => 'Administrateurs cafet\'',
        4 => 'Administrateurs',
        5 => 'Super utilisateurs'
    ];
    private $html;
    private $user;
    
    public function __construct() {
        parent::__construct(new ManageUsersView(self::GROUPS));
    }

    public function buildPage()
    {
        /*
         * Permissions Check
         */
        if (! $this->builder->getUser()) {
            $this->builder->build([CommonHTMLViews::class, 'neddLogin']);
            return;
        } elseif (! $this->builder->getUser()->hasPermission(Perm::SITE_MANAGE_USERS)) {
            $this->builder->build([CommonHTMLViews::class, 'forbiden']);
            return;
        }
        
        
        $this->processForms();
        
        
        $this->builder->build([$this->view, 'html']);
    }
    
    private function processForms() {
        if (! isset($_REQUEST['userid'])) {
            return;
        }
        
        
        
        $manager = UserManager::getInstance();
        $this->user = $manager->getUserById($_REQUEST['userid']);
        
        if (! $this->user) {
            $this->view->setMessage('Aucun utilisateur avec l\'id ' . $_REQUEST['userid'] . 'n\'a été trouvé.');
        }
        
        if (isset($_REQUEST['setgroup'])) $this->updateUserGroup();
            
        elseif (isset($_REQUEST['member'])) $this->updateUserMembership();
            
        elseif (isset($_REQUEST['client'])) $this->updateUserCustomerAcount();
    }

    private function updateUserGroup()
    {
        if (array_key_exists(intval(@$_REQUEST['setgroup']), Group::GROUPS)) {

            if ($this->user->getGroup()->getId() != $_REQUEST['setgroup']) {
                UserManager::getInstance()->setGroup($this->user->getId(), $_REQUEST['setgroup']);
                $this->view->setMessage($this->user->getPseudo() . ' a été mis à jour.');
            } else
                $this->view->setMessage($this->user->getPseudo() . ' est déjà dans le groupe demandé.');
        } else
            $this->view->setMessage('Il n\'existe aucun groupe avec l\'id ' . $_REQUEST['setgroup'] . '.');
    }

    private function updateUserCustomerAcount()
    {
        $clientManager = ClientManager::getInstance();
        $client = $clientManager->getClient($this->user->getId());

        if ($client && ! $_REQUEST['client']) {

            // FIXME implement delete client
            $this->view->setMessage('Cette action n\'est pas implémentée.');
        } elseif (! $client && $_REQUEST['client']) {

            $clientManager->createCustomer($this->user->getId());
            $this->view->setMessage($this->user->getPseudo() . ' a été mis à jour.');
        } else
            $this->view->setMessage('Action impossible.');
    }

    private function updateUserMembership()
    {
        $clientManager = ClientManager::getInstance();
        if ($client = $clientManager->getClient($this->user->getId())) {
            $clientManager->setMember($client->getId(), boolval($_REQUEST['member']));
            $this->view->setMessage($this->user->getPseudo() . ' a été mis à jour.');
        } else
            $this->view->setMessage('Il n\'existe aucun compte client associé avec l\'utilisateur sélectionné.');
    }
}

