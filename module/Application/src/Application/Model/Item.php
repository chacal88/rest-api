<?php
namespace Application\Model;

class Item
{
    public $id;

    public $description;

    public $done;

    public function exchangeArray($data)
    {
        $this->id = (! empty($data['id'])) ? $data['id'] : null;
        $this->description = (! empty($data['description'])) ? $data['description'] : null;
        $this->done = (! empty($data['done'])) ? $data['done'] : null;
    }

    public function toArray()
    {
        $data = array(
            'id' => $this->id,
            'description' => $this->description,
            'done' => $this->done
        );
        
        return $data;
    }
}