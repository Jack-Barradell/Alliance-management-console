<?php
namespace AMC\Classes;

trait Storable {

    private $_markForDelete = false;

    public function toggleDelete() {
        if($this->_markForDelete) {
            $this->_markForDelete = false;
        }
        else {
            $this->_markForDelete = true;
        }
    }

    public function commit(){
        $id = $this->getID();
        if($this->_markForDelete && $id != null){
            $this->delete();
        }
        elseif (!$this->_markForDelete && $id != null){
            $this->update();
        }
        elseif(!$this->_markForDelete and $id == null){
            $this->create();
            if(Database::getDatabaseType() == Database::TYPE_MYSQLI) {
                $this->setID($this->_connection->insert_id);
            }
            else if(Database::getDatabaseType() == Database::TYPE_SQLITE) {
                $this->setID($this->_connection->lastInsertRowID());
            }
        }
    }

}