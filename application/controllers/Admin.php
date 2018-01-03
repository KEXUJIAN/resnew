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
use \Res\Model\UploadFile;
use \Res\Util\MyExcel;

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
        $uploader = AppService::getUploader();
        $error = $uploader->check($files);
        if ($error) {
            $response['result'] = false;
            $response += $error;
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
                    $value .= '<span class="checkbox"><label><input value="' . $user->$column() . '" type="checkbox"> ' . ($index++ + $offset). '</label></span>';
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
                    $value .= '<span class="checkbox"><label><input value="' . $phone->$column() . '" type="checkbox"> ' . ($index++ + $offset). '</label></span>';
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
                    $value .= '<span class="checkbox"><label><input value="' . $simCard->$column() . '" type="checkbox"> ' . ($index++ + $offset). '</label></span>';
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
        $excel = new MyExcel();
        $head = [
        ];
        $excelResult = $excel->load($files['tmp_name'], $head);
        $response = array_merge($response, $excelResult);
        if (!$excelResult['result']) {
            return $response;
        }

        foreach ($excelResult['content'] as &$row) {
            if ($row['label']['value']) {
                $phone = Phone::getOne([
                    'label' => $row['label']['value'],
                    'deleted' => Phone::DELETED_NO,
                ]);
                if ($phone) {
                    unset($phone);
                    continue;
                }
            }
            $o = new Phone();
            foreach ($row as $name => &$def) {
                $value = $def['value'];
                if ('' === $value && 'status' !== $name) {
                    continue;
                }
            }
            unset($def);
            $o->save();
        }
        unset($row);
        $uploader = AppService::getUploader();
        $fileName = $uploader->saveFile($files, UploadFile::TYPE_USER_EXCEL);
        $o = new UploadFile();
        $o->type(UploadFile::TYPE_USER_EXCEL);
        $o->originName($files['name']);
        $o->fileName($fileName);
        $o->uploadByUser(App::getUser()->id());
        $o->save();
        return $response;
    }

    private function uploadPhones(array &$files) : array
    {
        $response = [
            'result' => true,
        ];
        $excel = new MyExcel();
        $head = [
            'type' => ['#机型#u'],
            'os' => ['#系统#u'],
            'resolution' => ['#分辨率#u'],
            'ram' => ['#ram#i'],
            'carrier' => ['#运营商#u'],
            'screenSize' => ['#屏幕尺寸#u'],
            'label' => ['#编号#u'],
            'imei' => ['#imei#i'],
            'status' => ['#状态#u'],
        ];
        $excelResult = $excel->load($files['tmp_name'], $head);
        $response = array_merge($response, $excelResult);
        if (!$excelResult['result']) {
            return $response;
        }
        $carrierList = array_flip(Phone::LABEL_CARRIER);
        foreach ($excelResult['content'] as &$row) {
            if ($row['label']['value']) {
                $phone = Phone::getOne([
                    'label' => $row['label']['value'],
                    'deleted' => Phone::DELETED_NO,
                ]);
                if ($phone) {
                    unset($phone);
                    continue;
                }
            }
            $o = new Phone();
            foreach ($row as $name => &$def) {
                $value = $def['value'];
                if ('' === $value && 'status' !== $name) {
                    continue;
                }
                switch ($name) {
                    case 'resolution':
                        if (!preg_match_all('#(\d+)#', $value, $match)) {
                            break;
                        }
                        $match = $match[0];
                        if (2 !== count($match)) {
                            break;
                        }
                        $value = "{$match[0]} X {$match[1]}";
                        $o->$name($value);
                        break;
                    case 'carrier':
                        if (!preg_match_all("#电信|移动|联通#u", $value, $match)) {
                            break;
                        }
                        $match = array_flip($match[0]);
                        $carrierCodes = [];
                        foreach ($carrierList as $label => $code) {
                            if (!array_key_exists($label, $match)) {
                                continue;
                            }
                            $carrierCodes[] = $code;
                        }
                        $carrierCodes = implode(',', $carrierCodes);
                        $o->$name($carrierCodes);
                        break;
                    case 'imei':
                        if (!preg_match_all('#\d{15}#m', $value, $match)) {
                            break;
                        }
                        $value = implode(',', $match[0]);
                        $o->$name($value);
                        break;
                    case 'status':
                        if ('' === $value) {
                            $o->$name(Phone::STATUS_OTHER);
                            $o->statusDescription('没有注明');
                            break;
                        }
                        if (false !== strpos($value, '组内')) {
                            $o->$name(Phone::STATUS_IN_INVENTORY);
                            break;
                        }
                        if (false !== strpos($value, '坏')) {
                            $o->$name(Phone::STATUS_BROKEN);
                            break;
                        }
                        $user = User::getOne([
                            'deleted' => User::DELETED_NO,
                            'name' => $value,
                        ]);
                        if ($user) {
                            $o->$name(Phone::STATUS_RENT_OUT);
                            $o->userId($user->id());
                            $o->statusDescription($value);
                            unset($user);
                            break;
                        }
                        $o->$name(Phone::STATUS_OTHER);
                        $o->statusDescription($value);
                        break;
                    case 'ram':
                        if (preg_match('#^\d+$#', $value)) {
                            $o->$name($value);
                            break;
                        }
                        if (preg_match('#^(\d+)G$#i', $value, $match)) {
                            $o->$name(intval($match[1]) * 1024);
                            break;
                        }
                        break;
                    case 'label':
                        $o->$name(strtoupper($value));
                        break;
                    default:
                        $o->$name($value);
                }
            }
            unset($def);
            $o->save();
        }
        unset($row);
        $uploader = AppService::getUploader();
        $fileName = $uploader->saveFile($files, UploadFile::TYPE_PHONE_EXCEL);
        $o = new UploadFile();
        $o->type(UploadFile::TYPE_PHONE_EXCEL);
        $o->originName($files['name']);
        $o->fileName($fileName);
        $o->uploadByUser(App::getUser()->id());
        $o->save();
        return $response;
    }

    private function uploadSimCards(array &$files) : array
    {
        $response = [
            'result' => true,
        ];
        $excel = new MyExcel();
        $head = [
            'phoneNumber' => ['#手机号#u'],
            'label' => ['#标识#u'],
            'carrier' => ['#运营商#u'],
            'place' => ['#归属地#u'],
            'imsi' => ['#imsi#i'],
            'status' => ['#状态#u'],
        ];
        $excelResult = $excel->load($files['tmp_name'], $head);
        $response = array_merge($response, $excelResult);
        if (!$excelResult['result']) {
            return $response;
        }
        $carrierList = array_flip(SimCard::LABEL_CARRIER);
        foreach ($excelResult['content'] as &$row) {
            if ($row['phoneNumber']['value']) {
                $simCard = SimCard::getOne([
                    'phoneNumber' => $row['phoneNumber']['value'],
                    'deleted' => SimCard::DELETED_NO,
                ]);
                if ($simCard) {
                    unset($simCard);
                    continue;
                }
            }
            $o = new SimCard();
            foreach ($row as $name => &$def) {
                $value = $def['value'];
                if ('' === $value && 'status' !== $name) {
                    continue;
                }
                switch ($name) {
                    case 'phoneNumber':
                        if (!preg_match('#\d+#', $value, $match)) {
                            continue;
                        }
                        $o->$name($match[0]);
                        break;
                    case 'carrier':
                        if (!preg_match_all("#电信|移动|联通|虚拟运营商#u", $value, $match)) {
                            break;
                        }
                        $match = array_flip($match[0]);
                        $carrierCodes = [];
                        foreach ($carrierList as $label => $code) {
                            if (!array_key_exists($label, $match)) {
                                continue;
                            }
                            $carrierCodes[] = $code;
                        }
                        $carrierCodes = implode(',', $carrierCodes);
                        $o->$name($carrierCodes);
                        break;
                    case 'status':
                        if ('' === $value) {
                            $o->$name(SimCard::STATUS_OTHER);
                            $o->statusDescription('没有注明');
                            break;
                        }
                        if (false !== strpos($value, '组内')) {
                            $o->$name(SimCard::STATUS_IN_INVENTORY);
                            break;
                        }
                        if (preg_match('#暂停|注销#u', $value)) {
                            $o->$name(SimCard::STATUS_BROKEN);
                            break;
                        }
                        $user = User::getOne([
                            'deleted' => User::DELETED_NO,
                            'name' => $value,
                        ]);
                        if ($user) {
                            $o->$name(SimCard::STATUS_RENT_OUT);
                            $o->userId($user->id());
                            $o->statusDescription($value);
                            unset($user);
                            break;
                        }
                        $o->$name(SimCard::STATUS_OTHER);
                        $o->statusDescription($value);
                        break;
                    case 'label':
                        $o->$name(strtoupper($value));
                        break;
                    default:
                        $o->$name($value);
                }
            }
            unset($def);
            $o->save();
        }
        unset($row);
        $uploader = AppService::getUploader();
        $fileName = $uploader->saveFile($files, UploadFile::TYPE_SIMCARD_EXCEL);
        $o = new UploadFile();
        $o->type(UploadFile::TYPE_SIMCARD_EXCEL);
        $o->originName($files['name']);
        $o->fileName($fileName);
        $o->uploadByUser(App::getUser()->id());
        $o->save();
        return $response;
    }
}