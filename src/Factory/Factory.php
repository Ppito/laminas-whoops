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

namespace WhoopsErrorHandler\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use WhoopsErrorHandler\Handler\HandlerInterface;

class Factory implements FactoryInterface
{
    /**
     * Invoke Handler
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return HandlerInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $config = $container->has('config') ? $container->get('config') : [];
        $config = $config['whoops'] ?? [];

        return new $requestedName($container, $config);
    }
}
