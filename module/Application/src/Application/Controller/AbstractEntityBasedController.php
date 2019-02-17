<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;

/**
 * The abstract orm entity based action controller
 */
abstract class AbstractEntityBasedController extends AbstractActionController
{
    public function __construct(\Zend\ServiceManager\ServiceLocatorInterface $sl )
    {
        $this->serviceLocator = $sl;
    }

    /**
     * Returns the doctrine entity manager for the user model handling.
     * @return \Doctrine\ORM\EntityManagerInterface
     */
    public function getEntityManager()
    {
        return $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
    }
    
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }
}
