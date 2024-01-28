<?php

declare(strict_types=1);

namespace App\Controllers;

/**
 * Display the default index page.
 */
class IndexController extends BaseController
{
    public function notFoundAction()
    {
        return  $this->response->setStatusCode(404, "Not Found")->setJsonContent([
            'status' => 'error',
            'message' => 'The action is not defined'
        ]);
    }

    public function indexAction()
    {
        #$this->logger->info('indexAction');
        #$this->success('Hello phalcon-swoole');
        $connection = $this->db;
        /* 判断数据源是否存在 start */
        $stmt = $connection->query("SELECT * FROM article WHERE id=1");
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        $article = $stmt->fetch();
        $this->success($article);
    }
}
