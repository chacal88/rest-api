<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class ItemTable
{

    public $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll(Select $select = null)
    {
        if ($select) {
            return $this->tableGateway->selectWith($select);
        }
        return $this->tableGateway->select();
    }

    public function get($id)
    {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(array(
            'id' => $id
        ));
        $row = $rowset->current();
        if (! $row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function save(Item $item)
    {
        $data = array(
            'description' => $item->description,
            'done' => $item->done
        );
        
        $id = (int) $item->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
            $item->id = $this->tableGateway->getLastInsertValue();
        } else {
            if ($this->get($id)) {
                $this->tableGateway->update($data, array(
                    'id' => $id
                ));
            } else {
                throw new \Exception('Id does not exist');
            }
        }
        return $item;
    }

    public function delete($id)
    {
        $this->tableGateway->delete(array(
            'id' => $id
        ));
    }
}