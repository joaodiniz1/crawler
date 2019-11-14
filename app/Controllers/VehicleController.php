<?php 
namespace App\Controllers;
use App\Controllers\CrawlerController;
use App\Models\VehicleModel;
use App\Models\UrlModel;
use DomXPath;
class VehicleController extends BaseController{
	private $retailPrice;
	private $brand;
	private $model;
	private $bodyType;
	private $engine;
	private $portugueseVersion;
	private $qGear;
	private $year;
	private $month;
	private $km;	
	private $url;
	
		
	function set($field,$value){
		$this->$field = trim(mb_convert_encoding(str_replace('"','',str_replace("'","",$value)),"UTF-8"));
	}
	
	function get($field){
		return $this->$field;
	}

	/* Looks on urls table for vehicle links to be crawled */
	function crawlVehicle(){
		$crawler = new CrawlerController();
		/* Gets an url to be crawled */
		$this->set('url',$this->getNextCrawl());
		/* Removes it from the urls table to dont be processed again */
		$this->remove();
		if ($this->get('url')<>''){
			/* Process page html looking for specific tags with useful information */
			$xpath = new DomXPath($crawler->processHtml($this->get('url')));
			/* Gets current url Vehicle Price Information */
			$nodeList = $xpath->query("//span[@class='offer-price__number']");
			if (isset($nodeList->item(0)->nodeValue)){
				$this->set('retailPrice',intval(trim(str_replace(" ","",str_replace("EUR","",$nodeList->item(0)->nodeValue)))));
			}
			/*  Gets current url Vehicle Details Information */
			$nodeList = $xpath->query("//li[@class='offer-params__item']");
			/* Search for specific values in details O(n) complexity */
			foreach ($nodeList as $nl){
				if (substr_count($nl->nodeValue,'Marca')>0){
					$this->set('brand',str_replace('Marca','',trim($nl->nodeValue)));
				}else if (substr_count($nl->nodeValue,'Modelo')>0){
					$this->set('model',str_replace('Modelo','',trim($nl->nodeValue)));
				}else if (substr_count($nl->nodeValue,'Segmento')>0){
					$this->set('bodyType',str_replace('Segmento','',trim($nl->nodeValue)));
				}else if (substr_count($nl->nodeValue,'Versão')>0){
					$temp = explode(" ",trim(str_replace('Versão','',trim($nl->nodeValue))));
					$this->set('engine',@$temp[0].' '.@$temp[1]);
					$this->set('portugueseVersion',@$temp[2].' '.@$temp[3].' '.@$temp[4].' '.@$temp[5]);
				}else if (substr_count($nl->nodeValue,'Número de Mudanças')>0){
					$this->set('qGear',str_replace('Número de Mudanças','',trim($nl->nodeValue)));
				}else if (substr_count($nl->nodeValue,'Ano de Registo')>0){
					$this->set('year',intval(str_replace('Ano de Registo','',trim($nl->nodeValue))));
				}else if (substr_count($nl->nodeValue,'Mês de Registo')>0){
					$this->set('month',str_replace('Mês de Registo','',trim($nl->nodeValue)));
				}else if (substr_count($nl->nodeValue,'Quilómetros')>0){
					$this->set('km',intval(str_replace('Quilómetros','',trim(str_replace(' ','',str_replace('km','',$nl->nodeValue))))));
				}	
			}
			/* Saves vehicle data crawled on database */
			$this->save();
			/* Returns status of crawled data to view */
			$vehicle = new VehicleModel();
			$url = new UrlModel();
			$output = $vehicle->countAll().'/'.$url->countAll();
			return $output;
		}
	}
	
	/* Insert vehicle crawled on db */
	function save(){
		/* Checks if vehicle crawled has minimum data valuable to be inserted */
		if (($this->retailPrice<>'')&&($this->brand<>'')&&($this->model<>'')){
			$vehicle = new VehicleModel();
			$vehicle->query('INSERT INTO vehicles (retailPrice,brand,model,bodyType,portugueseVersion,qGear,year,month,km,engine,url) VALUES ("'.$this->get('retailPrice').'","'.$this->get('brand').'","'.$this->get('model').'","'.$this->get('bodyType').'","'.$this->get('portugueseVersion').'","'.$this->get('qGear').'","'.$this->get('year').'","'.$this->get('month').'","'.$this->get('km').'","'.$this->get('engine').'","'.$this->get('url').'")');
		}
	}	
	
	/* Remove crawled vehicle url from urls database */
	function remove(){
		$vehicle = new VehicleModel();
		$vehicle->query('DELETE from urls where url = "'.$this->get('url').'"');
	}	
	
	/* Get next vehicle to be crawled from queue */
	function getNextCrawl(){
		$vehicle = new VehicleModel();
		$result = (array)$vehicle->query('SELECT * FROM urls limit 1')->getRow();
		if (isset($result['url'])){
			return $result['url'];
		}
	}
}