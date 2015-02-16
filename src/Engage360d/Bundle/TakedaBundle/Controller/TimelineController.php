<?php

namespace Engage360d\Bundle\TakedaBundle\Controller;

use JMS\Serializer\SerializationContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TimelineController extends Controller
{
    public function indexAction()
    {
        $user = $this->getUser();

        if ($user->getTestResults()->isEmpty()) {
            return $this->redirect($this->generateUrl('engage360d_takeda_risk_analysis'));
        }

        $timelineManager = $this->get('engage360d_takeda.timeline_manager');
        $timelineManager->setUser($user);

        $serializer = $this->get('jms_serializer');

        $timeline = $timelineManager->getTimeline();
        $context = new SerializationContext();
        $context->setSerializeNull(true);
        $timeline = $serializer->serialize($timeline, 'json', $context);

        $pills = array_map([$this, 'getPillArray'], $user->getPills()->toArray());
        $context = new SerializationContext();
        $context->setSerializeNull(true);
        $pills = $serializer->serialize($pills, 'json', $context);

        return $this->render('Engage360dTakedaBundle:Account:timeline.html.twig', array(
            'timeline' => $timeline,
            'pills' => $pills,
        ));
    }

    // TODO refactor
    protected function getPillArray($pill)
    {
        return [
            "id" => (string) $pill->getId(),
            "name" => $pill->getName(),
            "quantity" => $pill->getQuantity(),
            "repeat" => $pill->getRepeat(),
            "time" => $pill->getTime()->format('H:i:s'),
            "sinceDate" => $pill->getSinceDate()->format(\DateTime::ISO8601),
            "tillDate" => $pill->getTillDate()->format(\DateTime::ISO8601),
            "links" => [
                "user" => (string) $pill->getUser()->getId()
            ]
        ];
    }
}
