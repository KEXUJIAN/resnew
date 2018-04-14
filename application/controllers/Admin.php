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
use Res\Biz\AssetBiz;
use Res\Model\Request;
use Res\Biz\RequestBiz;
use Res\Model\Notification;

class Admin extends CI_Controller
{
    public function console()
    {
        App::view('admin/console', ['titleNavClass' => 'container-fluid']);
    }

    public function new($name)
    {
        switch ($name) {
            case 'user':
                App::view('admin/user-new');
                break;
            case 'phone':
                App::view('admin/phone-new');
                break;
            case 'simcard':
                App::view('admin/simcard-new');
                break;
            default:
        }
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

    public function save($name)
    {
        $response = [];
        switch ($name) {
            case 'user':
                $response += $this->saveUser();
                break;
            case 'phone':
                $response += $this->savePhone();
                break;
            case 'simcard':
                $response += $this->saveSimCard();
                break;
            default:
                $response += [
                    'result' => false,
                ];
        }
        echo json_encode($response);
    }

    public function update($name, $id)
    {
        $response = [];
        switch ($name) {
            case 'user':
                $response += $this->updateUser($id);
                break;
            case 'phone':
                $response += $this->updatePhone($id);
                break;
            case 'simcard':
                $response += $this->updateSimCard($id);
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
        $files    = $_FILES['files'] ?? [];
        if (!$files) {
            $response['result'] = 'false';
            echo json_encode($response);
            return;
        }
        $uploader = AppService::getUploader();
        $error    = $uploader->check($files);
        if ($error) {
            $response['result'] = false;
            $response           += $error;
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
        header('content-type: application/json');
        echo json_encode($response);
    }

    public function edit($name, $id)
    {
        switch ($name) {
            case 'user':
                $o = User::get($id);
                if (!$o || USer::DELETED_YES === $o->deleted()) {
                    show_error('用户不存在', 500, '');
                }
                App::view('admin/user-edit', ['user' => $o]);
                break;
            case 'phone':
                $o = Phone::get($id);
                if (!$o || Phone::DELETED_YES === $o->deleted()) {
                    show_error('测试机不存在', 500, '');
                }
                App::view('admin/phone-edit', ['phone' => $o]);
                break;
            case 'simcard':
                $o = SimCard::get($id);
                if (!$o || SimCard::DELETED_YES === $o->deleted()) {
                    show_error('测试卡不存在', 500, '');
                }
                App::view('admin/simcard-edit', ['simCard' => $o]);
                break;
            default:
        }
    }

    public function delete($name)
    {
        $required = [
            'postIds' => ['type' => 'array', 'name' => '要删除的项'],
        ];
        $error    = App::checkRequired($required, $_POST);
        if ($error) {
            echo json_encode($error);
            return;
        }
        $ids = $_POST['postIds'];
        $nr  = 0;
        switch ($name) {
            case 'user':
                $nr += User::hidden($ids);
                break;
            case 'phone':
                $nr += Phone::hidden($ids);
                break;
            case 'simcard':
                $nr += SimCard::hidden($ids);
                break;
        }

        echo json_encode([
            'result'  => true,
            'message' => "成功删除 {$nr} 项",
        ]);
    }

    public function accept($rid)
    {
        $request = Request::get($rid);

        if (!$request || Request::DELETED_YES === $request->deleted()) {
            echo json_encode([
                'result'  => false,
                'message' => '请求不存在',
            ]);
        }
        $o = RequestBiz::assetFactory($request->assetType(), $request->assetId());
        if (!$o) {
            echo json_encode([
                'result'  => false,
                'message' => '此资产不存在',
            ]);
        }

        $user = User::get($request->fromUserId());

        $now = date('Y-m-d H:i:s');
        switch ($request->type()) {
            case Request::TYPE_RENT_OUT:
                $o->userId($user->id());
                $o->status($o->getClass()::STATUS_RENT_OUT);
                $o->statusDescription("目前属于{$user->name()}[{$user->username()}]");
                break;
            case Request::TYPE_RETURN:
                $o->userId(null);
                $o->status($o->getClass()::STATUS_IN_INVENTORY);
                $o->statusDescription('');
        }
        $o->timeModified($now);
        $o->save();

        $request->timeModified($now);
        $request->status(Request::STATUS_DONE);
        $request->save();

        $placeholder = '[:rUrl]';
        $rUrl        = implode('/', ['user', 'profile', 'request', $request->id()]);
        $content     = "管理员通过了你的申请\r\n";
        $content     .= "请求细节查看链接: <a href=\"{$placeholder}\">{$placeholder}</a>";

        $notification = new Notification();
        $notification->userId($user->id());
        $notification->message(str_replace($placeholder, "/{$rUrl}", $content));
        $notification->save();

        if ($user->email()) {
            @AppService::getEmail()->send('申请通过', str_replace($placeholder, "/{$rUrl}", $content), $user->email());
        }

        echo json_encode([
            'result'  => true,
            'message' => '操作成功',
        ]);
    }

    public function reject($rid)
    {
        $request = Request::get($rid);

        if (!$request || Request::DELETED_YES === $request->deleted()) {
            echo json_encode([
                'result'  => false,
                'message' => '请求不存在',
            ]);
        }
        $o = RequestBiz::assetFactory($request->assetType(), $request->assetId());
        if (!$o) {
            echo json_encode([
                'result'  => false,
                'message' => '此资产不存在',
            ]);
        }

        $user = User::get($request->fromUserId());

        $now = date('Y-m-d H:i:s');
        switch ($request->type()) {
            case Request::TYPE_RENT_OUT:
                $o->userId(null);
                $o->status($o->getClass()::STATUS_IN_INVENTORY);
                $o->statusDescription('');
                break;
            case Request::TYPE_RETURN:
                $o->status($o->getClass()::STATUS_RENT_OUT);
                $o->statusDescription("目前属于{$user->name()}[{$user->username()}]");
        }
        $o->save();

        $request->timeModified($now);
        $request->status(Request::STATUS_REJECT);
        $request->save();

        $placeholder = '[:rUrl]';
        $rUrl        = implode('/', ['user', 'profile', 'request', $request->id()]);
        $content     = "管理员驳回了你的申请\r\n";
        $content     .= "请求细节查看链接: <a href=\"{$placeholder}\">{$placeholder}</a>";

        $notification = new Notification();
        $notification->userId($user->id());
        $notification->message(str_replace($placeholder, "/{$rUrl}", $content));
        $notification->save();

        if ($user->email()) {
            @AppService::getEmail()->send('申请被驳回', str_replace($placeholder, "/{$rUrl}", $content), $user->email());
        }

        echo json_encode([
            'result'  => true,
            'message' => '操作成功',
        ]);
    }

    private function dataUsers(): array
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
            return $response;
        }
        $response['draw'] = $_POST['draw'];
        $c                = ['deleted' => User::DELETED_NO];
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
            $c['timeAdded>='] = date('Y-m-d 00:00:00', strtotime($_POST['timeAddedMin']));
        }
        if ('' !== ($_POST['timeAddedMax'] ?? '')) {
            $c['timeAdded<='] = date('Y-m-d 23:59:59', strtotime($_POST['timeAddedMax']));
        }
        $count   = User::getCount($c);
        $columns = [];
        foreach ($_POST['columns'] as $columnDef) {
            $columns[] = $columnDef['data'];
        }
        $order = [];
        if ($_POST['order'] ?? []) {
            foreach ($_POST['order'] as $orderDef) {
                $key         = $columns[$orderDef['column']];
                $order[$key] = 'desc' === $orderDef['dir'] ? 'desc' : 'asc';
            }
        }
        $limit    = $_POST['length'];
        $offset   = $_POST['start'];
        $userList = User::getList($c, $order, $limit, $offset);
        if (!$userList) {
            $response['result']  = false;
            $response['message'] = '没有记录';
            return $response;
        }
        $data   = [];
        $index  = 1;
        $fields = User::COLUMNS;
        $fields = array_flip($fields);
        foreach ($userList as $user) {
            $row = [];
            foreach ($columns as $column) {
                $value = '';
                if ('id' === $column) {
                    $value .= '<span class="checkbox"><label><input value="' . $user->$column() . '" type="checkbox"> ' . ($index++ + $offset) . '</label></span>';
                } elseif ('#action' === $column) {
                    $value .= '<button data-toggle="modal" data-target="#ajax-modal" data-url="/admin/edit/user/' . $user->id() . '" class="btn btn-info" style="padding-bottom: 0;padding-top: 0;"><i class="fa fa-edit"></i> 编辑</button>';
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
        $response['data']         = $data;
        $response['recordsTotal'] = $response['recordsFiltered'] = $count;
        return $response;
    }

    private function dataPhones(): array
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
            return $response;
        }
        $response['draw'] = $_POST['draw'];
        $c                = ['deleted' => Phone::DELETED_NO];
        AssetBiz::phoneCondition($c, $_POST);

        $count   = Phone::getCount($c);
        $columns = [];
        foreach ($_POST['columns'] as $columnDef) {
            $columns[] = $columnDef['data'];
        }
        $order = [];
        if ($_POST['order'] ?? []) {
            foreach ($_POST['order'] as $orderDef) {
                $key         = $columns[$orderDef['column']];
                $order[$key] = 'desc' === $orderDef['dir'] ? 'desc' : 'asc';
            }
        }
        $limit     = $_POST['length'];
        $offset    = $_POST['start'];
        $phoneList = Phone::getList($c, $order, $limit, $offset);
        if (!$phoneList) {
            $response['result']  = false;
            $response['message'] = '没有记录';
            return $response;
        }
        $data   = [];
        $index  = 1;
        $fields = Phone::COLUMNS;
        $fields = array_flip($fields);
        foreach ($phoneList as $phone) {
            $row = [];
            foreach ($columns as $column) {
                $value = '';
                if ('id' === $column) {
                    $value .= '<span class="checkbox"><label><input value="' . $phone->$column() . '" type="checkbox"> ' . ($index++ + $offset) . '</label></span>';
                } elseif ('#action' === $column) {
                    $value .= '<button data-toggle="modal" data-target="#ajax-modal" data-url="/admin/edit/phone/' . $phone->id() . '" class="btn btn-info" style="padding-bottom: 0;padding-top: 0;"><i class="fa fa-edit"></i> 编辑</button>';
                } elseif (array_key_exists($column, $fields)) {
                    switch ($column) {
                        case 'status':
                            $value .= AssetBiz::phoneStatus($phone);
                            break;
                        case 'carrier':
                            if (!$phone->$column() && '0' !== $phone->$column()) {
                                break;
                            }
                            $carrierList = explode(',', $phone->$column());
                            $labels      = [];
                            foreach ($carrierList as $carrierCode) {
                                $labels[] = Phone::LABEL_CARRIER[$carrierCode];
                            }
                            $value .= '<span>' . implode(',', $labels) . '</span>';
                            break;
                        case 'type':
                        case 'os':
                        case 'imei':
                        case 'remark':
                            $value .= '<span class="long-data">' . htmlspecialchars($phone->$column()) . '</span>';
                            break;
                        default:
                            $value .= '<span>' . htmlspecialchars($phone->$column()) . '</span>';
                            break;
                    }
                }
                $row[$column] = $value;
            }
            $data[] = $row;
        }
        $response['data']         = $data;
        $response['recordsTotal'] = $response['recordsFiltered'] = $count;
        return $response;
    }

    private function dataSimCards(): array
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
            return $response;
        }
        $response['draw'] = $_POST['draw'];
        $c                = ['deleted' => SimCard::DELETED_NO];
        AssetBiz::simCardCondition($c, $_POST);

        $count   = SimCard::getCount($c);
        $columns = [];
        foreach ($_POST['columns'] as $columnDef) {
            $columns[] = $columnDef['data'];
        }
        $order = [];
        if ($_POST['order'] ?? []) {
            foreach ($_POST['order'] as $orderDef) {
                $key         = $columns[$orderDef['column']];
                $order[$key] = 'desc' === $orderDef['dir'] ? 'desc' : 'asc';
            }
        }
        $limit       = $_POST['length'];
        $offset      = $_POST['start'];
        $simCardList = SimCard::getList($c, $order, $limit, $offset);
        if (!$simCardList) {
            $response['result']  = false;
            $response['message'] = '没有记录';
            return $response;
        }
        $data   = [];
        $index  = 1;
        $fields = SimCard::COLUMNS;
        $fields = array_flip($fields);
        foreach ($simCardList as $simCard) {
            $row = [];
            foreach ($columns as $column) {
                $value = '';
                if ('id' === $column) {
                    $value .= '<span class="checkbox"><label><input value="' . $simCard->$column() . '" type="checkbox"> ' . ($index++ + $offset) . '</label></span>';
                } elseif ('#action' === $column) {
                    $value .= '<button data-toggle="modal" data-target="#ajax-modal" data-url="/admin/edit/simcard/' . $simCard->id() . '" class="btn btn-info" style="padding-bottom: 0;padding-top: 0;"><i class="fa fa-edit"></i> 编辑</button>';
                } elseif (array_key_exists($column, $fields)) {
                    switch ($column) {
                        case 'status':
                            $value .= AssetBiz::simCardStatus($simCard);
                            break;
                        case 'carrier':
                            if (!$simCard->$column() && '0' !== $simCard->$column()) {
                                break;
                            }
                            $carrierList = explode(',', $simCard->carrier());
                            $labels      = [];
                            foreach ($carrierList as $carrierCode) {
                                $labels[] = SimCard::LABEL_CARRIER[$carrierCode];
                            }
                            $value .= '<span>' . implode(',', $labels) . '</span>';
                            break;
                        case 'imsi':
                            $value .= '<span class="long-data">' . htmlspecialchars($simCard->$column()) . '</span>';
                            break;
                        case 'place':
                            $value .= htmlspecialchars($simCard->$column());
                            break;
                        default:
                            $value .= '<span>' . htmlspecialchars($simCard->$column()) . '</span>';
                            break;
                    }
                }
                $row[$column] = $value;
            }
            $data[] = $row;
        }
        $response['data']         = $data;
        $response['recordsTotal'] = $response['recordsFiltered'] = $count;
        return $response;
    }

    private function saveUser()
    {
        $response = [
            'result'  => true,
            'message' => '',
        ];
        $required = [
            'name'     => ['type' => 'str', 'name' => '姓名'],
            'username' => ['type' => 'str', 'name' => '用户名'],
            'password' => ['type' => 'str', 'name' => '密码'],
            'email'    => ['type' => 'str', 'name' => '邮箱'],
        ];
        $error    = App::checkRequired($required, $_POST);
        if ($error) {
            return array_merge($response, $error);
        }

        $username = $_POST['username'];
        $o        = User::getOne([
            'deleted'  => User::DELETED_NO,
            'username' => $username,
        ]);
        if ($o) {
            $response['result']  = false;
            $response['message'] = '用户名已存在';
            return $response;
        }
        $o = new User();
        $o->username($username);
        $o->name($_POST['name']);
        $o->password(self::encryptPassword($_POST['password']));
        $o->email($_POST['email']);
        $saved               = $o->save();
        $response['message'] = $saved ? '保存成功' : '未保存';

        $this->opLog('新建用户', $o->obj2Array(), $response['message']);
        return $response;
    }

    private function savePhone()
    {
        $response = [
            'result'  => true,
            'message' => '',
        ];
        $required = [
            'type'    => ['type' => 'str', 'name' => '机型'],
            'os'      => ['type' => 'str', 'name' => '系统'],
            'carrier' => ['type' => 'array', 'name' => '运营商'],
            'status'  => ['type' => 'str', 'name' => '状态'],
            'label'   => ['type' => 'str', 'name' => '标签'],
        ];
        $error    = App::checkRequired($required, $_POST);
        if ($error) {
            return array_merge($response, $error);
        }

        $label = $_POST['label'];
        $o     = Phone::getOne([
            'deleted' => User::DELETED_NO,
            'label'   => $label,
        ]);
        if ($o) {
            $response['result']  = false;
            $response['message'] = '同标识的测试机已经存在';
            return $response;
        }
        $status = $_POST['status'];
        if (!array_key_exists($status, Phone::LABEL_STATUS)) {
            $response['result']  = false;
            $response['message'] = '非法的状态值';
            return $response;
        }
        $userId = $_POST['userId'] ?? null;
        if ($status === Phone::STATUS_RENT_OUT && !$userId) {
            $response['result']  = false;
            $response['message'] = '已借出的测试机需要选择借出人';
            return $response;
        }
        if ($status !== Phone::STATUS_RENT_OUT && $userId) {
            $response['result']  = false;
            $response['message'] = '未借出的测试机不需要选择借出人';
            return $response;
        }
        $carrier     = $_POST['carrier'];
        $carrier     = implode(',', $carrier);
        $ram         = is_numeric($_POST['ram'] ?? null) ? $_POST['ram'] : null;
        $screenSize  = $_POST['screenSize'] ?? null;
        $resolutionW = $_POST['resolutionW'] ?? '';
        $resolutionH = $_POST['resolutionH'] ?? '';
        $imei        = $_POST['imei'] ?? '';
        if (preg_match_all('#\d{15}#', $imei, $match)) {
            $imei = implode(',', $match[0]);
        }
        $imei              = $imei ?: null;
        $type              = $_POST['type'] ?? null;
        $os                = $_POST['os'];
        $statusDescription = trim($_POST['statusDescription'] ?? '') ?: null;
        $remark            = trim($_POST['remark'] ?? '') ?: null;

        $o = new Phone();
        if ($resolutionH && $resolutionW) {
            $o->resolution("{$resolutionW} X {$resolutionH}");
        }
        $o->type($type);
        $o->label($label);
        $o->os($os);
        $o->screenSize($screenSize);
        $o->status($status);
        $o->ram($ram);
        $o->carrier($carrier);
        $o->imei($imei);
        $o->userId($userId);
        $o->statusDescription($statusDescription);
        $o->remark($remark);

        $saved               = $o->save();
        $response['message'] = $saved ? '保存成功' : '未保存';

        $this->opLog('新建测试机', $o->obj2Array(), $response['message']);
        return $response;
    }

    private function saveSimCard()
    {
        $response = [
            'result'  => true,
            'message' => '',
        ];
        $required = [
            'phoneNumber' => ['type' => 'str', 'name' => '手机号'],
            'label'       => ['type' => 'str', 'name' => '标识'],
            'carrier'     => ['type' => 'array', 'name' => '运营商'],
            'status'      => ['type' => 'str', 'name' => '状态'],
        ];
        $error    = App::checkRequired($required, $_POST);
        if ($error) {
            return array_merge($response, $error);
        }

        $phoneNumber = $_POST['phoneNumber'];
        $o           = SimCard::getOne([
            'deleted'     => User::DELETED_NO,
            'phoneNumber' => $phoneNumber,
        ]);
        if ($o) {
            $response['result']  = false;
            $response['message'] = '同号码的测试卡已经存在';
            return $response;
        }
        $status = $_POST['status'];
        if (!array_key_exists($status, SimCard::LABEL_STATUS)) {
            $response['result']  = false;
            $response['message'] = '非法的状态值';
            return $response;
        }
        $userId = $_POST['userId'] ?? null;
        if ($status === SimCard::STATUS_RENT_OUT && !$userId) {
            $response['result']  = false;
            $response['message'] = '已借出的测试卡需要选择借出人';
            return $response;
        }
        if ($status !== SimCard::STATUS_RENT_OUT && $userId) {
            $response['result']  = false;
            $response['message'] = '未借出的测试卡不需要选择借出人';
            return $response;
        }
        $carrier           = $_POST['carrier'];
        $carrier           = implode(',', $carrier);
        $label             = $_POST['label'];
        $place             = $_POST['place'] ?? null;
        $imsi              = $_POST['imsi'] ?? null;
        $statusDescription = trim($_POST['statusDescription'] ?? '') ?: null;
        $idCard            = trim($_POST['idCard'] ?? '') ?: null;
        $servicePassword   = trim($_POST['servicePassword'] ?? '') ?: null;
        $remark            = trim($_POST['remark'] ?? '') ?: null;

        $o = new SimCard();
        $o->phoneNumber($phoneNumber);
        $o->label($label);
        $o->status($status);
        $o->carrier($carrier);
        $o->imsi($imsi);
        $o->userId($userId);
        $o->place($place);
        $o->statusDescription($statusDescription);
        $o->idCard($idCard);
        $o->servicePassword($servicePassword);
        $o->remark($remark);

        $saved               = $o->save();
        $response['message'] = $saved ? '保存成功' : '未保存';

        $this->opLog('新建测试卡', $o->obj2Array(), $response['message']);
        return $response;
    }

    private function uploadUsers(array &$files): array
    {
        $response    = [
            'result' => true,
        ];
        $excel       = new MyExcel();
        $head        = [
            'name'     => ['#姓名#u'],
            'username' => ['#用户名#u'],
            'email'    => ['#邮箱#u'],
        ];
        $excelResult = $excel->load($files['tmp_name'], $head);
        $response    = array_merge($response, $excelResult);
        if (!$excelResult['result']) {
            return $response;
        }

        foreach ($excelResult['content'] as &$row) {
            if ($row['username']['value']) {
                $user = User::getOne([
                    'username' => $row['username']['value'],
                    'deleted'  => User::DELETED_NO,
                ]);
                if ($user) {
                    unset($user);
                    continue;
                }
            } else {
                continue;
            }
            $o = new User();
            foreach ($row as $name => &$def) {
                $value = $def['value'];
                if ('' === $value) {
                    continue;
                }
                $o->$name($value);
            }
            unset($def);
            $o->password(self::encryptPassword('123456'));
            $o->save();
        }
        unset($row);
        $uploader = AppService::getUploader();
        $fileName = $uploader->saveFile($files, UploadFile::TYPE_USER_EXCEL);
        $o        = new UploadFile();
        $o->type(UploadFile::TYPE_USER_EXCEL);
        $o->originName($files['name']);
        $o->fileName($fileName);
        $o->uploadByUser(App::getUser()->id());
        $o->save();
        return $response;
    }

    private function uploadPhones(array &$files): array
    {
        $response    = [
            'result' => true,
        ];
        $excel       = new MyExcel();
        $head        = [
            'type'       => ['#机型#u'],
            'os'         => ['#系统#u'],
            'resolution' => ['#分辨率#u'],
            'ram'        => ['#ram#i'],
            'carrier'    => ['#运营商#u'],
            'screenSize' => ['#屏幕尺寸#u'],
            'label'      => ['#编号#u'],
            'imei'       => ['#imei#i'],
            'status'     => ['#状态#u'],
            'remark'     => ['#备注#u'],
        ];
        $excelResult = $excel->load($files['tmp_name'], $head);
        $response    = array_merge($response, $excelResult);
        if (!$excelResult['result']) {
            return $response;
        }
        $carrierList = array_flip(Phone::LABEL_CARRIER);
        foreach ($excelResult['content'] as &$row) {
            if ($row['label']['value']) {
                $phone = Phone::getOne([
                    'label'   => $row['label']['value'],
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
                        $match        = array_flip($match[0]);
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
                        if ('' === $value || false !== strpos($value, '组内')) {
                            $o->$name(Phone::STATUS_IN_INVENTORY);
                            break;
                        }
                        if (preg_match('#坏|报废#u', $value)) {
                            $o->$name(Phone::STATUS_BROKEN);
                            break;
                        }
                        $user = User::getOne([
                            'deleted' => User::DELETED_NO,
                            'name'    => $value,
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
        $o        = new UploadFile();
        $o->type(UploadFile::TYPE_PHONE_EXCEL);
        $o->originName($files['name']);
        $o->fileName($fileName);
        $o->uploadByUser(App::getUser()->id());
        $o->save();
        return $response;
    }

    private function uploadSimCards(array &$files): array
    {
        $response    = [
            'result' => true,
        ];
        $excel       = new MyExcel();
        $head        = [
            'phoneNumber'     => ['#手机号#u'],
            'label'           => ['#标识#u'],
            'carrier'         => ['#运营商#u'],
            'place'           => ['#归属地#u'],
            'imsi'            => ['#imsi#i'],
            'status'          => ['#状态#u'],
            'idCard'          => ['#身份证#u'],
            'servicePassword' => ['#服务密码#u'],
            'remark'          => ['#备注#u'],
        ];
        $excelResult = $excel->load($files['tmp_name'], $head);
        $response    = array_merge($response, $excelResult);
        if (!$excelResult['result']) {
            return $response;
        }
        $carrierList = array_flip(SimCard::LABEL_CARRIER);
        foreach ($excelResult['content'] as &$row) {
            if ($row['phoneNumber']['value']) {
                $simCard = SimCard::getOne([
                    'phoneNumber' => $row['phoneNumber']['value'],
                    'deleted'     => SimCard::DELETED_NO,
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
                        $match        = array_flip($match[0]);
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
                        if ('' === $value || false !== strpos($value, '组内')) {
                            $o->$name(SimCard::STATUS_IN_INVENTORY);
                            break;
                        }
                        if (preg_match('#暂停|注销|报废#u', $value)) {
                            $o->$name(SimCard::STATUS_BROKEN);
                            break;
                        }
                        $user = User::getOne([
                            'deleted' => User::DELETED_NO,
                            'name'    => $value,
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
        $o        = new UploadFile();
        $o->type(UploadFile::TYPE_SIMCARD_EXCEL);
        $o->originName($files['name']);
        $o->fileName($fileName);
        $o->uploadByUser(App::getUser()->id());
        $o->save();
        return $response;
    }

    private function updateUser($id)
    {
        $response = [
            'result'  => true,
            'message' => '',
        ];
        $o        = User::get($id);
        if (!$o || User::DELETED_YES === $o->deleted()) {
            return array_merge($response, [
                'result'  => false,
                'message' => '用户不存在',
            ]);
        }
        $oldData = $o->obj2Array();
        if ('' !== trim($_POST['name'] ?? '')) {
            $o->name($_POST['name']);
        }
        if ('' !== trim($_POST['password'] ?? '')) {
            $o->password(self::encryptPassword($_POST['password']));
        }
        if ('' !== trim($_POST['email'] ?? '')) {
            $o->email($_POST['email']);
        }
        $newData = $o->obj2Array();
        if ($oldData !== $newData) {
            $o->timeModified(date('Y-m-d H:i:s'));
        }

        $changed             = $o->save();
        $response['message'] = $changed ? '成功保存' : '没有更改';

        $this->opLog('修改用户', $o->obj2Array(), $response['message']);
        return $response;
    }

    private function updatePhone($id)
    {
        $response = [
            'result'  => true,
            'message' => '',
        ];
        $o        = Phone::get($id);
        if (!$o || Phone::DELETED_YES === $o->deleted()) {
            return array_merge($response, [
                'result'  => false,
                'message' => '测试机不存在',
            ]);
        }
        $oldData = $o->obj2Array();
        if ('' !== trim($_POST['type'] ?? '')) {
            $o->type($_POST['type']);
        }
        if ('' !== trim($_POST['os'] ?? '')) {
            $o->os($_POST['os']);
        }
        if ('' !== trim($_POST['resolutionW'] ?? '') && ('' !== trim($_POST['resolutionH'] ?? ''))) {
            $o->resolution("{$_POST['resolutionW']} X {$_POST['resolutionH']}");
        }
        if ('' !== trim($_POST['ram'] ?? '')) {
            $o->ram(intval($_POST['ram']));
        }
        if ('' !== trim($_POST['screenSize'] ?? '')) {
            $o->screenSize($_POST['screenSize']);
        }
        $label = trim($_POST['label'] ?? '');
        if ($o->label() !== $label && '' !== $label) {
            $old = Phone::getOne([
                'label'   => $_POST['label'],
                'deleted' => Phone::DELETED_NO,
            ]);
            if ($old) {
                return array_merge($response, [
                    'result'  => false,
                    'message' => '同标识的测试机已存在',
                ]);
            }
            $o->label($label);
        }
        if ($_POST['carrier']) {
            $o->carrier(implode(',', $_POST['carrier']));
        }
        if ('' !== trim($_POST['status'] ?? '')) {
            $o->status(intval($_POST['status']));
        }
        if ('' !== trim($_POST['userId'] ?? '')) {
            $o->userId($_POST['userId']);
        }
        if ('' !== trim($_POST['imei'] ?? '')) {
            $o->imei($_POST['imei']);
        }

        $o->statusDescription($_POST['statusDescription']);
        $o->remark($_POST['remark']);

        $newData = $o->obj2Array();
        if ($oldData !== $newData) {
            $o->timeModified(date('Y-m-d H:i:s'));
        }

        $changed             = $o->save();
        $response['message'] = $changed ? '成功保存' : '没有更改';

        $this->opLog('修改测试机', $o->obj2Array(), $response['message']);
        return $response;
    }

    private function updateSimCard($id)
    {
        $response = [
            'result'  => true,
            'message' => '',
        ];
        $o        = SimCard::get($id);
        if (!$o || SimCard::DELETED_YES === $o->deleted()) {
            return array_merge($response, [
                'result'  => false,
                'message' => '测试卡不存在',
            ]);
        }
        $oldData = $o->obj2Array();
        if ('' !== trim($_POST['place'] ?? '')) {
            $o->place($_POST['place']);
        }
        if ('' !== trim($_POST['phoneNumber'] ?? '')) {
            $o->phoneNumber($_POST['phoneNumber']);
        }
        if ($_POST['carrier']) {
            $o->carrier(implode(',', $_POST['carrier']));
        }
        if ('' !== trim($_POST['status'] ?? '')) {
            $o->status(intval($_POST['status']));
        }
        if ('' !== trim($_POST['userId'] ?? '')) {
            $o->userId($_POST['userId']);
        }
        if ('' !== trim($_POST['imsi'] ?? '')) {
            $o->imsi($_POST['imsi']);
        }
        if ('' !== trim($_POST['label'] ?? '')) {
            $o->label($_POST['label']);
        }
        $o->statusDescription($_POST['statusDescription'] ?? null);
        $o->remark($_POST['remark'] ?? null);

        if ('' !== trim($_POST['idCard'] ?? '')) {
            $o->idCard($_POST['idCard']);
        }
        if ('' !== trim($_POST['servicePassword'] ?? '')) {
            $o->servicePassword($_POST['servicePassword']);
        }
        $newData = $o->obj2Array();
        if ($oldData !== $newData) {
            $o->timeModified(date('Y-m-d H:i:s'));
        }

        $changed             = $o->save();
        $response['message'] = $changed ? '成功保存' : '没有更改';

        $this->opLog('修改测试卡', $o->obj2Array(), $response['message']);
        return $response;
    }

    public static function encryptPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 9]);
    }

    private function opLog(string $operate, array $data, string $result)
    {
        $admin   = App::getUser();
        $message = "用户: {$admin->id()} => [{$admin->name()}, {$admin->username()}]\n";
        $message .= "操作: {$operate}\n";
        $message .= "数据: " . json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n";
        $message .= "结果: {$result}\n";
        log_message('error', $message);
    }
}