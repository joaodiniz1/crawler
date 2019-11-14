<?php
use \CodeIgniter\Database\ConnectionInterface;
namespace App\Models;
class UrlModel extends \CodeIgniter\Model{
        protected $table      = 'urls';
        protected $primaryKey = 'id';
        protected $useTimestamps = false;
        protected $allowedFields = ['url'];
}