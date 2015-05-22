<?php
namespace Api\PostProcessor;

use Zend\Mvc\MvcEvent;

/**
 * ResponsÃ¡vel por fazer o pÃ³s-processamento das requisiÃ§Ãµes da APi
 * 
 * @category Api
 * @package PostProcessor
 * @author  Elton Minetto<eminetto@coderockr.com>
 */
class PostProcessor
{
    /**
     * Executado no pÃ³s-processamento, apÃ³s qualquer action
     * Verifica o formato requisitado (json ou xml) e gera a saÃ­da correspondente
     * 
     * @param MvcEvent $e
     * @return null|\Zend\Http\PhpEnvironment\Response
     */
    public function process(MvcEvent $e)
    {
        $routeMatch = $e->getRouteMatch();
        $formatter = $routeMatch->getParam('formatter', false);

      //  $serviceLocator = $e->getTarget()->getServiceLocator();

        if ($formatter !== false) {
            if ($e->getResult() instanceof \Zend\View\Model\ViewModel) {
                $vars = null;
                if (is_array($e->getResult()->getVariables())) {
                    $vars = $e->getResult()->getVariables();
                } 
            } else {
                $vars = $e->getResult();
            }

            switch ($formatter) {
                case 'json':
                    $postProcessor = new Json;
                    break;
                case 'xml':
                    $postProcessor = new Xml;
                    break;
            }
            $postProcessor->setResponse($e->getResponse());
            $postProcessor->setVars($vars);
            
            $postProcessor->process();

            return $postProcessor->getResponse();
        }

        return null;
    }
}