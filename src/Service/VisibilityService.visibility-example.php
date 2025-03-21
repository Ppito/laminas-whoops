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

namespace Application\Service;

use Psr\Container\ContainerExceptionInterface;
use WhoopsErrorHandler\Service\VisibilityServiceAbstract;
use WhoopsErrorHandler\Service\VisibilityServiceInterface;

class VisibilityService extends VisibilityServiceAbstract implements VisibilityServiceInterface {

    /**
     * @var @Model\User
     */
    protected $connectedUser = null;

    /**
     * Configure Service Handler
     * - Get Connected User
     *
     * @return void
     * @throws ContainerExceptionInterface Error while retrieving the entry
     */
    public function configure(): void {
        $container = $this->getContainer();

        $this->connectedUser = $container->has('User') ?
            $container->get('User') :
            null;
    }

    /**
     * Verify the role of the user
     *
     * @return bool
     */
    public function canAttachEvent(): bool {
        return $this->connectedUser ?
            $this->connectedUser->hasRole('Admin') :
            false;
    }
}
