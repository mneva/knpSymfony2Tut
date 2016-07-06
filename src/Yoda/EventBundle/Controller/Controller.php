<?php
namespace Yoda\EventBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Yoda\EventBundle\Entity\Event;

class Controller extends BaseController
{
    /**
     * @return \Symfony\Component\Security\Core\Security
     */
    public function getSecurityAuth()
    {
        return $this->container->get('security.authorization_checker');
    }

    public function getSecurityToken()
    {
        return $this->container->get('security.token_storage');
    }

    public function enforceOwnerSecurity(Event $event)
    {
        $user = $this->getUser();

        if ($user != $event->getOwner()) {
            throw $this->createAccessDeniedException('You are not the owner!!!');
        }
    }
}