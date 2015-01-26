<?php

namespace Engage360d\Bundle\TakedaBundle\Processing;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Engage360d\Bundle\TakedaBundle\Entity\TestResult;

class TestResultFillerListener
{
    private $filler;
  
    public function __construct(TestResultFiller $filler)
    {
        $this->filler = $filler;
    }
  
    public function postPersist(LifecycleEventArgs $args)
    {
        $this->fill($args);
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->fill($args);
    }
  
    public function postLoad(LifecycleEventArgs $args)
    {
        $this->fill($args);
    }
  
    private function fill(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof TestResult) {
            $this->filler->fill($entity);
        }
    }
}
