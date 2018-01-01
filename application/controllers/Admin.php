<?php
/**
 * Created by PhpStorm.
 * User: KE
 * Date: 2018/1/1
 * Time: 11:54
 */

use \Res\Model\Phone;
use \Res\Model\SimCard;
use \Res\Model\User;

class Admin extends CI_Controller
{
    public function console()
    {
        App::view('console');
    }

    public function data($name)
    {
        $response = [];
        switch ($name) {
            case 'user':
                $response += $this->dataUsers();
                break;
            case 'phone':
                $response += $this->dataPhones();
                break;
            case 'simcard':
                $response += $this->dataSimCards();
                break;
        }
        echo json_encode($response);
    }

    private function dataUsers() : array
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
            return $response;
        }
        $response['draw'] = $_POST['draw'];
        $c = [];
//        if ('' !== ($_POST['']))
        $count = User::getCount($c);
        $columns = [];
        foreach ($_POST['columns'] as $columnDef) {
            $columns[] = $columnDef['data'];
        }
        $order = [];
        if ($_POST['order'] ?? []) {
            foreach ($_POST['order'] as $orderDef) {
                $key = $columns[$orderDef['column']];
                $order[$key] = 'desc' === $orderDef['dir'] ? 'desc' : 'asc';
            }
        }
        $limit = $_POST['length'];
        $offset = $_POST['start'];
        $userList = User::getList($c, $order, $limit, $offset);
        if (!$userList) {
            $response['result'] = false;
            $response['message'] = '没有记录';
            return $response;
        }
        $data = [];
        $index = 1;
        $fields = User::COLUMNS;
        $fields = array_flip($fields);
        foreach ($userList as $user) {
            $row = [];
            foreach ($columns as $column) {
                $value = '';
                if ('id' === $column) {
                    $value .= '<span class="checkbox"><label><input value="' . $user->$column() . '" type="checkbox"> ' . ($index + $offset). '</label></span>';
                } elseif ('#action' === $column) {
                    $value .= '<button class="btn btn-info" style="padding-bottom: 0;padding-top: 0;"><i class="fa fa-edit"></i> 编辑</button>';
                } elseif (array_key_exists($column, $fields)) {
                    switch ($column) {
                        case 'role':
                            $value .= '<span>' . User::LABEL_ROLE[$user->$column()] . '</span>';
                            break;
                        default:
                            $value .= '<span>' . htmlspecialchars($user->$column()) . '</span>';
                            break;
                    }
                }
                $row[$column] = $value;
            }
            $data[] = $row;
        }
        $response['data'] = $data;
        $response['recordsTotal'] = $response['recordsFiltered'] = $count;
        return $response;
    }

    private function dataPhones() : array
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
            return $response;
        }
        return $response;
    }

    private function dataSimCards() : array
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
            return $response;
        }
        return $response;
    }
}