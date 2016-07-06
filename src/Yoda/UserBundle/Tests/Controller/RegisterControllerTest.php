<?php

namespace Yoda\EventBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegisterControllerTest extends WebTestCase
{
    public function testRegister()
    {
        $client = static::createClient(array(), array('HTTP_HOST' => 'localhost:8000'));
        $container = self::$kernel->getContainer();
        $em = $container->get('doctrine')->getManager();
        $userRepo = $em->getRepository('UserBundle:User');
        $userRepo->createQueryBuilder('u')
            ->delete()
            ->getQuery()
            ->execute();

        $crawler = $client->request('GET', '/register');
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Register', $response->getContent());

        $usernameVal = $crawler
            ->filter('#register_form_username')
            ->attr('value');

        $this->assertEquals('Leia', $usernameVal);

        $form = $crawler->selectButton('Register!')->form();

        $form['register_form[username]'] = 'nunu5';
        $form['register_form[email]'] = 'nunu5@user.com';
        $form['register_form[plainPassword][first]'] = 'P3ssword';
        $form['register_form[plainPassword][second]'] = 'P3ssword';

        $crawler = $client->submit($form);
        //print_r($client->getResponse()->getContent());die;
        $this->assertTrue($client->getResponse()->isRedirect());
        $client->followRedirect();
        $this->assertContains(
            'Welcome to the Death Star, have a magical day!',
            $client->getResponse()->getContent()
        );
        /*$this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertRegexp(
            '/This value should not be blank/',
            $client->getResponse()->getContent()
        );*/
    }
}
