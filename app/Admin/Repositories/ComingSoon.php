<?php

namespace App\Admin\Repositories;

use Dcat\Admin\Grid;
use Dcat\Admin\Repositories\Repository;
use Illuminate\Pagination\LengthAwarePaginator;

class ComingSoon extends Repository
{
    protected $api = 'https://api.douban.com/v2/movie/coming_soon';

    protected $apiKey = 'apikey=0b2bdeda43b5688921839c8ecb20399b';

    /**
     * 查询表格数据
     *
     * @param Grid\Model $model
     * @return LengthAwarePaginator
     */
    public function get(Grid\Model $model)
    {
        $currentPage = $model->getCurrentPage();
        $perPage = $model->getPerPage();

        // 获取筛选参数
        $city = $model->filter()->input(Grid\Filter\Scope::QUERY_NAME, '广州');

        $start = ($currentPage - 1) * $perPage;

        $client = new \GuzzleHttp\Client();

        $response = $client->get("{$this->api}?{$this->apiKey}&city=$city&start=$start&count=$perPage");
        $data = json_decode((string)$response->getBody(), true);

        $paginator = new LengthAwarePaginator(
            $data['subjects'] ?? [],
            $data['total'] ?? 0,
            $perPage, // 传入每页显示行数
            $currentPage // 传入当前页码
        );

        $paginator->setPath(\url()->current());

        return $paginator;
    }


}
