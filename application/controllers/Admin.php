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
use Res\Util\Upload;

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
            default:
                $response += [
                    'result' => false,
                ];
        }
        echo json_encode($response);
    }

    public function upload($name)
    {
        $response = [];
        $files = $_FILES['files'] ?? [];
        if (!$files) {
            $response['result'] = 'false';
            echo json_encode($response);
            return;
        }
        switch ($name) {
            case 'user':
                $response += $this->uploadUsers($files);
                break;
            case 'phone':
                $response += $this->uploadPhones($files);
                break;
            case 'simcard':
                $response += $this->uploadSimCards($files);
                break;
            default:
                $response += [
                    'result' => false,
                ];
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
        $c = ['deleted' => User::DELETED_NO];
        if ('' !== ($_POST['name'] ?? '')) {
            $c['name@'] = $_POST['name'];
        }
        if ('' !== ($_POST['username'] ?? '')) {
            $c['username@'] = $_POST['username'];
        }
        if ('' !== ($_POST['email'] ?? '')) {
            $c['email@'] = $_POST['email'];
        }
        if ('' !== ($_POST['timeAddedMin'] ?? '')) {
            $c['timeAdded>='] = $_POST['timeAddedMin'];
        }
        if ('' !== ($_POST['timeAddedMax'] ?? '')) {
            $c['timeAdded<='] = $_POST['timeAddedMax'];
        }
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
        $response['draw'] = $_POST['draw'];
        $c = ['deleted' => Phone::DELETED_NO];
        if ('' !== ($_POST['type'] ?? '')) {
            $c['type@'] = $_POST['type'];
        }
        if ('' !== ($_POST['label'] ?? '')) {
            $c['label@'] = $_POST['label'];
        }
        if ('' !== ($_POST['os'] ?? '')) {
            $c['os@'] = $_POST['os'];
        }
        if ('' !== ($_POST['resolution'] ?? '')) {
            $c['resolution@'] = $_POST['resolution'];
        }
        if ('' !== ($_POST['ramMin'] ?? '')) {
            $c['ram>='] = $_POST['ramMin'];
        }
        if ('' !== ($_POST['ramMax'] ?? '')) {
            $c['ram<='] = $_POST['ramMax'];
        }
        if (isset($_POST['status']) && is_array($_POST['status'])) {
            $c['status()'] = $_POST['status'];
        }
        if ('' !== ($_POST['timeAddedMin'] ?? '')) {
            $c['timeAdded>='] = $_POST['timeAddedMin'];
        }
        if ('' !== ($_POST['timeAddedMax'] ?? '')) {
            $c['timeAdded<='] = $_POST['timeAddedMax'];
        }
        $count = Phone::getCount($c);
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
        $phoneList = Phone::getList($c, $order, $limit, $offset);
        if (!$phoneList) {
            $response['result'] = false;
            $response['message'] = '没有记录';
            return $response;
        }
        $data = [];
        $index = 1;
        $fields = Phone::COLUMNS;
        $fields = array_flip($fields);
        foreach ($phoneList as $phone) {
            $row = [];
            foreach ($columns as $column) {
                $value = '';
                if ('id' === $column) {
                    $value .= '<span class="checkbox"><label><input value="' . $phone->$column() . '" type="checkbox"> ' . ($index + $offset). '</label></span>';
                } elseif ('#action' === $column) {
                    $value .= '<button class="btn btn-info" style="padding-bottom: 0;padding-top: 0;"><i class="fa fa-edit"></i> 编辑</button>';
                } elseif (array_key_exists($column, $fields)) {
                    switch ($column) {
                        default:
                            $value .= '<span>' . htmlspecialchars($phone->$column()) . '</span>';
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
        $response['draw'] = $_POST['draw'];
        $c = [];
        if ('' !== ($_POST['phoneNumber'] ?? '')) {
            $c['phoneNumber@'] = $_POST['phoneNumber'];
        }
        if ('' !== ($_POST['place'] ?? '')) {
            $c['place@'] = $_POST['place'];
        }
        if ('' !== ($_POST['label'] ?? '')) {
            $c['label@'] = $_POST['label'];
        }
        if (isset($_POST['status']) && is_array($_POST['status'])) {
            $c['status()'] = $_POST['status'];
        }
        if ('' !== ($_POST['timeAddedMin'] ?? '')) {
            $c['timeAdded>='] = $_POST['timeAddedMin'];
        }
        if ('' !== ($_POST['timeAddedMax'] ?? '')) {
            $c['timeAdded<='] = $_POST['timeAddedMax'];
        }
        $count = SimCard::getCount($c);
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
        $simCardList = SimCard::getList($c, $order, $limit, $offset);
        if (!$simCardList) {
            $response['result'] = false;
            $response['message'] = '没有记录';
            return $response;
        }
        $data = [];
        $index = 1;
        $fields = SimCard::COLUMNS;
        $fields = array_flip($fields);
        foreach ($simCardList as $simCard) {
            $row = [];
            foreach ($columns as $column) {
                $value = '';
                if ('id' === $column) {
                    $value .= '<span class="checkbox"><label><input value="' . $simCard->$column() . '" type="checkbox"> ' . ($index + $offset). '</label></span>';
                } elseif ('#action' === $column) {
                    $value .= '<button class="btn btn-info" style="padding-bottom: 0;padding-top: 0;"><i class="fa fa-edit"></i> 编辑</button>';
                } elseif (array_key_exists($column, $fields)) {
                    switch ($column) {
                        default:
                            $value .= '<span>' . htmlspecialchars($simCard->$column()) . '</span>';
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

    private function uploadUsers(array &$files) : array
    {
        $response = [
            'result' => true,
        ];

        return $response;
    }

    private function uploadPhones(array &$files) : array
    {
        $response = [
            'result' => true,
        ];

        return $response;
    }

    private function uploadSimCards(array &$files) : array
    {
        $response = [
            'result' => true,
        ];

        return $response;
    }
}