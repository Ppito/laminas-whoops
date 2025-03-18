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

namespace WhoopsErrorHandler;

use Interop\Container\ContainerInterface;
use Laminas\View\Model\ViewModel;
use Psr\Container\ContainerExceptionInterface;
use Whoops\Run;
use Laminas\Http\Response;
use Laminas\EventManager\EventInterface;
use Laminas\ModuleManager\Feature\BootstrapListenerInterface;
use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use Laminas\Mvc\Application;
use Laminas\Mvc\MvcEvent;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Http\Response as HttpResponse;
use Laminas\Console\Response as ConsoleResponse;

class Module implements ConfigProviderInterface, BootstrapListenerInterface {

    /** @var string */
    protected string $template = 'laminas_whoops/simple_error';
    /** @var Run */
    protected ?Run $whoops;
    /** @var string[] */
    protected array $ignoredExceptions = [];

    /**
     * Return default laminas-serializer configuration for laminas-mvc applications.
     */
    public function getConfig() {
        return include __DIR__ . '/../config/module.config.php';
    }

    /**
     * Listen to the bootstrap event
     *
     * @param MvcEvent|EventInterface $e
     * @return void
     * @throws ContainerExceptionInterface Error while retrieving the entry
     */
    public function onBootstrap(MvcEvent|EventInterface $e): void {
        $application = $e->getApplication();
        /** @var ServiceManager $serviceManager */
        $serviceManager = $application->getServiceManager();

        $this->configureService($serviceManager);

        if ($this->whoops) {
            $eventManager = $application->getEventManager();
            $eventManager->attach(MvcEvent::EVENT_RENDER_ERROR, [
                $this,
                'prepareException',
            ]);
            $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, [
                $this,
                'prepareException',
            ]);
        }
    }

    /**
     * Configure Whoops Service
     *
     * @param ContainerInterface $container
     * @throws ContainerExceptionInterface Error while retrieving the entry
     */
    protected function configureService(ContainerInterface $container): void {
        $config = $container->has('config') ? $container->get('config') : [];
        $config = $config['whoops'] ?? [];

        $serviceName = $config['visibility_service_name'] ?? null;
        /** @var Service\VisibilityServiceInterface $visibilityService */
        $visibilityService = $serviceName && $container->has($serviceName) ?
            $container->get($serviceName) :
            null;

        if ($visibilityService instanceof Service\VisibilityServiceInterface && !$visibilityService->canAttachEvent()) {
            return;
        }

        $this->ignoredExceptions = (array) ($config['ignored_exceptions'] ?? []);
        $this->setTemplate($config['template_render'] ?? $this->template);

        /** @var Service\WhoopsService $service */
        $service = $container->has(Service\WhoopsService::class) ?
            $container->get(Service\WhoopsService::class) :
            null;

        if ($service) {
            $this->whoops = $service->getService();
        }
    }

    /**
     * Whoops handle exceptions
     *
     * @param MvcEvent $e
     */
    public function prepareException(MvcEvent $e): void {
        // Do nothing if no error in the event
        if (empty($error = $e->getError())) {
            return;
        }

        // Do nothing if the result is a response object
        if ($e->getResult() instanceof Response) {
            return;
        }

        switch ($error) {
            case Application::ERROR_CONTROLLER_NOT_FOUND:
            case Application::ERROR_CONTROLLER_INVALID:
            case Application::ERROR_ROUTER_NO_MATCH:
                // Specifically not handling these
                return;

            case Application::ERROR_EXCEPTION:
            default:
                // Bail out if we're explicitly told to ignore this exception:
                if (in_array(get_class($e->getParam('exception')), $this->ignoredExceptions)) {
                    return;
                }
                // Set writeToOutput to false for rendered output with laminas-view
                $this->whoops->writeToOutput(false);
                $result = $this->whoops->handleException($e->getParam('exception'));
                $model  = new ViewModel(['result' => $result]);

                $model->setTemplate($this->getTemplate());
                $e->setResult($model);

                $response = $e->getResponse();
                if ($response instanceof HttpResponse) {
                    if ($response->isSuccess()) {
                        $response->setStatusCode(HttpResponse::STATUS_CODE_500);
                    }
                } elseif (class_exists(ConsoleResponse::class) && $response instanceof ConsoleResponse) {
                    $statusCode = $response->getErrorLevel();
                    if ($statusCode === 0) {
                        $response->setErrorLevel(E_ERROR);
                    }
                } elseif (!$response) {
                    if (class_exists(HttpResponse::class)) {
                        $response = new HttpResponse();
                        $response->setStatusCode(HttpResponse::STATUS_CODE_500);
                        $e->setResponse($response);
                    } elseif (class_exists(ConsoleResponse::class)) {
                        $response = new ConsoleResponse();
                        $response->setErrorLevel(E_ERROR);
                        $e->setResponse($response);
                    }
                }
                break;
        }
    }

    /**
     * Set Template
     *
     * @param string $template
     * @return self
     */
    public function setTemplate(string $template): static {
        $this->template = $template;
        return $this;
    }

    /**
     * Retrieve the template
     *
     * @return string
     */
    public function getTemplate(): string {
        return $this->template;
    }
}
