<?php

namespace App\Models;

use Phalcon\Mvc\Model;

class ArticleModel extends Model
{
    public $id;
    public $title;
    public $content;
    public $category;
    public $status;
    public $sort;
    public $top;
    public $deleted;
    public $created_at;
    public $updated_at;

    public function initialize()
    {
        $this->setSource('article');
    }
}
