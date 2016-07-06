<?php namespace Yoda\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Yoda\UserBundle\Entity\User;

class LoadUsers implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('darth');
        $user->setPlainPassword('darthpass');
        $user->setEmail('darth@deathstar.com');
        $manager->persist($user);

        $admin = new User();
        $admin->setUsername('wayne');
        $admin->setPlainPassword('waynepass');
        $admin->setRoles(array('ROLE_ADMIN'));
        $admin->setEmail('wayne@deathstar.com');
        $manager->persist($admin);

        $manager->flush();
    }

    /*private function encodePassword(User $user, $plainPassword)
    {
        $encoder = $this->container->get('security.encoder_factory')
            ->getEncoder($user);

        return $encoder->encodePassword($plainPassword, $user->getSalt());
    }*/

    public function getOrder()
    {
        return 10;
    }
}