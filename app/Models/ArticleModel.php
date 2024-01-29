<?php

namespace App\Models;

use Phalcon\Mvc\Model;

class ArticleModel extends Model
{
    public function initialize()
    {
        $this->setSource('article');
    }
}
