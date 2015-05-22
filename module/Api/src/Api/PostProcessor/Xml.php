<?php
namespace Api\PostProcessor;

use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializationContext;

/**
 * Classe concreta que retorna JSON
 *
 * @category Api
 * @package PostProcessor
 * @author Elton Minetto<eminetto@coderockr.com>
 */
class Xml extends AbstractPostProcessor
{

    /**
     * Retorna os cabeÃ§alhos e conteÃºdo no formato JSON
     */
    public function process($class = null)
    {
        $serializer = SerializerBuilder::create()->build();
        $content = null;
        
        if (isset($this->_vars['error-message'])) {
            $content = $serializer->serialize($this->_vars, 'xml');
        }
        
        if (! $content) {
            try {
                $content = array();
                if ($class) {
                    $content = $serializer->serialize($this->_vars, 'xml', SerializationContext::create()->setGroups(array(
                        $class
                    )));
                } else {
                    $content = $serializer->serialize($this->_vars, 'xml');
                }
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }
        }
        
        $this->_response->setContent($content);
        
        $headers = $this->_response->getHeaders();
        
        $headers->addHeaderLine('Content-Type', 'application/xml');
        $this->_response->setHeaders($headers);
    }
}