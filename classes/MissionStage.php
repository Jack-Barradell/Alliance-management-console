<?php
namespace AMC\Classes;

use AMC\Exceptions\BlankObjectException;
use AMC\Exceptions\InvalidMissionException;
use AMC\Exceptions\QueryStatementException;

class MissionStage implements DataObject {
    use Getable;
    use Storable;

    private $_id = null;
    private $_missionID = null;
    private $_name = null;
    private $_body = null;
    private $_status = null;
    private $_connection = null;

    public function __construct($id = null, $missionID = null, $name = null, $body = null, $status = null) {
        $this->_id = $id;
        $this->_missionID = $missionID;
        $this->_name = $name;
        $this->_body = $body;
        $this->_status = $status;
        $this->_connection = Database::getConnection();
    }

    public function create() {
        if($this->eql(new MissionStage())) {
            throw new BlankObjectException('Cannot store a blank Mission Stage.');
        }
        else {
            if($stmt = $this->_connection->prepare("INSERT INTO `Mission_Stages` (`MissionID`,`MissionStageName`,`MissionStageBody`,`MissionStageStatus`) VALUES (?,?,?,?)")) {
                $stmt->bind_param('isss', $this->_missionID, $this->_name, $this->_body, $this->_status);
                $stmt->execute();
                $stmt->close();
            }
            else {
                throw new QueryStatementException('Failed to bind query.');
            }
        }
    }

    public function update() {
        if($this->eql(new MissionStage())) {
            throw new BlankObjectException('Cannot store a blank Mission Stage.');
        }
        else {
            if($stmt = $this->_connection->prepare("UPDATE `Mission_Stages` SET `MissionID`=?,`MissionStageName`=?,`MissionStageBody`=?,`MissionStageStatus`=? WHERE `MissionStageID`=?")) {
                $stmt->bind_param('isssi', $this->_missionID, $this->_name, $this->_body, $this->_status, $this->_id);
                $stmt->execute();
                $stmt->close();
            }
            else {
                throw new QueryStatementException('Failed to bind query.');
            }
        }
    }

    public function delete() {
        if($stmt = $this->_connection->prepare("DELETE FROM `Mission_Stages` WHERE `MissionStageID`=?")) {
            $stmt->bind_param('i', $this->_id);
            $stmt->execute();
            $stmt->close();
            $this->_id = null;
        }
        else {
            throw new QueryStatementException('Failed to bind query.');
        }
    }

    public function eql($anotherObject) {
        if(\get_class($this) == \get_class($anotherObject)) {
            if($this->_id == $anotherObject->getID() && $this->_missionID == $anotherObject->getMissionID() && $this->_name == $anotherObject->getName() && $this->_body == $anotherObject->getBody() && $this->_status == $anotherObject->getStatus()) {
                return true;
            }
            else {
                return false;
            }
        }
        else {
            return false;
        }
    }

    // Getters and setters

    public function getID() {
        return $this->_id;
    }

    public function getMissionID() {
        return $this->_missionID;
    }

    public function getName() {
        return $this->_name;
    }

    public function getBody() {
        return $this->_body;
    }

    public function getStatus() {
        return $this->_status;
    }

    public function setID($id) {
        $this->_id = $id;
    }

    public function setMissionID($missionID, $verify = false) {
        if($verify) {
            if(Mission::missionExists($missionID)) {
                $this->_missionID = $missionID;
            }
            else {
                throw new InvalidMissionException('There is no mission with id ' . $missionID);
            }
        }
        else {
            $this->_missionID = $missionID;
        }
    }

    public function setName($name) {
        $this->_name = $name;
    }

    public function setBody($body) {
        $this->_body = $body;
    }

    public function setStatus($status) {
        $this->_status = $status;
    }

    // Statics

    public static function select($id) {
        if(\is_array($id) && \count($id) > 0) {
            $missionStageResult = [];
            $typeArray = [];
            $refs = [];
            $typeArray[0] = 'i';
            $questionString = ',?';
            foreach($id as $key => $value) {
                $refs[$key] =& $id[$key];
            }
            for($i = 0; $i < \count($id) - 1; $i++) {
                $typeArray[0] .= 'i';
                $questionString = ',?';
            }
            $param = \array_merge($typeArray, $refs);
            if($stmt = Database::getConnection()->prepare("SELECT `MissionStageID`,`MissionID`,`MissionStageName`,`MissionStageBody`,`MissionStageStatus` FROM `Mission_Stages` WHERE `MissionStageID` IN (" . $questionString .")")) {
                \call_user_func_array(array($stmt, 'bind_param'), $param);
                $stmt->execute();
                $stmt->bind_result($missionStageID, $missionID, $name, $body, $status);
                while($stmt->fetch()) {
                    $missionStage = new MissionStage();
                    $missionStage->setID($missionStageID);
                    $missionStage->setMissionID($missionID);
                    $missionStage->setName($name);
                    $missionStage->setBody($body);
                    $missionStage->setStatus($status);
                    $missionStageResult[] = $missionStage;
                }
                $stmt->close();
                if(\count($missionStageResult) > 0) {
                    return $missionStageResult;
                }
                else {
                    return null;
                }
            }
            else {
                throw new QueryStatementException('Failed to bind query.');
            }
        }
        else if(\is_array($id) && \count($id) == 0) {
            $missionStageResult = [];
            if($stmt = Database::getConnection()->prepare("SELECT `MissionStageID`,`MissionID`,`MissionStageName`,`MissionStageBody`,`MissionStageStatus` FROM `Mission_Stages`")) {
                $stmt->execute();
                $stmt->bind_result($missionStageID, $missionID, $name, $body, $status);
                while($stmt->fetch()) {
                    $missionStage = new MissionStage();
                    $missionStage->setID($missionStageID);
                    $missionStage->setMissionID($missionID);
                    $missionStage->setName($name);
                    $missionStage->setBody($body);
                    $missionStage->setStatus($status);
                    $missionStageResult[] = $missionStage;
                }
                $stmt->close();
                if(\count($missionStageResult) > 0) {
                    return $missionStageResult;
                }
                else {
                    return null;
                }
            }
            else {
                throw new QueryStatementException('Failed to bind query.');
            }
        }
        else {
            return null;
        }
    }

    public static function getByMissionID($missionID) {
        if($stmt = Database::getConnection()->prepare("SELECT `MissionStageID` FROM `Mission_Stages` WHERE `MissionID`=?")) {
            $stmt->bind_param('i', $missionID);
            $stmt->execute();
            $stmt->bind_result($missionStageID);
            $input = [];
            while($stmt->fetch()) {
                $input[] = $missionStageID;
            }
            if(\count($input) > 0) {
                return MissionStage::get($input);
            }
            else {
                return null;
            }
        }
        else {
            throw new QueryStatementException('Failed to bind query.');
        }
    }

}