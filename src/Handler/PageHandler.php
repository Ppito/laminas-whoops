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

namespace WhoopsErrorHandler\Handler;

use Interop\Container\ContainerInterface;
use Whoops\Handler\PrettyPageHandler as WhoopsPageHandler;

class PageHandler extends HandlerAbstract implements HandlerInterface {

    /**
     * PageHandler constructor.
     *
     * @param ContainerInterface $container
     * @param array              $options
     * @return self
     */
    public function __construct(ContainerInterface $container, $options = []) {
        parent::__construct($container, $options);
        $this->handler = new WhoopsPageHandler();
        $this->configure();
        return $this;
    }

    /**
     * Inject an editor into the whoops configuration.
     *
     * @return void
     * @throws \InvalidArgumentException for an invalid editor definition.
     */
    public function configure(): void {
        /** @var WhoopsPageHandler $handler */
        $handler = $this->getHandler();

        $handler->setApplicationPaths([__FILE__]);

        if (!isset($this->options['editor'])) {
            return;
        }

        $editor = $this->options['editor'];
        if (!is_callable($editor) && !is_string($editor)) {
            throw new \InvalidArgumentException(sprintf(
                'Whoops editor must be a string editor name, string service name, or callable; received "%s"',
                (is_object($editor) ? get_class($editor) : gettype($editor))
            ));
        }

        if (is_string($editor) && $this->container->has($editor)) {
            $editor = $this->container->get($editor);
        }

        $handler->setEditor($editor);
    }
}
