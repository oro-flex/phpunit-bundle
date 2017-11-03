<?php

namespace Oro\Bundle\TestFrameworkBundle\Behat\Faker\Provider;

use Faker\Generator;
use Faker\Provider\Base as BaseProvider;
use Oro\Bundle\TestFrameworkBundle\Provider\XssPayloadProvider;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

class XssProvider extends BaseProvider
{
    /**
     * @var int
     */
    protected static $idx = 0;

    /**
     * @var XssPayloadProvider
     */
    private $payloadProvider;

    /**
     * @var PasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var string
     */
    private $prefix = 'p';

    /**
     * @param Generator $generator
     * @param XssPayloadProvider $payloadProvider
     */
    public function __construct(
        Generator $generator,
        XssPayloadProvider $payloadProvider
    ) {
        parent::__construct($generator);
        $this->payloadProvider = $payloadProvider;
        $this->passwordEncoder = new MessageDigestPasswordEncoder();
    }

    /**
     * @param string $identifier
     * @param string|null $payloadType
     * @param null|string $elementId
     * @return string
     */
    public function xss($identifier = 'XSS', $payloadType = null, $elementId = null)
    {
        if (!$elementId) {
            $elementId = $this->prefix . ++self::$idx;
        }
        $jsPayload = sprintf('_x("%s","%s")', $elementId, $identifier);

        return sprintf($this->payloadProvider->getPayload($jsPayload, $payloadType, $elementId));
    }

    /**
     * @param string $password
     * @param string $salt
     * @return string
     */
    public function userPassword($password, $salt)
    {
        return $this->passwordEncoder->encodePassword($password, $salt);
    }

    /**
     * @param string $prefix
     * @return XssProvider
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }
}
