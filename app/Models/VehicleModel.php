<?php
use \CodeIgniter\Database\ConnectionInterface;
namespace App\Models;
class VehicleModel extends \CodeIgniter\Model{
        protected $table      = 'vehicles';
        protected $primaryKey = 'id';
        protected $useTimestamps = false;
        protected $allowedFields = ['retailPrice','brand','model','bodyType','portugueseVersion','qGear','year','month','km','engine','url'];
		
}