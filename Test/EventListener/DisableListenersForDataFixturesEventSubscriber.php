<?php

namespace Oro\Bundle\PhpUnitBundle\Test\EventListener;

use Oro\Bundle\PhpUnitBundle\Test\Event\DisableListenersForDataFixturesEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * The subscriber that can be used to add event listener(s) that should be disabled
 * during loading of data fixtures for functional tests.
 * @see \Oro\Bundle\PhpUnitBundle\Test\WebTestCase::getListenersThatShouldBeDisabledDuringDataFixturesLoading
 */
class DisableListenersForDataFixturesEventSubscriber implements EventSubscriberInterface
{
    /** @var string[] */
    private $listeners = [];

    /**
     * @param string|string[] $listener
     */
    public function __construct($listener)
    {
        $this->listeners = (array)$listener;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [DisableListenersForDataFixturesEvent::NAME => 'collectListeners'];
    }

    public function collectListeners(DisableListenersForDataFixturesEvent $event)
    {
        foreach ($this->listeners as $listener) {
            $event->addListener($listener);
        }
    }
}
