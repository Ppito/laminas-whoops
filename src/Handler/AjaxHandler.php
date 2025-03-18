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

use InvalidArgumentException;
use Interop\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Whoops\Handler\JsonResponseHandler as WhoopsAjaxHandler;

class AjaxHandler extends HandlerAbstract implements HandlerInterface {

    /**
     * AjaxHandler constructor.
     *
     * @param ContainerInterface $container
     * @param array $options
     * @return self
     * @throws ContainerExceptionInterface Error while retrieving the entry
     */
    public function __construct(ContainerInterface $container, array $options = []) {
        parent::__construct($container, $options);
        $this->handler = new WhoopsAjaxHandler();
        $this->configure();
        return $this;
    }

    /**
     * Inject an editor into the whoops configuration.
     *
     * @return void
     * @throws InvalidArgumentException for an invalid show trace option.
     */
    public function configure(): void {
        /** @var WhoopsAjaxHandler $handler */
        $handler = $this->getHandler();
        $handler->setJsonApi(true);

        if (!isset($this->options['show_trace']) || !isset($this->options['show_trace']['ajax_display'])) {
            return;
        }

        $show_trace = $this->options['show_trace']['ajax_display'];

        if (! is_bool($show_trace)) {
            throw new InvalidArgumentException(sprintf(
                'Whoops show trace option must be a boolean; received "%s"',
                (is_object($show_trace) ? get_class($show_trace) : gettype($show_trace))
            ));
        }
        $handler->addTraceToOutput($show_trace);
    }
}
