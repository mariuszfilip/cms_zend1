<?php

class curl
{
	//klasa odpowiada za tworzenie polaczen curl
	
	public $o_raport;		//zawiera raport
	public $error;			//zawiera ostatni error
	public $post;			//zawiera tablice z postami do wyslania
	public $ciastko;		//adres ciastka
	public $timeout=10;		//timeout
	public $followlocation=1;
	public $verbose=1;	//debug ?
	public $useragent='Mozilla/4.0 (compatible; Lilion PHP:cURL client;)';
	public $sslversion=3;
	
	public function __construct()
	{
		$this->o_raport=new raport();
		$this->o_raport->dodaj('raport');		
	}
	
	public function wykonaj($url)
	{
        echo '<br/>';
        echo '<br/>';
        echo '<br/>';
		$this->o_raport->dodaj('raport');		

		if(isset($_SESSION['cms_tryb']) && strpos($_SESSION['cms_tryb'], 'nocurl')!==FALSE)
		{
			$this->o_raport->dodaj('error', 'NOCURL');		
			return null;
		}

		$curl=curl_init();
//		var_dump($curl);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSLVERSION, $this->sslversion);
		curl_setopt($curl, CURLOPT_TIMEOUT, $this->timeout);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, $this->followlocation);
		curl_setopt($curl, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($curl, CURLOPT_VERBOSE, $this->verbose);
		
		if(substr($url, 0, 5)=='https')	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);

		if($this->ciastko)
		{
            curl_setopt($curl, CURLOPT_COOKIEFILE, $this->ciastko);
			curl_setopt($curl, CURLOPT_COOKIEJAR, $this->ciastko);
		}
		
		if($this->post)
		{
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($this->post));
		}

		$wynik=curl_exec($curl);
		
		if($wynik===FALSE) $this->error=curl_errno($curl).': '.curl_error($curl);
		
		return $wynik;
	}
}

?>
