<?php

abstract class bankowosc_szablon
{
	//klasa obejmuje metody wspoldzielone
	//metody raportujace sa wywolywane na nadobiekcie
	//$this->bankowosc->raport_dodaj
	//$this->bankowosc->error_dodaj
	
	/*
	SPIS TRESCI

	public function __construct($bankowosc, $dane)
	protected function object2array($data)
	protected function my_curl($url, $method='POST', $data=NULL, $cookiepath=NULL, $options='', $extraheaders = array())
	protected function pobierz_kktan($bank, $klucz)
	protected function get_between($data,$prefix,$postfix)
	protected function get_domain($url)
	protected function trace_debug($data,$domain = '')
	protected function cleanamount($str)
	protected function rozbij_kwote($kwota,$zwroc='T')
	protected function wylicz_nrb($numer)
	protected function parseCSV($str, $delimiter = ',', $enclosure = '"', $len = 4000)
	protected function utime($date)
	protected function parseutime($time)
	protected function create_tmp($prefix,$dane)
	protected function kwota_waluta($str)
	*/
	
	protected	$bankowosc;		//nadobiekt
	protected	$o_curl;		//klasa curla
	protected	$dane;			//login, haslo, konto, ciastko
	public		$batch;			//id ostatniego przelewu (jesli nieznane to 1)
	
	public function __construct($bankowosc, $dane)
	{
		$this->bankowosc=$bankowosc;

		$this->bankowosc->o_raport->dodaj('raport');

		$this->o_curl=new curl();
		$this->dane=$dane;
	}

	protected function object2array($data)
	{
		//funkcja zwraca array zamiast obiektu

		$this->bankowosc->o_raport->dodaj('raport');
		
		if (is_object($data))
		{
			$data=get_object_vars($data);
			if(count($data)==0) $data=NULL; 
		}
		return is_array($data) ? array_map(array('cms','object2array'), $data) : $data;
	}

	/* 
		Metoda my_curl jest nakładką na curla, skraca zapis kodu i przyspiesza pisanie aplikacji
	*/
	protected function curl($url)
	{
		//nakladka na o_curl, ktora laduje wynik do raportu
		
		$wynik=$this->o_curl->wykonaj($url);
		
		$this->bankowosc->o_raport->dodaj('curl', null, $wynik);
		
		return $wynik;
	}

	protected function my_curl($url, $method='POST', $data=NULL, $cookiepath=NULL, $options='', $extraheaders = array())
	{
		$this->bankowosc->o_raport->dodaj('raport');
		
		$ismultipart = $withheader = $debug = $nojs = $displaypost = 0;
		
		if(is_string($options))
		{
			$options = explode('|',$options);
		}
		else $options = array();
		
		foreach($options AS $o)
		{
			if($o == 'MULTIPART') $ismultipart = 1;
			if($o == 'HEADER') $withheader = 1;
			if($o == 'DEBUG') $debug = 1;
			if($o == 'NOJS') $nojs = 1;
			if($o == 'AJAX') $extraheaders[] = 'X-Requested-With: XMLHttpRequest';
			if($o == 'REQ') $displaypost = 1;
		} 
		
		

		if(empty($url)) return FALSE;

		if($method == 'POST')
		{
			if(is_array($data) && count($data) > 0)
			{
				if($ismultipart)
				{
					$postdata = $data;
				}
				else
				{
					$postdata = http_build_query($data,'','&');
				}
			}
			elseif(is_string($data))
			{
				$postdata = $data;
			}
			else $postdata = '';
		}
		else
		{
			if(is_array($data) && count($data) > 0)
			{
				$url .= http_build_query($data,'','&');
			}	
			elseif(is_string($data))
			{
				$url .= $data;
			}
		}
		
		if($displaypost) var_dump($postdata);
		if(isset($this->debug_trace) && $this->debug_trace == 1) $withheader = 1;

		$curlheaders = array(
		'Accept-Language: pl,en;q=0.7,en-us;q=0.3',
		'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.64 Safari/537.31',
		'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
		'Accept-Charset: ISO-8859-2,utf-8;q=0.7,*;q=0.7',
		'Expect:'
		);
		
		if(count($extraheaders) > 0) $curlheaders = array_merge($curlheaders,$extraheaders);
		
		$ch = curl_init($url);
		if($withheader || $debug){ curl_setopt($ch, CURLOPT_HEADER, TRUE); }
		if(isset($postdata)) 
		{

			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
		}
		if(!empty($cookiepath))
		{
			curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
			curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiepath);
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLINFO_HEADER_OUT, 1);

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		 
		
		curl_setopt($ch, CURLOPT_HTTPHEADER, $curlheaders);
		if($_GET['id_rachunki']==146){
			//curl_setopt($ch, CURLOPT_HEADER, TRUE);
			
		}
		$resp = curl_exec($ch);
		if($_GET['id_rachunki']==146){
		
			//print_r(curl_getinfo($ch,CURLINFO_HEADER_OUT));
		}
		curl_close($ch);
		
		if(isset($this->debug_trace) && $this->debug_trace == 1) $this->trace_debug($method.' '.$url."\n<br />\n".$resp,$this->get_domain($url));
		
		if($debug) {echo '<pre>' , htmlspecialchars($resp);exit;}
		if($nojs) {echo preg_replace('#<script.+</script>#siU','',$resp);exit;}

		$this->bankowosc->o_raport->dodaj('curl', null, $resp);
		
		return $resp;
	}
	
	protected function pobierz_kktan($bank, $klucz)
	{
		//funkcja zwraca kod z karty kodow lub tan z db

		$this->bankowosc->o_raport->dodaj('raport');

		$wynik=$this->bankowosc->mysql->komorka_bezposrednie("SELECT `wartosc` FROM `banki_karty_kody` LEFT JOIN `banki_karty` ON (`banki_karty`.`id`=`banki_karty_kody`.`id_kk`) WHERE `widok`='1' AND `bankowosc_kod`='$bank' AND `klucz`='$klucz'");

		return $wynik;
	}

	/* 
		Metoda get_between zwraca string który jest otoczony prefixem i postfixem
	*/
	protected function get_between($data,$prefix,$postfix)
	{
		$this->bankowosc->o_raport->dodaj('raport');
		
		$expl = explode($prefix,$data);
		$expl = explode($postfix,$expl[1]);
		return $expl[0];
	}
	
	/* 
		Metoda get_domain zwraca nazwę właściwej domeny z podanego urla
	*/
	protected function get_domain($url)
	{
		$this->bankowosc->o_raport->dodaj('raport');
		
		$host = parse_url($url,PHP_URL_HOST);
		
		preg_match('#([^.]+)\.(?:com\.pl|pl|com)$#i',$host,$matches);
		
		if(isset($matches[1])) return $matches[1];
		
		return (string) $host;
	}
	
	/* 
		Metoda trace_debug zapisuje podane dane w katalogu debug/ numerowane od 1 poprzedzone unikalnym prefixem
	*/
	protected function trace_debug($data,$domain = '')
	{
		$this->bankowosc->o_raport->dodaj('raport');
		
		$this->debugger_counter++;
		if(!isset($this->debugger_prefix)) $this->debugger_prefix = uniqid();
		
		file_put_contents('debug/'.$this->debugger_prefix .'_'. $domain . '_'. $this->debugger_counter.'.htm',$data);
	}
	
	/* 
		Metoda cleanamount oczyszcza kwotę, zamienia przecinek na kropkę i zwraca float z podanego stringa
	*/
	protected function cleanamount($str)
	{
		$this->bankowosc->o_raport->dodaj('raport');
		
		$str = preg_replace('#[^0-9,-]#','',$str);
		return floatval(str_replace(',','.',$str));
	}
	
	/* 
		Metoda rozbij_kwote zwraca jako string część całkowitą lub ułamkową z podanej kwoty typu float
	*/
	protected function rozbij_kwote($kwota,$zwroc='T')
	{
		$this->bankowosc->o_raport->dodaj('raport');
		
		$kwota = number_format($kwota, 2, '.', '');
		$tmp = explode('.',$kwota);
		
		if($zwroc === 'A') return $tmp[0];
		elseif($zwroc === 'B') return $tmp[1];
		else return array('A' => $tmp[0] , 'B' => $tmp[1]);
	}
	
	/* 
		Metoda wylicz_nrb zwraca liczbę kontrolną podanego numeru NRB, np. PL 00 24_znakowy_NUMERKONTA
	*/
	protected function wylicz_nrb($numer)
	{
		$this->bankowosc->o_raport->dodaj('raport');
		
		$numer = str_replace(' ','',$numer);
		
		$numer = substr($numer,4) . substr($numer,0,4);
		
		$numer = str_replace(array('P','L'),array('25','21'),$numer);
		
		return 98 - bcmod($numer, 97);
	}
	
	/* 
		Metoda parseCSV zwraca podany string z csv jako tablicę, domyślny separator to przecinek
	*/
	protected function parseCSV($str, $delimiter = ',', $enclosure = '"', $len = 4000)
	{
		$this->bankowosc->o_raport->dodaj('raport');
		
		$result = array();
		
		$fh = fopen('php://memory', 'rw');
		fwrite($fh, $str);
		rewind($fh); 
		
		//$result = fgetcsv( $fh, $len, $delimiter, $enclosure );
		
		while( ($data = fgetcsv( $fh, $len, $delimiter, $enclosure )) !== FALSE ) 
		{
			$result[] =	$data;
		}
		
		fclose($fh); 
		return $result;
	} 
	
	
	/* 
		Metoda utime zwraca datę podaną jako rrrr-mm-dd lub dd-mm-rrrr lub inną w formacie uniwersalnym rrrrmmdd
	*/
	protected function utime($date)
	{
		$this->bankowosc->o_raport->dodaj('raport');
		
		if(strlen($date) === 10 && ($date[2] === '-' || $date[2] === '.' || $date[2] === '/') && ($date[5] === '-' || $date[5] === '.' || $date[5] === '/'))
			{	return $date[6].$date[7].$date[8].$date[9].$date[3].$date[4].$date[0].$date[1];}
			
		elseif(strlen($date) === 10 && ($date[4] === '-' || $date[4] === '.' || $date[4] === '/') && ($date[7] === '-' || $date[7] === '.' || $date[7] === '/'))
			{	return $date[0].$date[1].$date[2].$date[3].$date[5].$date[6].$date[8].$date[9];}
		
		else
			{	return date('Ymd',strtotime($date));}
	}
	
	/* 
		Metoda parseutime zwraca datę podaną jako rrrrmmdd w formie tablicy
	*/
	protected function parseutime($time)
	{
		$this->bankowosc->o_raport->dodaj('raport');
		
		$time = (string) $time;
		
		if(strlen($time) >= 8)
		{
			$ret['isvalid'] = TRUE;
			$ret['y'] = $time[0].$time[1].$time[2].$time[3];
			$ret['m'] = $time[4].$time[5];
			$ret['d'] = $time[6].$time[7];
		}
		else $ret['isvalid'] = FALSE;

		return $ret;
	}
	
	/* 
		Metoda create_tmp tworzy plik tymczasowy zawierający tablicę podaną jako $dane o nazwie $prefix_transfer_params
	*/
	protected function create_tmp($prefix,$dane)
	{
		$this->bankowosc->o_raport->dodaj('raport');
		
		$sciezka = 'zrodla/tmp/'.$prefix.'_'.md5(uniqid() . $prefix . time() . rand(1,999)).'.php';
		file_put_contents($sciezka,"<?php \n\n $".$prefix.'_transfer_params = '.var_export($dane,1).";\n?>");
		return $sciezka;
	}


	//metoda kasuje nadmiar spacji (po tysiacach) i zamienia przecinek na kropke
	protected function kwota_waluta($str)
	{
		$this->bankowosc->o_raport->dodaj('raport');

		preg_match('#(.+) ([A-Z]+)#',$str,$matches);
		return array(
		'waluta' => $matches[2],
		'kwota' => floatval(str_replace(' ', '', str_replace(array(',','&#xA0;'),array('.',''),trim($matches[1]))))
		);
	}
}
?>
