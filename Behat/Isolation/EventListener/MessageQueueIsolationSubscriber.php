<?php

namespace Oro\Bundle\TestFrameworkBundle\Behat\Isolation\EventListener;

use Behat\Behat\EventDispatcher\Event\AfterFeatureTested;
use Behat\Behat\EventDispatcher\Event\BeforeFeatureTested;
use Behat\Behat\EventDispatcher\Event\BeforeStepTested;
use Oro\Bundle\TestFrameworkBundle\Behat\Isolation\SkipIsolatorsTrait;
use Oro\Bundle\TestFrameworkBundle\Behat\Processor\MessageQueueProcessorAwareInterface;
use Oro\Bundle\TestFrameworkBundle\Behat\Processor\MessageQueueProcessorAwareTrait;
use Oro\Bundle\TestFrameworkBundle\Tests\Behat\Context\OroMainContext;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Subscriber that processed message queue during test execution
 */
class MessageQueueIsolationSubscriber implements EventSubscriberInterface, MessageQueueProcessorAwareInterface
{
    use SkipIsolatorsTrait, MessageQueueProcessorAwareTrait;

    /** @var KernelInterface */
    private $kernel;

    /** @var OutputInterface */
    private $output;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            BeforeStepTested::BEFORE => ['beforeStep'],
            BeforeFeatureTested::BEFORE => ['beforeFeature'],
            AfterFeatureTested::AFTER => ['afterFeature'],
        ];
    }

    /**
     * @param BeforeStepTested $event
     */
    public function beforeStep(BeforeStepTested $event)
    {
        if ($this->skip) {
            return;
        }

        if (preg_match(OroMainContext::SKIP_WAIT_PATTERN, $event->getStep()->getText())) {
            // Don't wait when we need assert the flash message, because it can disappear until ajax in process
            return;
        }

        $this->messageQueueProcessor->waitWhileProcessingMessages();
    }

    public function beforeFeature()
    {
        if ($this->skip) {
            return;
        }

        $this->output->writeln('<info>Start message queue</info>');
        $this->messageQueueProcessor->startMessageQueue();
    }

    public function afterFeature()
    {
        if ($this->skip) {
            return;
        }

        $this->output->writeln('<info>Stop message queue</info>');
        $this->messageQueueProcessor->stopMessageQueue();

        $this->output->writeln('<info>Cleanup message queue</info>');
        $this->messageQueueProcessor->cleanUp();
    }

    /**
     * @param OutputInterface $output
     */
    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
    }
}
