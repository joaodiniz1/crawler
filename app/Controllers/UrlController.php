<?php 
namespace App\Controllers;
use App\Controllers\CrawlerController;
use App\Models\UrlModel;
use DomXPath;
class UrlController extends BaseController{
	private $url;
	private $page=0;

	function set($field,$value){
		$this->$field = trim($value);
	}
	
	function get($field){
		return $this->$field;
	}
	
	/* Goes each page of Stand Virtual advanced search looking for valid links */
	function crawlUrls(){
		$crawler = new CrawlerController();
		$this->set('page',$this->request->getVar('page'));
		$url = new UrlModel();
		if ($this->get('page')==0){
			$this->set('url','https://www.standvirtual.com/carros/?search');
		}else{
			$this->set('url','https://www.standvirtual.com/carros/?search&page='.$this->get('page'));
		}
		$xpath = new DomXPath($crawler->processHtml($this->get('url')));
		$nodes = $xpath->query('//a');
		foreach($nodes as $node) {
			/* If the link is a valid vehicle add, saves it for post processing */
			if (substr_count($node->getAttribute('href'),'https://www.standvirtual.com/anuncio/')>0){
				$validUrl = explode('.html',$node->getAttribute('href'));
				if (isset($validUrl[0])){
					$this->set('url',$validUrl[0].'.html');
					$this->save();
				}
			}
		}
	}
	
	/* Saves valid vehicle url on crawler queue */
	function save(){
		$url = new UrlModel();
		$result = (array)$url->query('SELECT COUNT(*) as qUrls FROM urls WHERE url LIKE "%'.$this->get('url').'%"')->getRow();
		if ($result['qUrls']==0){
			$result = (array)$url->query('SELECT COUNT(*) as qVehicles FROM vehicles WHERE url LIKE "%'.$this->get('url').'%"')->getRow();
			if ($result['qVehicles']==0){
				$url->query('INSERT INTO urls (url) VALUES ("'.$this->get('url').'")');
			}
		}
	}
}