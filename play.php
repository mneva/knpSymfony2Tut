<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpKernel\Kernel;
use Yoda\EventBundle\Entity\Event;

// If you don't want to setup permissions the proper way, just uncomment the following PHP line
// read http://symfony.com/doc/current/book/installation.html#checking-symfony-application-configuration-and-setup
// for more information
umask(0000);

// This check prevents access to debug front controllers that are deployed by accident to production servers.
// Feel free to remove this, extend it, or make something more sophisticated.
/*if (isset($_SERVER['HTTP_CLIENT_IP'])
    || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
    || !(in_array(@$_SERVER['REMOTE_ADDR'], ['127.0.0.1', 'fe80::1', '::1']) || php_sapi_name() === 'cli-server')
) {
    header('HTTP/1.0 403 Forbidden');
    exit('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
}*/

/**
 * @var Composer\Autoload\ClassLoader $loader
 */
$loader = require __DIR__ . '/app/autoload.php';
Debug::enable();

require_once __DIR__.'/app/AppKernel.php';

$kernel = new AppKernel('dev', true);
$kernel->loadClassCache();
$request = Request::createFromGlobals();
$kernel->boot();

$container = $kernel->getContainer();

if (Kernel::VERSION_ID >= 20500 && Kernel::VERSION_ID < 30000) {
    $container->enterScope('request');
    $container->set('request', $request);
}

/*$templating = $container->get('templating');

echo $templating->render(
    'EventBundle:Default:index.html.twig',
    array(
        'name' => 'Yoda',
        'count' => 5,
    )
);*/

$event = new Event();
$event->setName('Darth\'s surprise birthday party');
$event->setLocation('Deathstar');
$event->setTime(new \DateTime('tomorrow noon'));
//$event->setDetails('Ha! Darth Hates surprises!!!!');

$em = $container->get('doctrine')->getManager();
$user = $em
    ->getRepository('UserBundle:User')
    ->findOneBy(array('username' => 'wayne'));

$user->setPlainPassword('new');
$em->persist($user);
$em->flush();
/*foreach ($user->getEvents() as $event) {
    var_dump($event->getName());
}*/
//$em->persist($event);
//$em->flush();