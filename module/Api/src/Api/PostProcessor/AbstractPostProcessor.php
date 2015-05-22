<?php
namespace Api\PostProcessor;

/**
 * Classe abstrata usada pelos pÃ³s-processadores
 *
 * @category Api
 * @package PostProcessor
 * @author Elton Minetto<eminetto@coderockr.com>
 */
abstract class AbstractPostProcessor
{

    /**
     *
     * @var array|null
     */
    protected $_vars = null;

    /**
     *
     * @var null|\Zend\Http\Response
     */
    protected $_response = null;

    /**
     *
     * @return null|\Zend\Http\Response
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /*
     * @param \Zend\Http\Response $response
     */
    public function setResponse(\Zend\Http\Response $response)
    {
        $this->_response = $response;
    }

    /**
     *
     * @param
     *            $vars
     */
    public function setVars($vars)
    {
        $this->_vars = $vars;
    }

    /**
     *
     * @abstract
     *
     */
    abstract public function process();
}