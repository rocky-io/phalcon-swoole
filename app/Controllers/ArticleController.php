<?php

declare(strict_types=1);

namespace App\Controllers;

use Phalcon\Mvc\Model\Manager;

use App\Models\ArticleModel;
use Phalcon\Http\Response;

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
    public function viewAction(int $id): Response
    {
        $article = ArticleModel::findFirst(
            [
                'id = :id:',
                'bind' => ['id' => $id],
            ]
        );
        return $this->success(['article' => $article]);
    }

    public function createAction(): Response
    {
        $content = json_decode($this->swooleRequest->getContent(), true);
        /**
         * @param ArticleModel $articleModel
         */
        $articleModel = new ArticleModel();
        $articleModel->assign(
            $content,
            ['title', 'content', 'category', 'sort']
        );
        $res = $articleModel->save();
        if ($res) {
            $articleModel->toArray();
            return $this->success(['article' => $articleModel->toArray()], '文章新增成功');
        } else {
            return $this->error('文章新增失败');
        }
    }

    /**
     * @param int $id
     */
    public function updateAction(int $id): Response
    {
        $content = json_decode($this->swooleRequest->getContent(), true);
        $article = ArticleModel::findFirst(
            [
                'id = :id:',
                'bind' => ['id' => $id],
            ]
        );
        $article->assign(
            $content,
            ['title', 'content', 'category', 'sort']
        );
        $res = $article->save();
        if ($res) {
            return $this->success(['article' => $article->toArray()], '文章更新成功');
        } else {
            return $this->error('文章更新失败');
        }
    }

    /**
     * @param int $id
     */
    public function deleteAction(int $id): Response
    {
        $article = ArticleModel::findFirst(
            [
                'id = :id:',
                'bind' => ['id' => $id],
            ]
        );
        $res = $article->delete();
        if ($res) {
            return $this->success([], '文章删除成功');
        } else {
            return $this->error('文章删除失败');
        }
    }

    /**
     * @param int $id
     */
    public function listAction(): Response
    {
        $get = $this->swooleRequest->get;
        $pageNum = $get['pageNum'] ?? 1;
        $limit = $get['limit'] ?? 10;
        $offset =  ($pageNum - 1) * $limit;
        $parameters = [
            'conditions' => '',
            'columns' => [
                'id',
                'title',
                'content',
                'status'
            ],
            'limit' => $limit,
            'offset' => $offset
        ];
        $articleList = ArticleModel::find($parameters)->toArray();
        return $this->success(['list' => $articleList]);
    }
}
