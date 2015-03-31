<?php
class bankowosc_pl_inteligo extends bankowosc_szablon
{
		
	function odwroc_date($in)
	{
		$this->bankowosc->o_raport->dodaj('raport', debug_backtrace());

		$tmp = explode('-',$in);
		return implode('-',array_reverse($tmp));
	}
	
	private function parse_form($html)
	{
		$this->bankowosc->o_raport->dodaj('raport', debug_backtrace());

		preg_match('#<input type="hidden" name="ias_sid" value="(?P<ias_sid>.+)">.*class="ias_navi_map" value="(?P<ias_navi_map>.+)" name="ias_navi_map"><input type="hidden" class="ias_post_into" value="(?P<ias_post_into>.+)" name="ias_post_into"><input type="hidden" name="back_reload_protection" value="(?P<back_reload_protection>.+)"><input type="hidden" name="ias.form_protect" value="(?P<ias_form_protect>[0-9a-zA-Z-]+)">#siU',$html,$matches);
		
		/* file_put_contents('inteligo_cache.php','<?php $inteligo_cache = ' . var_export($matches,1) .';?>'); */
		
		if(isset($matches['ias_sid'])) return $matches;
		else return FALSE;
	}
	

	private function zaloguj($gethtml=0)
	{
		$this->bankowosc->o_raport->dodaj('raport');
        echo 'logowanie';
        //if(file_exists( $this->dane['ciastko'])) unlink( $this->dane['ciastko'] );

		# pobieranie strony logowania
        //$this->o_curl->ciastko = $this->dane['ciastko'];
		$dane = $this->o_curl->wykonaj('https://inteligo.pl/secure');
		$form_fields = $this->parse_form($dane);
		if($form_fields === false) {$this->error = 'INTELIGO LOGOWANIE BRAK PARAMETROW #1';return FALSE;}



        $post = array(
            '__ie__bug__workaround__' => '',
            'back_reload_protection' => $form_fields['back_reload_protection'],
            'dynsub_ex_69f59d6ca8546e21edda0faabcc7a2cba7977a17' => 'Zaloguj',
            'ias.form_protect' => $form_fields['ias_form_protect'],
            'ias_navi_map' => $form_fields['ias_navi_map'],
            'ias_post_into' => $form_fields['ias_post_into'],
            'ias_sid' => $form_fields['ias_sid'],
            'cif' => $this->dane['login'],
            'menu' => '',
            'password' => $this->dane['haslo'],
            'token_password' => ''
        );


		#logowanie
        $this->o_curl->post = $post;
        $this->o_curl->ciastko = $this->dane['ciastko'];
		$dane = $this->o_curl->wykonaj('https://inteligo.pl/secure/igo2');
		if(strstr($dane,'Rachunki') === false) return  false;
		else true;
	}

    public function podtrzymuj(){
        echo 'podtrzynuj';
        $this->bankowosc->o_raport->dodaj('raport');
        $this->o_curl->ciastko = $this->dane['ciastko'];
        //$this->o_curl->post = array('menu'=> '','dynsub_ex_2abce40c89e8d1d826bc23abb8dc3494453f3761_2'=>'');

        $resp = $this->o_curl->wykonaj('https://inteligo.pl/secure/igo2');
        echo $resp;
        if(strstr($resp,'Rachunki') === false){
            return false;
        }else{
            return true;
        }
    }


	public function konta()
	{
        $rachunki = array();
		$this->bankowosc->o_raport->dodaj('raport', debug_backtrace());
        if(!($this->podtrzymuj())){
            $this->zaloguj();
        }

        ///$this->o_curl->ciastko = $this->dane['ciastko'];
        $dane = $this->o_curl->wykonaj('https://inteligo.pl/secure/igo2');
		
		require_once('funkcje/simple_html_dom.php');


		$html = str_get_html($dane);
		$konta = $html->find('table[class=table-list] tr[class!=table-header]');
		foreach($konta as $k){
			$rach = preg_replace('#[^0-9]#','',$k->find('td',0)->plaintext);
			if(!preg_match('#[0-9]{26}#',$rach)) continue;
		
			$rachunki[$rach]['typ'] = $k->find('td',0)->plaintext;
			$rachunki[$rach]['nazwa'] = $k->find('td',0)->plaintext;
			$rachunki[$rach]['numer'] = $rach;
			$rachunki[$rach]['dostepne'] = $this->cleanamount($k->find('td',1)->plaintext);
			$rachunki[$rach]['saldo'] =$this->cleanamount($k->find('td',2)->plaintext);
			$rachunki[$rach]['waluta'] = 'PLN';
		}
		return $rachunki;
	}
	
	
	public function historia($rachunek,$filtry = array())
	{
		$this->bankowosc->o_raport->dodaj('raport', debug_backtrace());

		$rachunek = preg_replace('#[^0-9]#','',$rachunek);
		if(strlen($rachunek) != 26) return 'ZA KROTKI NUMER KONTA';


        if(!($this->podtrzymuj())){
            $this->zaloguj();
        }

		$form_fields = $this->parse_form($this->konta());
		if($form_fields === false) {$this->error = 'INTELIGO HISTORIA BRAK PARAMETROW #2';return FALSE;}
		
	
		# pobieranie formularza historii
		$dane = $this->my_curl('https://inteligo.pl/secure/igo2','POST',array(
		'__ie__bug__workaround__' => '',
		'back_reload_protection' => $form_fields['back_reload_protection'],
		'dynsub_ex_2abce40c89e8d1d826bc23abb8dc3494453f3761_2' => '',
		'ias.form_protect' => $form_fields['ias_form_protect'],
		'ias_navi_map' => $form_fields['ias_navi_map'],
		'ias_post_into' => $form_fields['ias_post_into'],
		'ias_sid' => $form_fields['ias_sid'],
		'menu' => ''
		),NULL,'');
		
		
		$form_fields = $this->parse_form($dane);
		if($form_fields === false) {$this->error = 'INTELIGO HISTORIA BRAK PARAMETROW #3';return FALSE;}
		if(isset($filtry['czas_od']))
		{
			$tmp = $this->parseutime($filtry['czas_od']);
			$filtry['od'] = $tmp['y'].'-'.$tmp['m'].'-'.$tmp['d'];
		}
		if(isset($filtry['czas_do']))
		{
			$tmp = $this->parseutime($filtry['czas_do']);
			$filtry['do'] = $tmp['y'].'-'.$tmp['m'].'-'.$tmp['d'];
		}
		
		$post['ias_sid'] = $form_fields['ias_sid'];
		$post['__ie__bug__workaround__'] = '';
		$post['ias_navi_map'] = $form_fields['ias_navi_map'];
		$post['ias_post_into'] = $form_fields['ias_post_into'];
		$post['back_reload_protection'] = $form_fields['back_reload_protection'];
		$post['ias.form_protect'] = $form_fields['ias_form_protect'];
		$post['menu'] = '';
		$post['fields_search-selected_acc'] = $rachunek;
		$post['fields_search-date_range-since'] = (isset($filtry['od']))  ? $filtry['od']  : date('Y-m-d',time()-604800);
		$post['fields_search-date_range-to'] =    (isset($filtry['do']))  ? $filtry['do']  : date('Y-m-d');
		$post['fields_search-show_advanced'] = '1';
		$post['fields_advanced-search_text'] = (isset($filtry['szukaj']))    ? $filtry['szukaj']     : '';
		$post['fields_advanced-search_type'] = (isset($filtry['jak']))       ? $filtry['jak']        : 'all_words';
		$post['fields_advanced-filter_tx_type'] = (isset($filtry['typ'])) ? $filtry['typ']       : 'ALL';
		$post['fields_advanced-amount_range-min'] = (isset($filtry['min']))        ? $filtry['min']        : '';
		$post['fields_advanced-amount_range-max'] = (isset($filtry['max']))        ? $filtry['max']        : '';
		$post['fields_advanced-other_account'] = (isset($filtry['konto']))        ? $filtry['konto']        : '';
		$post['dynsub_ex_b390f775eeab31a9b23eb69013270c1751fdd32b'] = 'Pokaz';
		
		
		# wysyłanie formularza historii
		//$dane = $this->my_curl('https://inteligo.pl/secure/igo2','POST',$post,NULL,'');
        //echo $dane;
	/*	$form_fields = $this->parse_form($dane);
		if($form_fields === false) {$this->error = 'INTELIGO HISTORIA BRAK PARAMETROW #3';return FALSE;}*/
		
		$post = array();
		$post['ias_sid'] = $form_fields['ias_sid'];
		$post['__ie__bug__workaround__'] = '';
		$post['ias_navi_map'] = $form_fields['ias_navi_map'];
		$post['ias_post_into'] = $form_fields['ias_post_into'];
		$post['back_reload_protection'] = $form_fields['back_reload_protection'];
		$post['ias.form_protect'] = $form_fields['ias_form_protect'];
		$post['menu'] = '';
		$post['fields_search-selected_acc'] = $rachunek;
		$post['fields_search-date_range-since'] = (isset($filtry['od']))  ? $filtry['od']  : date('Y-m-d',time()-604800);
		$post['fields_search-date_range-to'] =    (isset($filtry['do']))  ? $filtry['do']  : date('Y-m-d');
		$post['fields_search-show_advanced'] = '1';
		$post['fields_advanced-search_text'] = (isset($filtry['szukaj']))    ? $filtry['szukaj']     : '';
		$post['fields_advanced-search_type'] = (isset($filtry['jak']))       ? $filtry['jak']        : 'all_words';
		$post['fields_advanced-filter_tx_type'] = (isset($filtry['typ'])) ? $filtry['typ']       : 'ALL';
		$post['fields_advanced-amount_range-min'] = (isset($filtry['min']))        ? $filtry['min']        : '';
		$post['fields_advanced-amount_range-max'] = (isset($filtry['max']))        ? $filtry['max']        : '';
		$post['fields_advanced-other_account'] = (isset($filtry['konto']))        ? $filtry['konto']        : '';
		$post['format'] = 'xml';
		$post['dynsub_ex_6a174157eec0e7b59a2180ba0936938d4668b42d'] = 'Pobierz';
		
		
		# pobieranie XML
		$dane = $this->my_curl('https://inteligo.pl/secure/igo2','POST',$post,NULL,'');
		$dane = explode("<?xml",$dane,2);
		$dane = "<?xml".$dane[1];
		
		#parsowanie XML
		$history = array();
		
		$xml = @simplexml_load_string($dane);
		if($xml === false) {$this->error = 'INTELIGO HISTORIA LOAD XML FAILED #4';return FALSE;}

		foreach($xml->operations->operation AS $o) 
		{
			$side = '';
			if(isset($o->{'other_side'})) 
			{
				foreach($o->{'other_side'}->owner->line AS $line) $side .= (string) $line."\n";
			}
			
			preg_match('#Tytu..: (.+)#',$o->description,$matches);
			if(isset($matches[1]))
			{
				if(preg_match('#(.*) Data#',$matches[1],$m)){
					$matches[1] = $m[1];
				}
				$o->description = $matches[1];
			}
			
			$history[] = array(
				'id' => (string) $o['id'],
				'data_operacji' => (int) $this->utime( (string) $o->{'order-date'} ),
				'data_ksiegowania' => (int) $this->utime( (string) $o->{'exec-date'} ),
				'typ' => trim((string) $o->type),
				'kontrahent' => trim($side),
				'rachunek_kontrahenta' =>  isset($o->{'other_side'}) ? trim((string) $o->{'other_side'}->account) : '',
				'tytul' => trim((string) $o->description),
				'kwota' => (float) preg_replace('#[^0-9\.-]#','',str_replace('+','',(string) $o->amount)) ,
				'saldo' => (float) preg_replace('#[^0-9\.-]#','',str_replace('+','',(string) $o->{'ending-balance'})) ,
				'waluta' => 'PLN'
				);
		}
		
		$history = array_reverse($history);
		
		return $history;
	}
	
	
	public function przelew_start($zrachunku,$kwota,$tytul,$narachunek,$odbiorca)
	{ 
		$this->bankowosc->o_raport->dodaj('raport', debug_backtrace());

		$zrachunku = preg_replace('#[^0-9]#','',$zrachunku);
		if(strlen($zrachunku) != 26) return 'ZA KROTKI NUMER KONTA';
		
		$kwota = number_format(str_replace(',','.',$kwota), 2, ',', '');
	
	
		$loginresult = $this->zaloguj(1);
			
		if(!$loginresult['status']) {$this->error = 'INTELIGO PRZELEW LOGIN FAILED #1';return FALSE;}
		
		
		$form_fields = $this->parse_form($loginresult['rachunki']);
		if($form_fields === false) {$this->error = 'INTELIGO PRZELEW BRAK PARAMETROW #2';return FALSE;}
		
	
		# przelewy
		$dane = $this->my_curl('https://inteligo.pl/secure/igo2','POST',array(
		'__ie__bug__workaround__' => '',
		'back_reload_protection' => $form_fields['back_reload_protection'],
		'dynsub_ex_2abce40c89e8d1d826bc23abb8dc3494453f3761_1' => 'Przelewy',
		'ias.form_protect' => $form_fields['ias_form_protect'],
		'ias_navi_map' => $form_fields['ias_navi_map'],
		'ias_post_into' => $form_fields['ias_post_into'],
		'ias_sid' => $form_fields['ias_sid'],
		'menu' => ''
		),NULL,'');
		

		$form_fields = $this->parse_form($dane);
		if($form_fields === false) {$this->error = 'INTELIGO PRZELEW BRAK PARAMETROW #3';return FALSE;}
		

		# przelew jednorazowy
		$dane = $this->my_curl('https://inteligo.pl/secure/igo2','POST',array(
		'__ie__bug__workaround__' => '',
		'back_reload_protection' => $form_fields['back_reload_protection'],
		'dynsub_ex_5f832dce55045848b67cf6d742deb3855e6d8ce5' => '',
		'ias.form_protect' => $form_fields['ias_form_protect'],
		'ias_navi_map' => $form_fields['ias_navi_map'],
		'ias_post_into' => $form_fields['ias_post_into'],
		'ias_sid' => $form_fields['ias_sid'],
		'menu' => '',
         'transfer_type' => 'ONETIME'
		),NULL,'');


		# wysyłanie formularza
		
		$form_fields = $this->parse_form($dane);
		if($form_fields === false) {$this->error = 'INTELIGO PRZELEW BRAK PARAMETROW #4';return FALSE;}
		

		$post['__ie__bug__workaround__'] = '';
        $post['back_reload_protection'] = $form_fields['back_reload_protection'];
        $post['beneficiary_data-destination_account'] = $narachunek;
        $post['beneficiary_data-beneficiary_name'] = $odbiorca;
        $post['details-amount-val'] = $kwota;
        $post['details-date_multi-date'] = date('Y-m-d');
        $post['details-title'] = $tytul;
        $post['dynsub_ex_837423d158781321e3f99ffc1bbfabfc9e66bfe9'] = 'Dalej';
        $post['ias.form_protect'] = $form_fields['ias_form_protect'];
		$post['ias_navi_map'] = $form_fields['ias_navi_map'];
		$post['ias_post_into'] = '/transfers/transfer_onetime_1/submit';
        $post['ias_sid'] = $form_fields['ias_sid'];
        $post['menu'] = '';
        $post['payment_name'] = '';
        $post['source-account'] = $zrachunku;
        $post['transfer_type'] = 'ONETIME';

		$dane = $this->my_curl('https://inteligo.pl/secure/igo2','POST',$post,NULL,'');

		
		$form_fields = $this->parse_form($dane);
		if($form_fields === false) {$this->error = 'INTELIGO PRZELEW BRAK PARAMETROW #5';return FALSE;}
		
		$tmp = $this->create_tmp('pl_inteligo',$form_fields);


		preg_match('#src="/dynimg/(.+)/image\.png"#iU',$dane,$m);
			if(isset($m[1])) return array('authtype' => 'kk' , 'klucz' => 'https://secure.inteligo.com.pl/dynimg/'.$m[1].'/image.png');

		preg_match('#Podaj kod SMS nr ([0-9]+)#',$dane,$m);
		
			if(isset($m[1])) return array('authtype' => 'sms' , 'klucz' => $m[1] , 'identyfikator' => $tmp);
			
		{$this->error = 'INTELIGO PRZELEW NIE WYKRYTO NR SMS #6';return FALSE;}
	}


	public function przelew_kod($code , $sciezka) 
	{
		$this->bankowosc->o_raport->dodaj('raport', debug_backtrace());

		if(file_exists($sciezka)) require $sciezka;
		else {$this->error = 'INTELIGO KOD BRAK PLIKU Z PARAMETRAMI #1';return FALSE;}
		
		unlink($sciezka);
		
		
		$dane = $this->my_curl('https://inteligo.pl/secure/igo2','POST',array(
		'__ie__bug__workaround__' => '',
		'back_reload_protection' => $pl_inteligo_transfer_params['back_reload_protection'],
		'dynsub_12' => 'Wykonaj',
		'ias.form_protect' => $pl_inteligo_transfer_params['ias_form_protect'],
		'ias_navi_map' => $pl_inteligo_transfer_params['ias_navi_map'],
		'ias_post_into' => $pl_inteligo_transfer_params['ias_post_into'],
		'ias_sid' => $pl_inteligo_transfer_params['ias_sid'],
		'menu' => '',
		'tan' => $code
		),NULL,'');
		//echo $dane;
		if(strstr($dane,'przelew zostanie zrealizowany') === false) return FALSE;
		else return TRUE;
	}


}
