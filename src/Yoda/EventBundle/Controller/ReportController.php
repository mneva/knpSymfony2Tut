<?php
/**
 * Created by PhpStorm.
 * User: neo
 * Date: 6/23/16
 * Time: 4:15 PM
 */

namespace Yoda\EventBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Yoda\EventBundle\Entity\Event;
use Yoda\EventBundle\Reporting\EventReportManager;

class ReportController extends Controller
{
    /**
     * @Route("/events/report/recentlyUpdated.csv")
     */
    public function updatedEventsAction()
    {
        $eventReportManager = $this->container->get('event_report_manager');
        $content = $eventReportManager->getRecentlyUpdatedReport();

        $response = new Response($content);
        $response->headers->set('Content-Type', 'text/csv');

        return $response;
    }
}