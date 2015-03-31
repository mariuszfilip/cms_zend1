<?php
require_once 'Smpptransceiver.php';



class SMSTransportDaemon{

    private $campaign_id = 0;
    private $campaignsend_id = 0;
    protected $emails=array();
    protected $ids = array();

    public function __construct($id_campaign,$id_campaign_send){
        $this->campaignsend_id=$id_campaign_send;
        $this->id_campaign=$id_campaign;
        $campaignsendtemp = new Application_Model_DbTable_Campaignsendsmstemp();

        $this->emails = $campaignsendtemp->getCampaignsendtemplimit($this->campaignsend_id);
        if(count($this->emails)) {
            foreach($this->emails as $key => $value) {
                $this->ids[] = $value["subscriber_id"];
            }
            $campaignsendtemp->changeStatus($this->ids, 1, $this->campaignsend_id);
        }
    }

    public function sendCampaign(){
        try {
            echo "<pre>";
            if(count($this->emails)){
               	$campaign = new Application_Model_DbTable_Campaign();
               	$sms_send_subscriber = new Application_Model_DbTable_Campaignsendsmssubscriber();
               	$data = $campaign->getCampaignSmsToSend($this->id_campaign);
                // to mozna przeniesc do config.ini i podlaczyc Zend_Config
                $config = array(
            	'host' => 'smpp.sitmobile.com',
            	'port' => 9000,
            	'system_id' => 'mailbox',
            	'client_id' => '30084',
            	'password' => 'W1ls0n9',
            	'account_id' => '59546',
            	'default_sender' => 'mailbox',
            	'delay_between_sms' => 100,		// microseconds
            	'delay_after_sms' => 100,		// microseconds
            	'answer_timeout' => 3,		// czas w sekundach na oczekiwanie na raporty po wyslaniu sms-ow
                );

                // tu bedzie trzeba podlaczyc mechanizm pollingu bazy danych i pobierania wiadomosci do wysylki
                // recipient_number: numer telefonu odbiorcy, w formacie miedzynarodowym bez plusa
                $defaultData = array(
                array(
        		'message_class' => 1	// 0 - flash(tylko na ekran telefonu), 1- normalna 2 - unicode flash 3 - binarna
                ));

                $smsData = $this->emails;

                $log = array();
                $receive_log = array();


                // receive bedzie wywolywane regularnie w trakcie dzialania skryptu
                //declare(ticks = 5);


                // wywolanie transceivera smpp i ustanowienie polaczenia
                $tx = new SMPP($config['host'], $config['port']);
                $tx->system_type = $config['client_id'] . '|' . $config['account_id'];
                $tx->addr_ton = 1;
                $tx->addr_npi = 1;
                $tx->sms_source_addr_ton = 5;
                $tx->sms_source_addr_npi = 0;	// te parametry odpowiadaja nadawcy alfanumerycznemu, max 11 znakow, dla nadawcy numerycznego np. 48666222666 ton=1, npi=1
                $tx->sms_dest_addr_ton = 1;
                $tx->sms_dest_addr_npi = 1;
                $tx->data_coding = 0;	// kodowanie GSM0338, dla ISO-8859-1 $data_coding=3, dla UCS-2 $data_coding=8
                $tx->start_enquire = 1;
                $tx->debug = false;	// mozna wylaczyc jesli wszystko bedzie OK




                // jesli chcemy odbierac wiadomosci nalezy wlaczyc ponizsza funkcje
                 

                // proba polaczenia jako transceiver (nadajnik/odbiornik)

                $bound = $tx->bindTransceiver($config['system_id'], $config['password']);
                if(!$bound) die ("Problem z polaczeniem!");



                //receive($tx);
                // funkcja do odbierania wiadomosci
                //register_tick_function('receive', $tx);


                if($tx->start_enquire) {
                    $tx->sendPDU(0x00000015, "", $tx->sequence_number++);
                    $proceed = true;
                }

                do {
                    // wysylanie smsow z kolejki
                    if(count($smsData) == 0) {
                        $proceed = false;
                        break;
                    }
                    foreach($smsData as $message) {

                        // sprawdzenie sms-a
                        if(strlen($data['content']) == 0) {
                            $log[] = date("Y-m-d H:i:s") . 'Pusta wiadomosc o id '.$message['id'];
                            continue;
                        }

                        if(strlen($message['phone']) == 0) {
                            $log[] = date("Y-m-d H:i:s") . 'Brak numeru odbiorcy w wiadomosci o id '.$message['id'];
                            continue;
                        }
                        switch($defaultData['message_class']) {
                            case 0: $tx->data_coding = 240; break;
                            case 1: break;
                            case 2: $tx->data_coding = 88;
                            case 3: $tx->data_coding = 4.245;
                        }

                        if(strlen($message['sender']) == 0) {
                            $message['sender'] = $config['default_sender'];
                        }

                        $sent = $tx->sendSMS($message['sender'], $message['phone'], $data['content']);

                        if($sent){
                            $send = 1;
                            $log[]  = 'Wysylka do '.$message['phone'].': OK';
                        }
                        if(!$sent){
                            $send = 0;
                            $log[] = 'Wysylka do '.$message['phone'].': Failed';
                        }
                        $sms_send_subscriber->insertCampaignsendsubscriber(array('campaignsend_id'=>$this->campaignsend_id,'send'=>$send ,'subscriber_id'=>$message['id'],'phone'=>$message['phone']));
                        usleep($config['delay_between_sms']);
                        $this->receive($tx);
                    }

                    usleep($config['delay_after_sms']);
                    $start_time = time();
                } while(false);

                // czekamy na odpowiedzi
                while ($proceed) {

                    if(time() >= $start_time + $config['answer_timeout']) {
                        $proceed = false;
                        $this->receive($tx);
                    }
                }
                $campaignsendtemp = new Application_Model_DbTable_Campaignsendsmstemp();
                $campaignsendtemp->deleteStatus($this->campaignsend_id,$this->ids);
                // konczymy polaczenie

                $tx->close();
                unset($tx);
                 
            }
            $temp = new Application_Model_DbTable_Campaignsendsmstemp();
            $temp_result = $temp->checkTempIsClear($this->campaignsend_id);
            if(!$temp_result){
                $send =new Application_Model_DbTable_CampaignsmsSend();
                $send->updateCampaignsend($this->campaignsend_id,array('status'=>1));
                
            }
            echo "</pre>";
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    function receive($tx) {
        $return = array();
        if(!$tx || $tx->state != 'bind_tcx') return false;
        $sms = $tx->readSMS();
        if($sms) {
            // check where the message is stored in short_messsage or message_payload
            if ( !isset($sms['short_message']) || ($sms['sm_length'] != strlen($sms['short_message'])) || ($sms['sm_length'] == 0)) {
                if (isset($sms['sar_total_segments']) && isset($sms['sar_segment_seqnum'])) {
                    if (($sms['sar_total_segments'] - $sms['sar_segment_seqnum']) == 0) { //if it's the last part of payload, accumlate
                        $text = $sms['user_defined_payload'];
                        unset($sms['user_defined_payload']);
                    } else {
                        continue;
                    }
                } else {
                    $text = $sms['message_payload'];
                }
                 
            } else {
                $text = $sms['short_message'];
            }
            $campaignsendsms = new Application_Model_DbTable_Campaignsendsmssubscriber();
            $find_result = $campaignsendsms->getSubscriberByPhone($this->campaignsend_id,$sms['source_addr']);
            if($find_result){
                if($text != ''){
                    $info = explode(' ',$text);
                    foreach($info as $key => $value){
                        $error= explode(':',$value);
                        var_dump($error[1]);
                        if(isset($error[0]) && $error[0] == 'stat'){
                            $date_result = array();
                            $data_result['response']=1;
                            if($error[1] == 'DELIVRD'){
                                $status_response = 1;
                            }elseif($error[1] == 'EXPIRED'){
                                $status_response = 2;
                            }elseif($error[1] == 'DELETED'){
                                $status_response = 3;
                            }elseif($error[1] == 'UNDELIV'){
                                $status_response = 4;
                            }elseif($error[1] == 'ACCEPTD'){
                                $status_response = 5;
                            }elseif($error[1] == 'UNKNOWN'){
                                $status_response = 6;
                            }elseif($error[1] == 'REJECTD'){
                                $status_response = 7;
                            }
                            $data_result['response_status']=$status_response;
                            $campaignsendsms->updateCampaignsendsubscriberid($find_result['id'],$data_result);
                        }
                        if(isset($error[0]) && $error[0] == 'err'){
                            $date_result = array();
                            $data_result['response_status_network']=(int)$error[1];
                            $campaignsendsms->updateCampaignsendsubscriberid($find_result['id'],$data_result);
                        }
                    }
                }
            }
        }

    }
}
?>