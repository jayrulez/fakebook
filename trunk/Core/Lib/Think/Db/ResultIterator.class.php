<?php

class ResultIterator extends Base implements IteratorAggregate
{
    private $sql      =   null;
    private $map    =   null;
    private $db      =   null;
    private $size    =   null;
    private $data   =   null;

    public function __construct($sql='')
    {
        $this->sql  =   $sql;
    }

    public function getIterator()
    {
        $result =   $this->getData();
        return $result;
    }

    public function getData() {
        if(empty($this->data)) {
            $this->db   =   Db::getInstance();
            $this->data =   $this->db->query($this->sql);
            if(is_array($this->data)) {
                $this->size  =   count($this->data);
            }
        }
        return $this->data;
    }

    public function size() {
        if(empty($this->size)) {
            $this->getData();
        }
        return $this->size;
    }

    public function getSql() {
        return $this->sql;
    }

    public function resetData() {
        $this->data = null;
    }
}

?>