<?php

namespace App\Models;

use CodeIgniter\Model;

class EventCategoryModel extends Model
{
    protected $table = 'event_category';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = false;
    protected $allowedFields = [];

}