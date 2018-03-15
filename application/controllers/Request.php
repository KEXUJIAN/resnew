<?php
/**
 * Created by PhpStorm.
 * User: KE
 * Date: 2018/1/10
 * Time: 23:36
 */

use Res\Model\Request as ReqModal;
use Res\Biz\RequestBiz;
use Res\Model\User;
use Res\Model\Phone;
use Res\Model\SimCard;

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
        $user = App::getUser();
        $uid = $user->id();
        $c = [];
        if (!empty($_POST['specificId'])) {
            $c['id'] = $_POST['specificId'];
        }

        if (User::ROLE_MANAGER !== $user->role()) {
            $c += [
                'fromUserId' => $uid,
                'toUserId' => $uid,
            ];
        }
        $where = $this->buildWhere($c);
        $table = ReqModal::TABLE;
        $limit = $_POST['length'] ?? '';
        $offset = $_POST['start'] ?? '';
        $limitStr = '';
        if (is_numeric($limit) && is_numeric($offset)) {
            $limitStr = "LIMIT {$limit} OFFSET {$offset}";
        } elseif (is_numeric($limit)) {
            $limitStr = "LIMIT {$limit}";
        }
        $countSql = "SELECT count(1) AS _count FROM {$table} WHERE {$where['string']}";
        $pdo = AppService::getPDO();
        $sth = $pdo->prepare($countSql);
        $sth->execute($where['array']);
        $ret = $sth->fetch();
        $sth->closeCursor();
        if ($ret) {
            $count += intval($ret['_count']);
        }
        $fields = [];
        foreach (ReqModal::COLUMNS as $column) {
            $fields[] = strtolower($column) . " AS {$column}";
        }
        $fields = implode(',', $fields);
        $selSql = "SELECT {$fields} FROM {$table} WHERE {$where['string']} ORDER BY id DESC {$limitStr}";
        $sth = $pdo->prepare($selSql);
        $sth->execute($where['array']);
        $columns = [];
        foreach ($_POST['columns'] as $columnDef) {
            $columns[] = $columnDef['data'];
        }
        $index = 1;
        $fields = array_flip(ReqModal::COLUMNS);
        while ($ret = $sth->fetch()) {
            $o = new ReqModal();
            $o->clone($ret);
            $row = [];
            foreach ($columns as $column) {
                $value = '';
                if ('id' === $column) {
                    $value .= '<label class="index-label" data-id="' . $o->$column() . '">' . ($index++ + $offset). '</label>';
                } elseif ('#action' === $column) {
                    $value .= RequestBiz::requestAction($o);
                } elseif (array_key_exists($column, $fields)) {
                    switch ($column) {
                        case 'fromUserId':
                            $user = User::get($o->$column());
                            $userLabel = "{$user->name()}[{$user->username()}]";
                            $url = "/user/info/{$user->id()}";
                            $value .= '<button data-toggle="modal" data-target="#ajax-modal" data-url="' . $url . '" class="btn btn-primary action-button">' . htmlspecialchars($userLabel) . '</button>';
                            break;
                        case 'toUserId':
                            $user = User::get($o->$column());
                            $userLabel = "{$user->name()}[{$user->username()}]";
                            $url = "/user/info/{$user->id()}";
                            $value .= '<button data-toggle="modal" data-target="#ajax-modal" data-url="' . $url . '" class="btn btn-default action-button">' . htmlspecialchars($userLabel) . '</button>';
                            break;
                        case 'assetId': {
                            $url = '';
                            $id = $o->$column();
                            $label = '';
                            switch ($o->assetType()) {
                                case ReqModal::ASSET_TYPE_PHONE:
                                    $label = Phone::get($id)->label();
                                    $url .= "/phone/info/{$id}";
                                    break;
                                case ReqModal::ASSET_TYPE_SIM_CARD:
                                    $label = SimCard::get($id)->label();
                                    $url .= "/simCard/info/{$id}";
                                    break;
                            }
                            if (!$label) {
                                $label = $id;
                            }
                            $value .= '<label data-toggle="modal" data-target="#ajax-modal" data-url="' . $url . '" class="label label-info" style="cursor: pointer;">' . $label . '</label>';
                            break;
                        }
                        case 'assetType':
                            $value .= ReqModal::LABEL_ASSET_TYPE[$o->$column()];
                            break;
                        case 'type':
                            $value .= ReqModal::LABEL_TYPE[$o->$column()];
                            break;
                        case 'status':
                            $value .= ReqModal::LABEL_STATUS[$o->$column()];
                            break;
                        case 'timeModified':
                            if (ReqModal::STATUS_NEW !== $o->status()) {
                                $value .= '<span class="long-data">' . htmlspecialchars($o->$column()) . '</span>';
                            } else {
                                $value .= '请求暂未被处理';
                            }
                            break;
                        case 'timeAdded':
                            $value .= '<span class="long-data">' . htmlspecialchars($o->$column()) . '</span>';
                            break;
                        default:
                            $value .= htmlspecialchars($o->$column());
                    }
                }
                $row[$column] = $value;
            }
            $data[] = $row;
        }
        $sth->closeCursor();
        if (!$data) {
            $response['result'] = false;
            $response['message'] = '没有记录';
            echo json_encode($response);
            return;
        }
        $response['data'] = $data;
        $response['recordsTotal'] = $response['recordsFiltered'] = $count;
        echo json_encode($response);
    }

    private function buildWhere(array &$c) : array
    {
        if (!$c) {
            return [
                'string' => '1 = 1',
                'array' => [],
            ];
        }
        $where = [
            'string' => '',
            'array'  => [],
        ];

        if (isset($c['id'])) {
            $where['string'] .= 'id = :id ';
            $where['array'][':id'] =  $c['id'];
        }

        if (isset($c['toUserId'], $c['fromUserId'])) {
            if ($where['string']) {
                $where['string'] .= 'AND ';
            }

            $where['string'] .= '(touserid = :toUserId OR fromuserid = :fromUserId) ';
            $tmp = [
                ':toUserId' => $c['toUserId'],
                ':fromUserId' => $c['fromUserId'],
            ];
            $where['array'] = array_merge($where['array'], $tmp);
            unset($c['id'], $c['toUserId'], $c['fromUserId']);
        }
        if ($c) {
            $tmp = ReqModal::buildWhere($c);
            $where['string'] .= "AND {$tmp['string']}";
            $where['array'] = array_merge($where['array'], $tmp['array']);
        }
        return $where;
    }

}