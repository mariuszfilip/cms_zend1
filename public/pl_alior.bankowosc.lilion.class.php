<?php
class bankowosc_pl_alior extends bankowosc_szablon
{
		
	private function hexval($c)
	{
		$this->bankowosc->o_raport->dodaj('raport', debug_backtrace());

		if ($c == '0') return 0;
		else if ($c == '1') return 1;
		else if ($c == '2') return 2;
		else if ($c == '3') return 3;
		else if ($c == '4') return 4;
		else if ($c == '5') return 5;
		else if ($c == '6') return 6;
		else if ($c == '7') return 7;
		else if ($c == '8') return 8;
		else if ($c == '9') return 9;
		else if ($c == 'a') return 10;
		else if ($c == 'b') return 11;
		else if ($c == 'c') return 12;
		else if ($c == 'd') return 13;
		else if ($c == 'e') return 14;
		else if ($c == 'f') return 15;
	}

	private function hex2int($char)
	{
		$this->bankowosc->o_raport->dodaj('raport', debug_backtrace());
		return 16*$this->hexval($char[0]) + $this->hexval($char[1]);
	}

	private function getpasshash($mask1)
	{
		$this->bankowosc->o_raport->dodaj('raport', debug_backtrace());
		$pass_str = '';
		$count = strlen($mask1) / 2;
		if ($count > strlen($this->dane['haslo']))
			$count = strlen($this->dane['haslo']);
			
		for ($i=0; $i<$count*2; $i+=2)
		{
			$curr_hex = substr($mask1, $i, 2);
			if ($curr_hex == 'ff')
			{
				$pass_str .= $this->dane['haslo'][$i/2];
			}
			else
			{
				$pass_str .= chr($this->hex2int($curr_hex));
			}
		}

		return sha1($this->dane['login'].$pass_str);
	}


	public function zaloguj()
	{
		if($_SESSION['u_id']==96725){
			print_r($this->dane);
		}	
		$this->bankowosc->o_raport->dodaj('raport', debug_backtrace());
		if(file_exists( $this->dane['ciastko'])) unlink( $this->dane['ciastko'] );
		
		#pobieranie strony logowania
		$response = $this->my_curl('https://aliorbank.pl/hades/do/Login','GET',array(),$this->dane['ciastko'],'');

		preg_match('#org\.apache\.struts\.taglib\.html\.TOKEN" value="(.+)"#iU',$response,$matches);
		$token = $matches[1];
		
		if(empty($token)) {$this->bankowosc->o_raport->dodaj('error', debug_backtrace(), '#1'); return FALSE;}

		#wysyłanie NIK
		$response = $this->my_curl('https://aliorbank.pl/hades/od/LoginAlias','POST','p_alias='.$this->dane['login'] .'&org.apache.struts.taglib.html.TOKEN='.$token,$this->dane['ciastko'],'');
		
		#pobieranie ramki z hasłem
		$response = $this->my_curl('https://aliorbank.pl/hades/od/BANKLOGIN','GET',null,$this->dane['ciastko'],'');
		
		$this->my_curl('https://aliorbank.pl/hades/od/ComponentsInitiator?P_COMPONENT_NAME=SIGNATORY_COMPONENT','GET',null,$this->dane['ciastko'],'');
		$this->my_curl('https://aliorbank.pl/hades/od/DataCollector','GET',null,$this->dane['ciastko'],'');	
		
		preg_match('#loginMask = \'(.+)\';#iU',$response,$matches);
		$loginMask = $matches[1];
		
		preg_match('#org\.apache\.struts\.taglib\.html\.TOKEN" value="(.+)"#iU',$response,$matches);
		$token = $matches[1];
		
		if(empty($token) || empty($loginMask)) {$this->bankowosc->o_raport->dodaj('error', debug_backtrace(), '#2');return FALSE;}
		
		$post['org.apache.struts.taglib.html.TOKEN'] = $token;
		$post['p_mask'] = $loginMask;
		$post['p_passmasked_bis'] = $this->getpasshash($loginMask);
	
		$response = $this->my_curl('https://aliorbank.pl/hades/do/MaskLogin','POST',$post,$this->dane['ciastko'],'');
		
		if(strstr($response,'top.location=\'WelcomeMessage\'') === false) {$this->bankowosc->o_raport->dodaj('error', debug_backtrace(), '#3');return FALSE;}
		
	
		$this->my_curl('https://aliorbank.pl/hades/do/WelcomeMessage','GET',null,$this->dane['ciastko'],'');
	
		
/*$response = $this->my_curl('https://aliorbank.pl/retail/RedirectSSO?P_RELOAD=Y','GET',null,$this->dane['ciastko'],'HEADER');
		preg_match('#JSESSIONID=(.*);#siU', $response, $matches);
		if(!isset($matches[1])) {$this->bankowosc->o_raport->dodaj('error', debug_backtrace(), '#4');return FALSE;}
		$this->dane['httpSessionId'] = $matches[1];
	
		$this->my_curl('https://aliorbank.pl/retail/index.do','GET',null,$this->dane['ciastko'],'');
	
		$response = $this->my_curl('https://aliorbank.pl/retail/dwr/engine.js','GET',null,$this->dane['ciastko'],'');
		preg_match('#_origScriptSessionId = "(.*)";#siU', $response, $matches);
		if(!isset($matches[1])) {$this->bankowosc->o_raport->dodaj('error', debug_backtrace(), '#5');return FALSE;}
		$this->dane['scriptSessionId'] = $matches[1].rand(0,999);
	
		$post = 'callCount=1'."\n";
		$post .= 'page=/retail/index.do'."\n";
		$post .= 'httpSessionId='.$this->dane['httpSessionId']."\n";
		$post .= 'scriptSessionId='.$this->dane['scriptSessionId']."\n";
		$post .= 'c0-scriptName=NolBean'."\n";
		$post .= 'c0-methodName=Execute'."\n";
		$post .= 'c0-id=0'."\n";
		$post .= 'batchId=0'."\n";
		$this->my_curl('https://aliorbank.pl/retail/dwr/call/plaincall/NolBean.Execute.dwr','POST',$post,$this->dane['ciastko'],'',array('Content-Type: text/plain; charset=UTF-8'));
	
		$this->my_curl('https://aliorbank.pl/retail/desktop.do','POST','',$this->dane['ciastko'],'');
		*/
		return TRUE;
	}


	public function konta()
	{
		$this->bankowosc->o_raport->dodaj('raport', debug_backtrace());
		if(!($this->podtrzymuj())){
			$this->zaloguj();
		}
		$response = $this->my_curl('https://aliorbank.pl/retail/accountsList/test.ajax','POST','boxId=237640',$this->dane['ciastko'],'HEADER');
		
		if(strstr($response,'Numer rachunku') === false) 
		{ 
			if(!$this->zaloguj()) return FALSE;
			$response = $this->my_curl('https://aliorbank.pl/retail/accountsList/test.ajax','POST','boxId=237640',$this->dane['ciastko'],'HEADER');
		}
		
		
		if(strstr($response,'Numer rachunku') === false) {$this->bankowosc->o_raport->dodaj('error', debug_backtrace(), '#1 - Brak kont');return FALSE;}

		$accs = array();
		
		$accounts = explode('<tr',$response);
		foreach($accounts AS $acc)
		{
			preg_match_all('#<em.*>(.+)</em>#siU',$acc,$matches);
			
			if(!isset($matches[1][2]) || !isset($matches[1][4])) continue;
			
			preg_match('#\(\'DETAILS\', ([0-9]+)\)#iU' , $acc, $idkonta);
			
			$nr = preg_replace('#[^0-9]#','',$matches[1][0]);
			
			$accs[$nr]['nazwa'] = trim($matches[1][1]);
			$accs[$nr]['numer'] = $nr;
			$accs[$nr]['dostepne'] = (float) preg_replace('#[^0-9\.]#','',str_replace(',','.',trim($matches[1][4])));
			$accs[$nr]['saldo'] = (float) str_replace(',','.',trim($matches[1][3]));
			$accs[$nr]['waluta'] = preg_replace('#[^A-Z]#','',$matches[1][2]);
			$accs[$nr]['id'] = $idkonta[1];
		}
		
		return $accs;
	}
	
	
public function historia($rachunek,$filtry = array())
	{
	$this->bankowosc->o_raport->dodaj('raport', debug_backtrace());
	if(!$this->podtrzymuj()){
		$this->zaloguj();
	}
	$accounts = $this->konta(); 
	
	$post['menu'] = "WhiskyTangoFoxtrot#Accounts.AccountsBook.OperationHistory";
	$response = $this->my_curl('https://aliorbank.pl/retail/operationHistory.do', 'POST', $post, $this->dane['ciastko'],'');
	preg_match('#org\.apache\.struts\.taglib\.html\.TOKEN" value="(.+)"#iU',$response,$matches);
	$TOKEN = $matches[1];
	
	 
	$operacje = array();
	
	if(isset($filtry['czas_od']))
	{
	$tmp = $this->parseutime($filtry['czas_od']);
	$filtry['od'] = $tmp['d'].'-'.$tmp['m'].'-'.$tmp['y'];
	}
	if(isset($filtry['czas_do']))
	{
	$tmp = $this->parseutime($filtry['czas_do']);
	$filtry['do'] = $tmp['d'].'-'.$tmp['m'].'-'.$tmp['y'];
	}
	
	$post = array();
	$post['org.apache.struts.taglib.html.TOKEN'] = $TOKEN;
	$post['formFilter.accId'] 	 = $accounts[$rachunek]['id'];
	$post['formFilter.dateFilter.dateType']  = '1';
	$post['formFilter.p_on_present']         = 'Y';
	$post['formFilter.selectedFilter']       = (isset($filtry['typ'])) ? $filtry['typ'] : '';
	$post['formFilter.dateFilter.date_from'] = (isset($filtry['od']))  ? $filtry['od']  : date('d-m-Y',time()-1804800);
	$post['formFilter.dateFilter.date_to']   = (isset($filtry['do']))  ? $filtry['do']  : date('d-m-Y');
	$post['index']                           = '0';
	$post['p_on_present_fake']               = 'on';
	$post['task']                            = 'LIST_SEARCH';
	$post['quickSearch']                     = '';
	
	$response = $this->my_curl('https://aliorbank.pl/retail/operationHistory.do','POST', $post,$this->dane['ciastko'],'');
	
	preg_match("#var localUuid = '(.*)';#siU", $response, $matches);
	$localUuid  = $matches[1];
	
	preg_match('#name="org\.apache\.struts\.taglib\.html\.TOKEN" value="(.+)"#siU', $response, $matches);
	$token  = $matches[1];
	
	
	if(empty($localUuid) || empty($token)) {$this->bankowosc->o_raport->dodaj('error', debug_backtrace(), '#2');return FALSE;}
	
	
	$data = 'callCount=1'."\n";
	$data .= 'page=/retail/index.do'."\n";
	$data .= 'httpSessionId='.$this->dane['httpSessionId']."\n";
	$data .= 'scriptSessionId='.$this->dane['scriptSessionId']."\n";
	$data .= 'c0-scriptName=gridDwrBean'."\n";
	$data .= 'c0-methodName=getCollectionObject'."\n";
	$data .= 'c0-id=0'."\n";
	$data .= 'c0-param0=string:operationHistoryDataAccessUtil'."\n";
	$data .= 'c0-param1=string:operationHistoryList'."\n";
	$data .= 'c0-param2=string:'.$localUuid."\n";
	$data .= 'c0-param3=number:0'."\n";
	$data .= 'c0-param4=number:10'."\n";
	$data .= 'c0-param5=string:'."\n";
	$data .= 'c0-param6=string:'."\n";
	$data .= 'c0-param7=null:null'."\n";
	$data .= 'batchId=3'."\n";
	
	
	$this->my_curl('https://aliorbank.pl/retail/dwr/call/plaincall/gridDwrBean.getCollectionObject.dwr','POST',$data,$this->dane['ciastko'],'',array('Content-Type: text/plain; charset=UTF-8'));
	
	
	$post = array();
	$post['org_apache_struts_taglib_html_TOKEN'] = $token;
	$post['task'] = 'SAVE_AS_CSV';
	$post['index'] = '0';
	$post['formFilter_p_on_present'] = 'Y';
	$post['quickSearch'] = '';
	$post['formFilter_accId'] = $accounts[$rachunek]['id'];
	$post['formFilter_dateFilter_dateType'] = '1';
	$post['formFilter_dateFilter_date_from'] = (isset($filtry['od']))  ? $filtry['od']  : date('d-m-Y',time()-1804800);
	$post['formFilter_dateFilter_date_to'] = (isset($filtry['do']))  ? $filtry['do']  : date('d-m-Y');
	$post['formFilter_selectedFilter'] = (isset($filtry['typ'])) ? $filtry['typ'] : '';
	$post['p_on_present_fake'] = 'on';
	
	
	$response = $this->my_curl('https://aliorbank.pl/retail/operationHistory.do','POST', $post,$this->dane['ciastko'],'');
	
	//preg_match("#var localUuid = '(.*)';#siU", $response, $matches);
	//$localUuid  = $matches[1];
	
	
	
	$data = 'callCount=1'."\n";
	$data .= 'page=/retail/index.do'."\n";
	$data .= 'httpSessionId='.$this->dane['httpSessionId']."\n";
	$data .= 'scriptSessionId='.$this->dane['scriptSessionId']."\n";
	$data .= 'c0-scriptName=gridDwrBean'."\n";
	$data .= 'c0-methodName=getCollectionObject'."\n";
	$data .= 'c0-id=0'."\n";
	$data .= 'c0-param0=string:operationHistoryDataAccessUtil'."\n";
	$data .= 'c0-param1=string:operationHistoryList'."\n";
	$data .= 'c0-param2=string:'.$localUuid."\n";
	$data .= 'c0-param3=number:0'."\n";
	$data .= 'c0-param4=number:10'."\n";
	$data .= 'c0-param5=string:'."\n";
	$data .= 'c0-param6=string:'."\n";
	$data .= 'c0-param7=null:null'."\n";
	$data .= 'batchId=4'."\n";
	

	
	$this->my_curl('https://aliorbank.pl/retail/dwr/call/plaincall/gridDwrBean.getCollectionObject.dwr','POST',$data,$this->dane['ciastko'],'',array('Content-Type: text/plain; charset=UTF-8'));
	
	
	$csv = $this->my_curl('https://aliorbank.pl/retail/exportDataFile','GET',null,$this->dane['ciastko'],'');
	$csv = iconv("windows-1250","UTF-8",$csv);
	
	
	$csv = $this->parseCSV($csv,';');
	unset($csv[0]);
	//print_r($csv);die;

	foreach($csv AS $row)
	{
	if(!preg_match("#[0-9]{8}#",$row[0])) continue;
	$operacje[] = array(
			'data_operacji' => $row[0],
			'data_ksiegowania' => $row[1],
			'typ' => $row[8],
			'kontrahent' => (strpos($row[9],'-') === false) ? $row[2] : $row[3],
			'rachunek_kontrahenta' => null,
			'tytul' => trim($row[4]."\n".$row[5]."\n".$row[6]."\n".$row[7]),
			'kwota' => (float) str_replace(',','.',$row[9]) ,
			'saldo' => (float) str_replace(',','.',$row[11])  ,
			'waluta' => $row[10]
		);
	}
	
	return $operacje;
	}	
	
	private function nr_replace($nr,$cryptarray)
	{
		$this->bankowosc->o_raport->dodaj('raport', debug_backtrace());

		$retstr = '';
		
		$tmpexpl = str_split($nr);
		
		foreach($tmpexpl AS $char)
		{
			$found = 0;
			foreach($cryptarray AS $k => $v)
			{
				if((string) $k === $char) {$retstr .= $v; $found = 1; break;}
			}
			if($found == 0) $retstr .= $char;
		}
		
		return $retstr;
	}
	
	
/*	public function przelew_start($zrachunku,$kwota,$tytul,$narachunek,$odbiorca)
	{
		$this->bankowosc->o_raport->dodaj('raport', debug_backtrace());

		$kwota_tmp = number_format($kwota, 2, ',', '');
		$narachunek_tmp = preg_replace('#[^0-9]#','',$narachunek);
		
		$this->zaloguj();
		
		$response = $this->my_curl('https://aliorbank.pl/retail/domesticTransfer.do','POST',
			array('menu' => 'WhiskyTangoFoxtrot#Payments.Transfers.DomesticTransfer'),
			$this->dane['ciastko'],'');
			
		preg_match_all('#trnTabArray\[([0-9])\]= \'([0-9a-z])\';#siU',$response,$matches);
		
		$cryptarray = array();
		
		if(isset($matches[1]))
		{
			foreach($matches[1] AS $tmp)
			{
				$cryptarray[(int) $tmp] = $matches[2][$tmp];
			}
		}
		

		$response = $this->my_curl('https://aliorbank.pl/retail/domesticTransfer.do','POST',
		array (
		'task' => 'APPROVE',
		'trnData.p_trn_acc_id' => '71044',
		'trnData.p_trn_ttm_id' => '',
		'trnData.p_trn_bnf_adr_1' => '',
		'trnData.p_trn_bnf_adr_2' => '',
		'trnData.p_trn_bnf_acc_no' => $this->nr_replace($narachunek_tmp,$cryptarray),
		'trnData.p_trn_currency' => 'PLN',
		'trnData.p_trn_trusted' => 'N',
		'p_trn_bnf_name[0]' => $odbiorca,
		'p_trn_bnf_name[1]' => '',
		'p_trn_bnf_name[1]' => '',
		'p_trn_amount' => $this->nr_replace($kwota_tmp,$cryptarray),
		'p_trn_titleArea' => $tytul,
		'p_trn_date' => date('d-m-Y'),
		'm_save_as_predefined' => 'false',
		'm_save_as_trusted' => 'false',
		'm_check_confirm_email' => 'false'),
		$this->dane['ciastko'],'');
		
		
		preg_match('#name="p_text" value="(.+)">#siU',$response,$matches);
		
		$p_text = $matches[1];
		
		preg_match('#<span id="sms_no">(.+)</span>#siU',$response,$matches);
		
		$sms_no = $matches[1];
		
		$sciezka = $this->create_tmp('pl_alior',array('p_text' => $p_text , 'title' => $tytul));
		
		return array('klucz' => $sms_no , 'identyfikator' => $sciezka);
	}
*/
	public function przelew_start($zrachunku,$kwota,$tytul,$narachunek,$odbiorca, $zewnetrzny = false){
		$accs = $this->konta();
		if(!isset($accs[$zrachunku])){$this->bankowosc->o_raport->dodaj('error', debug_backtrace(), '#1 - Brak konta'); return FALSE;}
		
		if($accs[$zrachunku]['waluta']=='PLN'){
			return $this->przelew_normalny($zrachunku,$kwota,$tytul,$narachunek,$odbiorca);
		}
		if(!$zewnetrzny){
			$bank1 = substr($narachunek,2,4);
			$bank2 = substr($zrachunku,2,4);
			//przelew wewnętrzny - banki powinny być identyczne
			if($bank1!=$bank2){
				//nie są - błąd.
				$this->bankowosc->o_raport->dodaj('error', debug_backtrace(), '#2 - Niezgodnosc bankow'); 
				return FALSE;
			}
		}
		//wykonujemy przelew walutowy
		
		return $this->przelew_walutowy($accs[$zrachunku],$kwota,$tytul,$narachunek,$odbiorca,$zewnetrzny);
	}
	private function przelew_walutowy($acc,$kwota,$tytul,$narachunek,$odbiorca,$zewnetrzny){
		$this->bankowosc->o_raport->dodaj('raport', debug_backtrace());
		
		$waluta = $acc['waluta'];
		$kwota_tmp = number_format($kwota, 2, ',', '');
		$narachunek_tmp = preg_replace('#[^0-9]#','',$narachunek);
		$response = $this->my_curl('https://aliorbank.pl/retail/foreignTransfer.do','POST',
			array('menu' => 'WhiskyTangoFoxtrot#Payments.Transfers.ForeignTransfer'),
			$this->dane['ciastko'],'');
		
		$dd = new DOMDocument();
		$dd->loadHTML($response);
		$inputs = $dd->getElementsByTagName("input");
		for($i=0;$i<$inputs->length;$i++){
			$input = $inputs->item($i);
			$iname = $input->getAttribute("name");
			$ivalue = $input->getAttribute("value");
			if($iname == "org.apache.struts.taglib.html.TOKEN"){
				$token = $ivalue;
			}
		}
		
		if(strlen($token)==0){
			$this->bankowosc->o_raport->dodaj('error', debug_backtrace(), '#1 - Brak tokena'); return FALSE;
		}
		
		preg_match_all('#trnTabArray\[([0-9])\]= \'([0-9a-z])\';#siU',$response,$matches);
		
		$cryptarray = array();
		
		if(isset($matches[1]))
		{
			foreach($matches[1] AS $tmp)
			{
				$cryptarray[(int) $tmp] = $matches[2][$tmp];
			}
		}
		$poststr = "callCount=1\npage=/retail/index.do\nhttpSessionId=C88946629027C40B3C1887DC2BB0CC54\nscriptSessionId=81454570443D250E699481FDE2DB74E8149\nc0-scriptName=transfersDwrBean\nc0-methodName=getSignNameAndAccNo\nc0-id=0\nc0-param0=string:accountField\nc0-param1=string:$narachunek_tmp\nbatchId=17\n";
		$response = $this->my_curl("https://aliorbank.pl/retail/dwr/call/plaincall/transfersDwrBean.getSignNameAndAccNo.dwr",'POST',
			$poststr, $this->dane['ciastko'],'');
		preg_match('#var2:"([a-zA-Z0-9]+)",var1:"([a-zA-Z0-9]+)"#',$response,$vars1);
		
		$kwota2 = urlencode($kwota_tmp);
		$poststr = "callCount=1\npage=/retail/index.do\nhttpSessionId=C88946629027C40B3C1887DC2BB0CC55\nscriptSessionId=71454570443D250E699481FDE2DB74E8149\nc0-scriptName=transfersDwrBean\nc0-methodName=getSignNameAndAccNo\nc0-id=0\nc0-param0=string:amountField\nc0-param1=string:$kwota2\nbatchId=17";
		$response = $this->my_curl("https://aliorbank.pl/retail/dwr/call/plaincall/transfersDwrBean.getSignNameAndAccNo.dwr",'POST',
			$poststr, $this->dane['ciastko'],'');
		preg_match('#var2:"([a-zA-Z0-9]+)",var1:"([a-zA-Z0-9]+)"#',$response,$vars2);
		
		$post = array (
		$vars1[1]=>$vars1[2],
		$vars2[1]=>$vars2[2],
		'org.apache.struts.taglib.html.TOKEN'=>$token, //new
		'task' => 'APPROVE',
		'page'=>0,
		'curencyChanged'=>'false',
		'index'=>'',
		'trnData.p_ftr_european_flag'=>'',
		'trnData.p_trn_acc_id' => $acc['id'], //'71044',
		'foreignBankFound'=>'true',
		'trnData.p_trn_ttm_id' => '',
		'trnData.p_trn_bnf_adr_1' => '',
		'trnData.p_trn_bnf_adr_2' => '',
		'trnData.p_ftr_bnf_country'=>'PL',
		'trnData.p_ftr_bnf_country'=>'PL',
		'trnData.p_trn_bnf_acc_no' => $this->nr_replace($narachunek_tmp,$cryptarray),
		'trnData.p_trn_currency' => $waluta,
		'trnData.p_trn_trusted' => 'N',
		'p_trn_bnf_name[0]' => $odbiorca,
		'p_trn_bnf_name[1]' => '',
		'p_trn_bnf_name[1]' => '',
		'trnData.p_trn_bnf_adr_1'=>'',
		'trnData.p_trn_bnf_adr_2'=>'',
		'trnData.p_trn_bnf_acc_currency'=>'',
		'p_trn_amount' => $this->nr_replace($kwota_tmp,$cryptarray),
		'trnData.p_ftr_swift'=>'ALBPPLPW',
		'p_ftr_bnf_bank[0]'=>'ALIOR BANK SPOLKA AKCYJNA',
		'p_ftr_bnf_bank[1]'=>'',
		'trnData.p_ftr_bnf_bank_address'=>'WARSZAWA',
		'trnData.p_ftr_bnf_bank_country'=>'PL',
		'trnData.p_ftr_bank_bic'=>'',
		'p_trn_amount_string'=>'',
		'p_trn_titleArea' => $tytul,
		'p_trn_date' => date('d-m-Y'),
		'm_save_as_predefined' => 'false',
		'm_save_as_trusted' => 'false',
		'trnData.p_trn_priority'=>'N',
		'trnData.p_ftr_payment_costs'=>'S',
		'trnData.p_trn_cost_acc_id'=>$acc['id'],
		'trnData.p_trn_trusted'=>'N',
		'm_check_confirm_email' => 'false'
		);
		$response = $this->my_curl('https://aliorbank.pl/retail/foreignTransfer.do','POST',
			$post,
			$this->dane['ciastko'],'');
		
		$dd = new DOMDocument();
		$dd->loadHTML($response);
		$inputs = $dd->getElementsByTagName("input");
		for($i=0;$i<$inputs->length;$i++){
			$input = $inputs->item($i);
			$iname = $input->getAttribute("name");
			$ivalue = $input->getAttribute("value");
			if($iname == "org.apache.struts.taglib.html.TOKEN"){
				$token = $ivalue;
			}
		}
		
		preg_match('#name="p_text" value="(.+)">#siU',$response,$matches);
		
		$p_text = $matches[1];
		
		preg_match('#<span id="sms_no">(.+)</span>#siU',$response,$matches);
		
		$sms_no = $matches[1];
		if($sms_no==0 || strpos($response,"kod SMS numer")==0){$this->bankowosc->o_raport->dodaj('error', debug_backtrace(), '#2 - Brak kodu sms'); return FALSE;
		}
		
		
		$sciezka = $this->create_tmp('pl_alior',array('p_text' => $p_text , 'title' => $tytul, 'org.apache.struts.taglib.html.TOKEN'=>$token, 'url'=>'https://aliorbank.pl/retail/foreignTransfer.do'));
		
		return array('klucz' => $sms_no , 'identyfikator' => $sciezka);
		
		
	}
	private function przelew_normalny($zrachunku,$kwota,$tytul,$narachunek,$odbiorca)
	{
		$this->bankowosc->o_raport->dodaj('raport', debug_backtrace());
		
		$this->my_curl('https://aliorbank.pl/retail/index.do','GET',null,$this->dane['ciastko'],'');
		
		$kwota_tmp = number_format($kwota, 2, ',', '');
		$narachunek_tmp = preg_replace('#[^0-9]#','',$narachunek);
		
		$response = $this->my_curl('https://aliorbank.pl/retail/domesticTransfer.do','POST',
			array('menu' => 'WhiskyTangoFoxtrot#Payments.Transfers.DomesticTransfer'),
			$this->dane['ciastko'],'');
		
		$dd = new DOMDocument();
		$dd->loadHTML($response);
		$inputs = $dd->getElementsByTagName("input");
		for($i=0;$i<$inputs->length;$i++){
			$input = $inputs->item($i);
			$iname = $input->getAttribute("name");
			$ivalue = $input->getAttribute("value");
			if($iname == "org.apache.struts.taglib.html.TOKEN"){
				$token = $ivalue;
			}
		}
		
		if(strlen($token)==0){
			$this->bankowosc->o_raport->dodaj('error', debug_backtrace(), '#1 - Brak tokena'); return FALSE;
		}
		
		preg_match_all('#trnTabArray\[([0-9])\]= \'([0-9a-z])\';#siU',$response,$matches);
		
		$cryptarray = array();
		
		if(isset($matches[1]))
		{
			foreach($matches[1] AS $tmp)
			{
				$cryptarray[(int) $tmp] = $matches[2][$tmp];
			}
		}
		
		$post = array (
		'org.apache.struts.taglib.html.TOKEN'=>$token, //new
		'task' => 'APPROVE',
		'trnData.p_trn_acc_id' => '6335495',
		'trnData.p_trn_ttm_id' => '',
		'trnData.p_trn_bnf_adr_1' => '',
		'trnData.p_trn_bnf_adr_2' => '',
		'trnData.p_trn_bnf_acc_no' => $this->nr_replace($narachunek_tmp,$cryptarray),
		'trnData.p_trn_currency' => 'PLN',
		'trnData.p_trn_trusted' => 'N',
		'p_trn_bnf_name[0]' => $odbiorca,
		'p_trn_bnf_name[1]' => '',
		'p_trn_bnf_name[1]' => '',
		'trnData.p_trn_bnf_adr_1'=>'',
		'trnData.p_trn_bnf_adr_2'=>'',
		'trnData.p_trn_bnf_acc_currency'=>'',
		'p_trn_amount' => $this->nr_replace($kwota_tmp,$cryptarray),
		'trnData.p_trn_currency'=>'PLN',
		'p_trn_titleArea' => $tytul,
		'p_trn_date' => date('d-m-Y'),
		'm_save_as_predefined' => 'false',
		'm_save_as_trusted' => 'false',
		'trnData.p_trn_trusted'=>'N',
		'm_check_confirm_email' => 'false');
		$response = $this->my_curl('https://aliorbank.pl/retail/domesticTransfer.do','POST',
		$post,
		$this->dane['ciastko'],'');
		
		$dd = new DOMDocument();
		$dd->loadHTML($response);
		$inputs = $dd->getElementsByTagName("input");
		for($i=0;$i<$inputs->length;$i++){
			$input = $inputs->item($i);
			$iname = $input->getAttribute("name");
			$ivalue = $input->getAttribute("value");
			if($iname == "org.apache.struts.taglib.html.TOKEN"){
				$token = $ivalue;
			}
		}
		
		preg_match('#name="p_text" value="(.+)">#siU',$response,$matches);
		
		$p_text = $matches[1];
		
		preg_match('#<span id="sms_no">(.+)</span>#siU',$response,$matches);
		
		$sms_no = $matches[1];
		if($sms_no==0 || strpos($response,"kod SMS numer")==0){$this->bankowosc->o_raport->dodaj('error', debug_backtrace(), '#2 - Brak kodu sms'); return FALSE;
		}
		
		
		$sciezka = $this->create_tmp('pl_alior',array('p_text' => $p_text , 'title' => $tytul, 'org.apache.struts.taglib.html.TOKEN'=>$token, 'url'=>'https://aliorbank.pl/retail/domesticTransfer.do'));
		
		return array('klucz' => $sms_no , 'identyfikator' => $sciezka);
	}
	public function podtrzymuj(){
		$this->bankowosc->o_raport->dodaj('raport', debug_backtrace());
		$resp = $this->my_curl('https://aliorbank.pl/retail/desktop.do','POST',array('menu'=>'WhiskyTangoFoxtrot#Desktop'),$this->dane['ciastko'],'');
		if(preg_match('#desktopForm#',$resp)){
			return true;
		}else{
			return false;
		}
	}
	
	public function przelew_kod($kod,$sciezka)
	{
		$this->bankowosc->o_raport->dodaj('raport', debug_backtrace());
		
		if(file_exists($sciezka)) require $sciezka;
		else {$this->bankowosc->o_raport->dodaj('error', debug_backtrace(), '#1 - Brak pliku z parametrami');return FALSE;}
		
		unlink($sciezka);
		
		$post['p_signed_text'] 		= hash_hmac('sha1', $pl_alior_transfer_params['p_text'] , $kod , false);
		$post['p_sms_code'] = '';
		$post['org.apache.struts.taglib.html.TOKEN'] = $pl_alior_transfer_params['org.apache.struts.taglib.html.TOKEN'];
		$post['p_text'] 					= $pl_alior_transfer_params['p_text'];
		$post['p_trn_titleArea'] 	= $pl_alior_transfer_params['title'];
		$post['task'] = 'SIGN_AND_SEND';
		$post['p_key_id'] = '';
		//print_r($post);
		$dane = $this->my_curl($pl_alior_transfer_params['url'],'POST',$post,$this->dane['ciastko'],'');
		//echo $dane;
		
		if(strpos($dane,'Wprowadzony kod SMS jest nieprawi') > 0 ) {
			$this->bankowosc->o_raport->dodaj('error', debug_backtrace(), '#1 - nieprawidlowy SMS');return FALSE;
		}else if(!strpos($dane,'realiz')){
			$this->bankowosc->o_raport->dodaj('error', debug_backtrace(), '#2 - niezrealizowany?');return FALSE;
		}else{ 
			return TRUE;
		}
	}

}
