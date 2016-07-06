<?php namespace Yoda\EventBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Yoda\EventBundle\Entity\Event;

class LoadEvents implements FixtureInterface, OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $wayne = $manager->getRepository('UserBundle:User')
            ->findOneByUsernameOrEmail('wayne');

        $event1 = new Event();
        $event1->setName('Darth\'s Birthday Party');
        $event1->setLocation('Deathstar');
        $event1->setTime(new \DateTime('Friday noon'));
        $event1->setDetails('Ha! Darth HATES surprises');
        $event1->setOwner($wayne);
        $manager->persist($event1);

        $event2 = new Event();
        $event2->setName('Rebellion Fundraiser Bake Sale!');
        $event2->setLocation('Endor');
        $event2->setTime(new \DateTime('Thursday noon'));
        $event2->setDetails('Ewok pies! Support the rebellion');
        $event2->setOwner($wayne);
        $manager->persist($event2);

        $manager->flush();
    }

    public function getOrder()
    {
        return 20;
    }
}