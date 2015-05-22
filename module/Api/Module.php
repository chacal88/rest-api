<?php
namespace Api;

use Zend\Mvc\MvcEvent;
use Api\PostProcessor\PostProcessor;
use Api\PreProcessor\PreProcessor;
class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
    /**
     * Executada no bootstrap do mÃ³dulo
     *
     * @param MvcEvent $e
     */
    public function onBootstrap($e)
    {
        /** @var \Zend\ModuleManager\ModuleManager $moduleManager */
        $moduleManager = $e->getApplication()->getServiceManager()->get('modulemanager');
        /** @var \Zend\EventManager\SharedEventManager $sharedEvents */
        $sharedEvents = $moduleManager->getEventManager()->getSharedManager();
    
        //adiciona eventos ao mÃ³dulo
        //prÃ© e pÃ³s-processadores do controller Rest
        $sharedEvents->attach('Api\Controller\RestController', MvcEvent::EVENT_DISPATCH, array(new PostProcessor, 'process'), -100);
        $sharedEvents->attach('Api\Controller\RestController', MvcEvent::EVENT_DISPATCH, array(new PreProcessor, 'process'), 100);
    
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}
