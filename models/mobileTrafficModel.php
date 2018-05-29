<?php
/**
 * Class mobileTrafficModel to manage database actions
 * 
 * @author Marie Vial
 */
class mobileTrafficModel {
    /**
     * @var null Database Connection
     */
    public $db = null;

    /**
     * construct
     */
    function __construct() {
        $this->openDatabaseConnection();
    }
    
    /**
     * Open the database connection
     */
    private function openDatabaseConnection() {
        $this->db = new mysqli("127.0.0.1", "root", "root", "mysql", 8989);
        $this->db->autocommit(FALSE);
        if ($this->db->connect_error) die("Impossible de se connecter : " . $this->db->connect_error);
    }

    public function commit() {
        $this->db->commit();
    }

    /**
     * Load the model
     * 
     * @param string $model_name The name of the model
     * 
     * @return object model
     */
    public function loadModel($model_name) {
        require 'models/' . strtolower($model_name) . '.php';
        // return new model (and pass the database connection to the model)
        return new $model_name($this->db);
    }

    /**
     * insert mobile traffic data
     * 
     * @param array $arrayValues
     * 
     * @return boolean
     */
    public function insertMobileTraffic($arrayValues) {
        foreach ($arrayValues AS $key => $value) {
            $data[] = "(".$value['subscriber'].", '".$value['date']."', ".($value['consumption'] != '' ? $value['consumption'] : 'NULL' ).", '".$value['type']."')";
        }
        $sql = "INSERT INTO gac.mobile_traffic (subscriber, date, consumption, type) VALUES ".implode(', ', $data);
        
        if (!($this->db->query($sql))){
            echo '<pre>Error on insert data : '.$this->db->error.'</pre>';
            echo '<pre>query = '.$sql.'</pre>';

            return false;
        }

        return true;
    }

    /**
     * insert mobile traffic data
     * 
     * @return boolean
     */
    public function deleteAll() {
        $sql = "DELETE FROM gac.mobile_traffic";
        
        if (!($this->db->query($sql))){
            echo '<pre>Error on delete data : '.$this->db->error.'</pre>';
            echo '<pre>query = '.$sql.'</pre>';
            return false;
        }
        return true;
    }

    /**
     * get total time of calls
     * 
     * @return array
     */
    public function getRealTimeCalls() {
        $sql    = "SELECT sum(consumption) as total_time FROM gac.mobile_traffic WHERE type = 'call' AND date >= '2012-02-15 00:00:00'";
        if (!($result = $this->db->query($sql))){
            echo '<pre>Error on select data : '.$this->db->error.'</pre>';
            echo '<pre>query = '.$sql.'</pre>';
            return false;
        }
        else {
            while ($row = $result->fetch_object()){
                $arrResult = ($row->total_time!='' ? $row->total_time : 0);
            }
            // Free result set
            $result->close();
            return $arrResult;
        }
    }


    /**
     * get top 10 of data
     * 
     * @return array
     */
    public function getTopData() {
        $sql    = "SELECT subscriber, date, consumption FROM gac.mobile_traffic 
                    WHERE type = 'data' 
                        AND (HOUR(date) < 8 
                        OR HOUR(date) > 18)
                    order by consumption desc
                    limit 10";
        
        if (!($result = $this->db->query($sql))){
            echo '<pre>Error on select data : '.$this->db->error.'</pre>';
            echo '<pre>query = '.$sql.'</pre>';
            return false;
        }
        else {
            $arrResult = array();
            while ($row = $result->fetch_object()){
                $arrResult[] = array(
                    'subscriber' => $row->subscriber,
                    'date' => $row->date,
                    'consumption' => $row->consumption);
            }
            // Free result set
            $result->close();
            return $arrResult;
        }
    }


    /**
     * get total of sms
     * 
     * @return array
     */
    public function getTotalSms() {
        $sql    = "SELECT count(*) as total_sms 
                    FROM gac.mobile_traffic 
                    WHERE type = 'sms'";
        
        if (!($result = $this->db->query($sql))){
            echo '<pre>Error on select data : '.$this->db->error.'</pre>';
            echo '<pre>query = '.$sql.'</pre>';
            return false;
        }
        else {
            while ($row = $result->fetch_object()){
                $arrResult = $row->total_sms;
            }
            // Free result set
            $result->close();
            return $arrResult;
        }
    }
}