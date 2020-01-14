<?php
/**
 * Created by PhpStorm.
 * User: Ppito
 * Date: 13/01/2020
 * Time: 8:45 PM
 *
 * @link      https://github.com/Ppito/laminas-whoops for the canonical source repository
 * @copyright Copyright (c) 2020 Mickael TONNELIER.
 * @license   https://github.com/Ppito/laminas-whoops/blob/master/LICENSE.md The MIT License
 */

namespace WhoopsErrorHandler\Service;

use Interop\Container\ContainerInterface;
use Laminas\EventManager\EventManager;

abstract class ServiceAbstract {

    /** @var array|null */
    protected $options = [];
    /** @var ContainerInterface */
    protected $container;
    /** @var EventManager|null  */
    protected $eventManager = null;

    /**
     * HandlerAbstract constructor.
     *
     * @param ContainerInterface $container
     * @param array              $options
     * @return self
     */
    public function __construct(ContainerInterface $container, $options = []) {
        $this->options      = $options;
        $this->container    = $container;
        $this->eventManager = $container->has('EventManager') ?
            $container->get('EventManager') :
            null;
        return $this;
    }

    /**
     * Configure Service Handler
     *
     * @return void
     */
    abstract public function configure(): void;

    /**
     * @return array|null
     */
    public function getOptions() {
        return $this->options;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer() {
        return $this->container;
    }
}
