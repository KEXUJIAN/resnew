<?php
/**
 * Created by PhpStorm.
 * User: KE
 * Date: 2018/1/10
 * Time: 23:36
 */

use Res\Model\Request as ReqModal;

class Request extends CI_Controller
{
    public function dataTable()

    {
        $response = [
            'result' => true,
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => [],
        ];
        if ('' === ($_POST['draw'] ?? '')) {
            $response['result'] = false;
            $response['message'] = '缺少参数"draw"';
            echo json_encode($response);
            return;
        }
        $response['draw'] = $_POST['draw'];
        $data = [];
        $count = 0;
        $response['data'] = $data;
        $response['recordsTotal'] = $response['recordsFiltered'] = $count;
        echo json_encode($response);
    }
}