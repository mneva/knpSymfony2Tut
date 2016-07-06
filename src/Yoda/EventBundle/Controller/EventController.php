<?php

namespace Yoda\EventBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Yoda\EventBundle\Entity\Event;
use Yoda\EventBundle\Form\EventType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Event controller.
 *
 */
class EventController extends Controller
{
    /**
     * Lists all Event entities.
     *
     * @Template()
     * @Route("/", name="event")
     */
    public function indexAction()
    {
       /* $em = $this->getDoctrine()->getManager();

        $events = $em
            ->getRepository('EventBundle:Event')
            ->getUpcomingEvents();*/

        return  array(
            //'events' => $events,
        );
    }

    /**
     * Creates a new Event entity.
     *
     */
    public function newAction(Request $request)
    {
        $this->enforceUserSecurity('ROLE_EVENT_CREATE');

        $event = new Event();
        $form = $this->createForm('Yoda\EventBundle\Form\EventType', $event);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $user = $this->getUser();

            $event->setOwner($user);
            $em = $this->getDoctrine()->getManager();
            $em->persist($event);
            $em->flush();

            return $this->redirectToRoute('event_show', array('slug' => $event->getSlug()));
        }

        return $this->render('event/new.html.twig', array(
            'event' => $event,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Event entity.
     *
     */
    public function showAction($slug)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EventBundle:Event')->findOneBy(array('slug' => $slug));

        if (!$entity) {
            throw $this->createNotFoundException('No event with id '.$slug);
        }

        $deleteForm = $this->createDeleteForm($entity);

        return $this->render('EventBundle:Event:show.html.twig', array(
            'event' => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Event entity.
     *
     */
    public function editAction(Request $request, Event $event)
    {
        $this->enforceUserSecurity();

        if (!$event) {
            throw $this->createNotFoundException('Unable to find Event entity.');
        }

        $this->enforceOwnerSecurity($event);

        $deleteForm = $this->createDeleteForm($event);
        $editForm = $this->createForm('Yoda\EventBundle\Form\EventType', $event);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($event);
            $em->flush();

            return $this->redirectToRoute('event_edit', array('id' => $event->getId()));
        }

        return $this->render('event/edit.html.twig', array(
            'event' => $event,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Event entity.
     *
     */
    public function deleteAction(Request $request, Event $event)
    {
        $this->enforceUserSecurity();

        if (!$event) {
            throw $this->createNotFoundException('Unable to find Event entity.');
        }

        $this->enforceOwnerSecurity($event);

        $form = $this->createDeleteForm($event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($event);
            $em->flush();
        }

        return $this->redirectToRoute('event');
    }

    /**
     * Creates a form to delete a Event entity.
     *
     * @param Event $event The Event entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Event $event)
    {
        $this->enforceUserSecurity();

        if (!$event) {
            throw $this->createNotFoundException('Unable to find Event entity.');
        }

        $this->enforceOwnerSecurity($event);

        return $this->createFormBuilder()
            ->setAction($this->generateUrl('event_delete', array('id' => $event->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    private function enforceUserSecurity($role = 'ROLE_USER')
    {
        if (!$this->getSecurityAuth()->isGranted($role)) {
            throw $this->createAccessDeniedException('Need '.$role);
        }
    }

    public function attendAction($id, $format)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var  $event \Yoda\EventBundle\Entity\Event */
        $event = $em->getRepository('EventBundle:Event')->find($id);

        if (!$event) {
            throw $this->createNotFoundException('No event found for id '.$id);
        }

        if (!$event->hasAttendee($this->getUser())) {
            $event->getAttendees()->add($this->getUser());
        }

        $em->persist($event);
        $em->flush();

        return $this->createAttendingResponse($event, $format);
    }

    public function unattendAction($id, $format)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var  $event \Yoda\EventBundle\Entity\Event */
        $event = $em->getRepository('EventBundle:Event')->find($id);

        if (!$event) {
            throw $this->createNotFoundException('No event found for id '.$id);
        }

        if ($event->hasAttendee($this->getUser())) {
            $event->getAttendees()->removeElement($this->getUser());
        }

        $em->persist($event);
        $em->flush();

        return $this->createAttendingResponse($event, $format);
    }

    private function createAttendingResponse(Event $event, $format)
    {
        if ($format == 'json') {
            $data = array(
                'attending' =>$event->hasAttendee($this->getUser())
            );

            $response = new JsonResponse($data);

            return $response;
        }

        $url = $this->generateUrl('event_show', array(
            'slug' => $event->getSlug(),
        ));

        return $this->redirect($url);
    }

    public function _upcomingEventsAction($max = null)
    {
        $em = $this->getDoctrine()->getManager();

        $events = $em->getRepository('EventBundle:Event')
            ->getUpcomingEvents($max);

        return $this->render('EventBundle:Event:_upcomingEvents.html.twig', array(
            'events' => $events,
        ));
    }
/*    private function enforceOwnerSecurity(Event $event)
    {
        $user = $this->getUser();

        if ($user != $event->getOwner()) {
            throw new AccessDeniedException('You are not the owner!');
        }
    }*/
}
