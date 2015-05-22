<?php
namespace Api\PreProcessor;

use Zend\Mvc\MvcEvent;

/**
 * ResponsÃ¡vel por fazer o prÃ©-processamento das requisiÃ§Ãµes da APi
 *
 * @category Api
 * @package PreProcessor
 * @author Elton Minetto<eminetto@coderockr.com>
 */
class PreProcessor
{

    /**
     * Executado no prÃ©-processamento, antes de qualquer action
     * Verifica se o usuÃ¡rio tem permissÃ£o de acessar o recurso
     *
     * @param MvcEvent $e            
     * @return null|\Zend\Http\PhpEnvironment\Response
     */
    public function process(MvcEvent $e)
    {
        $this->configureEnvironment($e);
        
        $routeMatch = $e->getRouteMatch();
        $routeName = $routeMatch->getMatchedRouteName();
        $module = $routeMatch->getParam('module', false);
        
        // verifica se a entidade ou o service sendo invocados estÃ£o disponÃ­veis
        switch ($routeName) {
            case 'restful':
                $request = $routeMatch->getParam('entity', false);
                break;
        }
        
        $moduleConfig = null;
        switch ($routeName) {
            case 'restful':
                $moduleConfig = include __DIR__ . '/../../../../' . ucfirst($module) . '/config/entities.config.php';
                break;
        }
        
        if (! $moduleConfig) {
            throw new \Exception("Caminho invÃ¡lido");
        }
        
        if (! isset($moduleConfig[$request])) {
            throw new \Exception("NÃ£o permitido");
        }
        // acesso requer um token vÃ¡lido e permissÃµes de acesso
        if ($moduleConfig[$request]['authorization'] == 1) {
            $token = $e->getRequest()->getHeaders('Authorization');
            if (! $token) {
                $response = $e->getResponse();
                $response->setStatusCode(401);
                return $response;
            }
            
            return $this->checkAuthorization($token, $module . '.' . $request);
        }
        return true;
    }

    /**
     * Executa o teste da autorizaÃ§Ã£o
     * 
     * @param Auth $auth
     *            ServiÃ§o de auth
     * @param Header $token
     *            Token enviado na requisiÃ§Ã£o
     * @param string $request
     *            ServiÃ§o sendo requisitado
     * @return boolean
     */
    private function checkAuthorization($token, $request)
    {
        /* definir a estratÃ©gia de autenticaÃ§Ã£o e autorizaÃ§Ã£o */
        return true;
    }

    /**
     * Verifica se a api estÃ¡ sendo acessada de um ambiente de testes
     * e configura o ambiente
     * 
     * @param MvcEvent $e
     *            Evento
     * @return void
     */
    private function configureEnvironment(MvcEvent $e)
    {
        if (! method_exists($e->getRequest(), 'getHeaders')) {
            return;
        }
        
        $env = $e->getRequest()->getHeaders('Environment');
        if ($env) {
            switch ($env->getFieldValue()) {
                case 'testing':
                    putenv("ENV=testing");
                    break;
                case 'jenkins':
                    putenv("ENV=jenkins");
                    break;
            }
        }
        return;
    }
}