<?php
/**
 * Created by PhpStorm.
 * User: KE
 * Date: 2018/1/8
 * Time: 10:51
 */

use Res\Model\Notification as NotifyModal;

class Notification extends CI_Controller
{
    public function count()
    {
        ini_set('max_execution_time', 31);
        $response    = [
            'result'  => true,
            'message' => 0,
        ];
        $originCount = intval($_POST['originCount'] ?? 0);
        $c           = [
            'userId' => App::getUser()->id(),
            'read'   => NotifyModal::READ_NO,
        ];
        $begin       = $now = microtime(true);
        while ($now - $begin < 30) {
            try {
                $response['message'] = NotifyModal::getCount($c);
            } catch (Throwable $t) {
                $response['result'] = false;
                $response['error']  = $t->getMessage();
            }
            if (!$response['message'] || $originCount === intval($response['message'])) {
                usleep(500000);
                $now = microtime(true);
                continue;
            }
            break;
        }
        echo json_encode($response);
    }

    public function dataTable()
    {
        $response = [
            'result'          => true,
            'recordsTotal'    => 0,
            'recordsFiltered' => 0,
            'data'            => [],
        ];
        if ('' === ($_POST['draw'] ?? '')) {
            $response['result']  = false;
            $response['message'] = '缺少参数"draw"';
            echo json_encode($response);
            return;
        }
        $response['draw'] = $_POST['draw'];
        $c                = [
            'userId'  => App::getUser()->id(),
            'deleted' => NotifyModal::DELETED_NO,
        ];
        if ('' !== ($_POST['type'] ?? '')) {
            $c['type@'] = $_POST['type'];
        }
        $count   = NotifyModal::getCount($c);
        $columns = [];
        foreach ($_POST['columns'] as $columnDef) {
            $columns[] = $columnDef['data'];
        }
        $order            = ['id' => 'desc'];
        $limit            = $_POST['length'];
        $offset           = $_POST['start'];
        $notificationList = NotifyModal::getList($c, $order, $limit, $offset);
        if (!$notificationList) {
            $response['result']  = false;
            $response['message'] = '没有记录';
            echo json_encode($response);
            return;
        }
        $data   = [];
        $index  = 1;
        $fields = NotifyModal::COLUMNS;
        $fields = array_flip($fields);
        foreach ($notificationList as $notification) {
            $row = [];
            foreach ($columns as $column) {
                $value = '';
                if ('id' === $column) {
                    $value .= '<label class="index-label" data-id="' . $notification->$column() . '">' . ($index++ + $offset) . '</label>';
                } elseif ('#action' === $column) {
                    $value .= '';
                } elseif (array_key_exists($column, $fields)) {
                    switch ($column) {
                        case 'status':
                            $value .= '';
                            break;
                        case 'message':
                            $value .= nl2br($notification->$column());
                            break;
                        default:
                            $value .= '<span>' . htmlspecialchars($notification->$column()) . '</span>';
                            break;
                    }
                }
                $row[$column] = $value;
            }
            $data[] = $row;
        }
        $response['data']         = $data;
        $response['recordsTotal'] = $response['recordsFiltered'] = $count;
        echo json_encode($response);
    }
}