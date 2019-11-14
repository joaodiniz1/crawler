<?php 
namespace App\Controllers;
use App\Models\CrawlerModel;
use DOMDocument;
class CrawlerController extends BaseController{

	/* Calls crawler default interface */
	public function index(){
		$crawler = new CrawlerModel();
		$data['vehicles'] = $crawler->query('Select * from vehicles order by id desc limit 100')->getResult();
		$data['qVehicles'] = (array)$crawler->query('Select COUNT(*) as totalRows from vehicles')->getRow();
		$data['qUrls'] = (array)$crawler->query('Select COUNT(*) as totalRows from urls')->getRow();
		return view('crawler',$data);
	}
	
	/* Receive an url and returns his html version */
	public function processHtml($url){
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($curl);
		curl_close($curl);
		$doc = new DOMDocument();
		@$doc->loadHTML($result);
		return $doc;
	}
}