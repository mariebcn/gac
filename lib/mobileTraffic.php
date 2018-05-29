<?php

require 'models/mobileTrafficModel.php';

define ('COUNT_VALUES', 8);

/**
 * Class mobileTraffic to manage actions of the test
 * 
 * @author Marie Vial
 */
class mobileTraffic {

    private $arrMsgError = array();

    /**
     * Import file sending by the user
     * 
     * @param file $file
     * 
     * @return boolean
     */
    public function importAction($file) {
        $row = 0;
        $countError = 0;
        $countInsert = 0;
        
        $content = file_get_contents($file);
        
        $rows = str_getcsv($content, "\n");
        
        if (!empty($rows)) {
            $arrayToInsert = array();
            $nbrow = 0;

            $mobileTrafficModel = new mobileTrafficModel();
            //clean table before inserted new file
            $mobileTrafficModel->deleteAll();

            foreach ($rows as $row) {
                $resValidation = '';
                $columns = str_getcsv($row, ';');
                
                //check data row
                $resValidation = $this->validRow($columns);
                
                if (is_array($resValidation)) {
                    $arrayToInsert[] = $resValidation;
                    $countInsert++;
                }
                else {
                    $countError++;
                }
                
                $nbrow++;
            }

            //muliple insert
            $msc=microtime(true);
            $multipleInsert = array_chunk($arrayToInsert, 10000);
            if (is_array($multipleInsert) && !empty($multipleInsert)) {
                foreach($multipleInsert as $dataToInsert) {
                    $mobileTrafficModel->insertMobileTraffic($dataToInsert);
                }
            }

            $mobileTrafficModel->commit();
        }

        echo '<pre>-------------------------- End of import file with ' . $nbrow . ' rows. --------------------------</pre>';
        echo '<pre>-------------------------- Number rows inserted on database : ' . $countInsert . '. --------------------------</pre>';
        echo '<pre>-------------------------- Number rows with wrong data : ' . $countError . '. --------------------------</pre>';

        return true;
    }

    /**
     * call model to execute queries for the test
     * 
     * 
     * @return array
     */
    public function resultAction() {
        $arrResult = array();

        $mobileTrafficModel = new mobileTrafficModel();

        //Total real time of calls
        $msc=microtime(true);
        $arrResult['total_call'] = $mobileTrafficModel->getRealTimeCalls();
        $arrResult['execution_time']['total_call'] = microtime(true)-$msc;

        //Top 10 of best data
        $msc=microtime(true);            
        $arrResult['top_data'] = $mobileTrafficModel->getTopData();
        $arrResult['execution_time']['top_data'] = microtime(true)-$msc;

        //Total of sms
        $msc=microtime(true);
        $arrResult['total_sms'] = $mobileTrafficModel->getTotalSms();
        $arrResult['execution_time']['total_sms'] = microtime(true)-$msc;

        return $arrResult;
    }

    /**
     * function to valid the data of the files
     * 
     * @param array $arrayToCheck row of the file
     * 
     * @return array
     */
    private function validRow($arrayToCheck) {
        if (isset($arrayToCheck) && !empty($arrayToCheck)) {
            //check number values
            if (count($arrayToCheck) == COUNT_VALUES) {
                //check values needed to put on database
                
                //Check subscriber
                if (!intval($arrayToCheck[2])) {
                    $this->arrMsgError[] = array(
                        'data' => $arrayToCheck,
                        'error' => 'Subscriber is not an integer'
                    );
                    return false;
                }

                //Check date
                list($day, $month, $year) = explode('/', $arrayToCheck[3]);
                if(!checkdate($month,$day,$year)) {
                    $this->arrMsgError[] = array(
                        'data' => $arrayToCheck,
                        'error' => 'Date is wrong'
                    );
                    return false;
                }

                //Check hour
                if (!preg_match("/^(?:2[0-3]|[01][0-9]):[0-5][0-9]:[0-5][0-9]$/", $arrayToCheck[4])) {
                    $this->arrMsgError[] = array(
                        'data' => $arrayToCheck,
                        'error' => 'Time is wrong'
                    );
                    return false;
                }

                //Check Consumption
                if (preg_match("/^(?:2[0-3]|[01][0-9]):[0-5][0-9]:[0-5][0-9]$/", $arrayToCheck[5])) {
                    $type = 'call';
                    $parsed = date_parse($arrayToCheck[5]);
                    $consumption = $parsed['hour'] * 3600 + $parsed['minute'] * 60 + $parsed['second'];
                }
                elseif ($arrayToCheck[6] == '0.0' || intval($arrayToCheck[6])) {
                        $type = 'data';
                        $consumption = intval($arrayToCheck[6]);
                }
                elseif ($arrayToCheck[5]=='' || $arrayToCheck[6]=='') {
                    $type = 'sms';
                    $consumption = '';
                }
                else {
                    $this->arrMsgError[] = array(
                        'data' => $arrayToCheck,
                        'error' => 'Consumption values are not good'
                    );
                    return false;
                }

                //return array to insert in database 
                //array(subscriber, datetime, consumption, $type)
                $date = date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $arrayToCheck[3]).' '.$arrayToCheck[4]));
                return array(
                    'subscriber' => $arrayToCheck[2],
                    'date' => $date,
                    'consumption' => $consumption,
                    'type' => $type);
            } 
            else
                return false;
        }
        else {
            return false;
        }
    }
}
