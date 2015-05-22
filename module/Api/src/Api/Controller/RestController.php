<?php

/**
 * Classe responsÃ¡vel pelo acesso REST das entidades
 * 
 * @category Api
 * @package  Controller
 * @author   Elton Minetto <eminetto@coderockr.com>
 */
namespace Api\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Select;

class RestController extends AbstractRestfulController
{

    /**
     * Table Gateway
     */ 
    protected $tableGateway;

    /**
     * Objeto sendo manipulado
     */
    protected $tableObject;

    /**
     * Retorna uma lista de entidades
     *
     * http://zf2.dev/api/v1/album.album.json
     * http://zf2.dev/api/v1/album.album.xml
     * http://zf2.dev/api/v1/album.album.json?fields=title,id
     * http://zf2.dev/api/v1/album.album.json?fields=title,id&limit=1
     * http://zf2.dev/api/v1/album.album.json?limit=10&offset=5
     * http://zf2.dev/api/v1/album.album.json?nome=Elton&idade=33&fields=nome,cidade
     *
     * @return array
     */
    public function getList()
    {
        $fields = null;
        $limit = null;
        $offset = null;
        $order = null;
        
        $where = new Where();
        $query = $this->getRequest()->getQuery();
        if (isset($query['fields'])) {
            $fields = explode(",", $query['fields']);
            unset($query['fields']);
        }
        
        if (isset($query['limit'])) {
            $limit = $query['limit'];
            unset($query['limit']);
        }
        
        if (isset($query['offset'])) {
            $offset = $query['offset'];
            unset($query['offset']);
        }
        
        if (isset($query['order'])) {
            $order = explode(",", $query['order']);
            unset($query['order']);
        }
        
        // where
        if (count($query) > 0) {
            foreach ($query as $field => $condition) {
                $where->equalTo($field, $condition);
            }
        }
        
        $select = new Select();
        $select->from($this->getTableGateway()->tableGateway->getTable());
        
        if ($fields) {
            $select->columns($fields);
        }
        
        if ($where) {
            $select->where($where);
        }
        
        if ($limit) {
            $select->limit((int) $limit);
        }
        
        if ($offset) {
            $select->offset((int) $offset);
        }
        
        if ($order) {
            $select->order($order);
        }
        
        $result = $this->getTableGateway()->fetchAll($select);
        
        if (! $result) {
            $response = $this->getResponse();
            $response->setStatusCode(404);
            return $response;
        }
        $data = array();
        foreach ($result as $r) {
            $data[] = $r;
        }
        return $data;
    }

    /**
     * Retorna uma Ãºnica entidade
     *
     * http://zf2.dev:8080/api/v1/album.album.json/1
     *
     * @param int $id
     *            Id da entidade
     *            
     * @return array
     */
    public function get($id)
    {
        try {
            $entity = $this->getTableGateway()->get($id);
        } catch (\Exception $e) {
            $response = $this->getResponse();
            $response->setStatusCode(404);
            return $response;
        }
        return $entity->toArray();
    }

    /**
     * Cria uma nova entidade
     *
     * @param array $data
     *            Dados da entidade sendo salva
     *            
     * @return array
     */
    public function create($data)
    {
        $entity = $this->getTableObject();
        $entity->exchangeArray($data);
        $savedEntity = $this->getTableGateway()->save($entity);
        
        return $savedEntity->toArray();
    }

    /**
     * Atualiza uma entidade
     * 
     * @param int $id
     *            O cÃ³digo da entidade a ser atualizada
     * @param array $data
     *            Os dados sendo alterados
     *            
     * @return array Retorna a entidade atualizada
     */
    public function update($id, $data)
    {
        $entity = $this->getTableObject();
        $data = array_merge($this->get($id), $data);
        $entity->exchangeArray($data);
        return $this->getTableGateway()
            ->save($entity)
            ->toArray();
    }

    /**
     * Exclui uma entidade
     *
     * @param int $id
     *            Id da entidade sendo excluÃ­da
     *            
     * @return int
     */
    public function delete($id)
    {
        return $this->getTableGateway()->delete($id);
    }

    /**
     * Retorna uma instancia de TableGateway
     *
     * @return Zend\Db\TableGateway\TableGateway
     */
    protected function getTableGateway()
    {
        if (! isset($this->tableGateway)) {
            $this->getCallParameters();
        }
        return $this->tableGateway;
    }

    /**
     * Retorna uma instÃ¢ncia da entidade
     *
     * @return Core\Mode\Entity
     */
    protected function getTableObject()
    {
        if (! isset($this->tableObject)) {
            $this->getCallParameters();
        }
        
        return $this->tableObject;
    }

    /**
     * Recupera os parÃ¢metros da entidade a ser executada
     *
     * @return void
     */
    private function getCallParameters()
    {
        $module = $this->getEvent()
            ->getRouteMatch()
            ->getParam('module');
        $entity = $this->getEvent()
            ->getRouteMatch()
            ->getParam('entity');
        $moduleConfig = include __DIR__ . '/../../../../' . ucfirst($module) . '/config/entities.config.php';
        
        if (! isset($moduleConfig[$entity])) {
            $response = $this->getResponse();
            $response->setStatusCode(404);
            throw new \Exception('Undefined index: ' . $entity);
        }
        $sm = $this->getServiceLocator();
        $this->tableObject = $sm->get($moduleConfig[$entity]['class']);
        $this->tableGateway = $sm->get($moduleConfig[$entity]['tableGateway']);
    }
}