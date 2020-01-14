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

abstract class VisibilityServiceAbstract extends ServiceAbstract
    implements VisibilityServiceInterface {

    /**
     * Verify if the module can be loaded
     *
     * @return bool
     */
    abstract public function canAttachEvent(): bool;
}
