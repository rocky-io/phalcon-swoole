<?php

declare(strict_types=1);

namespace App\Controllers;

use Phalcon\Mvc\Model\Manager;

use App\Models\ArticleModel;

/**
 * @property Manager $modelsManager
 * @property Request $request
 * @property View    $view
 */
class ArticleController extends BaseController
{
    /**
     * @param int $id
     */
    public function viewAction($id)
    {
        $this->logger->info('viewction');
        $article = ArticleModel::findFirst(
            [
                'id = :id:',
                'bind' => ['id' => $id],
            ]
        );
        $this->success($article);
    }
}
