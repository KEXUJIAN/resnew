<?php
/**
 * Created by PhpStorm.
 * User: KE
 * Date: 2018/1/4
 * Time: 20:09
 */

use Res\Model\SimCard as SimCardModal;
use Res\Model\Request;
use Res\Model\User;
use Res\Model\Notification;

class SimCard extends CI_Controller
{
    const ERROR_NO_ERROR = 0;
    const ERROR_NO_RECORD = 1;
    const ERROR_WRONG_STATUS = 2;
    const ERROR_MESSAGE = [
        0 => '请求成功',
        1 => '请求的测试卡不存在',
        2 => '测试卡状态错误',
    ];

    public function info($id)
    {
        $simCard = SimCardModal::get($id);
        if (!$simCard) {
            show_error(self::ERROR_MESSAGE[self::ERROR_NO_RECORD], 500, '找不到测试卡');
        }
        App::view('asset/simcard-info', ['simCard' => $simCard]);
    }

    public function rent($id)
    {
        $response = [
            'result' => true,
        ];
        $pdo = AppService::getPDO();
        $pdo->beginTransaction();
        try {
            $admin = User::getOne([
                'deleted' => User::DELETED_NO,
                'role' => User::ROLE_MANAGER,
            ]);
            $user = App::getUser();
            $simCard = SimCardModal::get($id, true);
            if (!$simCard || $simCard->deleted() === SimCardModal::DELETED_YES) {
                throw new Exception(self::ERROR_MESSAGE[self::ERROR_NO_RECORD], self::ERROR_NO_RECORD);
            }
            if ($simCard->status() !== SimCardModal::STATUS_IN_INVENTORY) {
                throw new Exception(self::ERROR_MESSAGE[self::ERROR_WRONG_STATUS] . ', 不能借出', self::ERROR_WRONG_STATUS);
            }
            $request = new Request();

            $simCard->status(SimCardModal::STATUS_RENTING);
            $simCard->userId($user->id());
            $simCard->statusDescription("{$user->name()} [{$user->username()}] 申请借用该测试卡");
            $simCard->timeModified($request->timeAdded());
            $simCard->save();

            $request->type(Request::TYPE_RENT_OUT);
            $request->fromUserId($user->id());
            $request->toUserId($admin->id());
            $request->assetId($id);
            $request->assetType(Request::ASSET_TYPE_SIM_CARD);
            $request->status(Request::STATUS_NEW);
            $request->save();

            $label = htmlspecialchars($simCard->label());
            $content = "测试卡申请借出\r\n测试卡[标志]: {$label}\r\n申请借用人: {$user->name()}[{$user->username()}]\r\n";
            $rUrl = implode('/', ['user', 'profile', 'request', $request->id()]);
            $placeholder = '[:rUrl]';
            $content .= "请求细节查看链接: <a href=\"{$placeholder}\">{$placeholder}</a>";

            @AppService::getEmail()->send('测试卡借出申请', str_replace($placeholder, site_url($rUrl), $content));
            $notify = new Notification();
            $notify->userId($admin->id());
            $notify->message(str_replace($placeholder, "/{$rUrl}", $content));
            $notify->save();

            $pdo->commit();
            $response['message'] = self::ERROR_MESSAGE[self::ERROR_NO_ERROR];
            $response['code'] = self::ERROR_NO_ERROR;
        } catch (Throwable $t) {
            $pdo->rollBack();
            $message = $t->getMessage();
            $code = $t->getCode();
            $response['result'] = false;
            $response['message'] = $message;
            $response['code'] = $code;
        }
        echo json_encode($response);
    }

    public function restore($id)
    {
        $response = [
            'result' => true,
        ];
        $pdo = AppService::getPDO();
        $pdo->beginTransaction();
        try {
            $admin = User::getOne([
                'deleted' => User::DELETED_NO,
                'role' => User::ROLE_MANAGER,
            ]);
            $user = App::getUser();
            $simCard = SimCardModal::get($id, true);
            if (!$simCard || $simCard->deleted() === SimCardModal::DELETED_YES) {
                throw new Exception(self::ERROR_MESSAGE[self::ERROR_NO_RECORD], self::ERROR_NO_RECORD);
            }
            if ($simCard->status() !== SimCardModal::STATUS_RENT_OUT || $simCard->userId() !== $user->id()) {
                throw new Exception(self::ERROR_MESSAGE[self::ERROR_WRONG_STATUS] . ', 无法归还', self::ERROR_WRONG_STATUS);
            }
            $request = Request::getOne([
                'assetId' => $id,
                'deleted' => Request::DELETED_NO,
                'assetType' => Request::ASSET_TYPE_SIM_CARD,
                'type' => Request::TYPE_TRANSFER,
                'status' => Request::STATUS_NEW,
            ]);
            if ($request) {
                $request->status(Request::STATUS_REJECT);
                $request->timeModified(date('Y-m-d H:i:s'));
                $request->save();
                $fromUser = User::get($request->fromUserId());
                $label = htmlspecialchars($simCard->label());
                $content = "您的测试卡转借请求被拒绝, 原因: 测试卡被归还\r\n测试卡[标志]: {$label}\r\n";
                $content .= "原借出人: {$user->name()}[{$user->username()}]\r\n";
                $rUrl = implode('/', ['user', 'profile', 'request', $request->id()]);
                $placeholder = '[:rUrl]';
                $content .= "请求细节查看链接: <a href=\"{$placeholder}\">{$placeholder}</a>";

                $notify = new Notification();
                $notify->userId($fromUser->id());
                $notify->message(str_replace($placeholder, "/{$rUrl}", $content));
                $notify->save();
                if ($fromUser->email()) {
                    @AppService::getEmail()->send('测试卡转借|拒绝', str_replace($placeholder, site_url($rUrl), $content), $fromUser->email());
                }
                unset($request);
            }
            $request = new Request();

            $simCard->status(SimCardModal::STATUS_RETURNING);
            $simCard->statusDescription("{$user->name()} [{$user->username()}] 申请归还该测试卡");
            $simCard->timeModified($request->timeAdded());
            $simCard->save();

            $request->type(Request::TYPE_RETURN);
            $request->fromUserId($user->id());
            $request->toUserId($admin->id());
            $request->assetId($id);
            $request->assetType(Request::ASSET_TYPE_SIM_CARD);
            $request->status(Request::STATUS_NEW);
            $request->save();

            $label = htmlspecialchars($simCard->label());
            $content = "测试卡归还申请\r\n测试卡[标志]: {$label}\r\n申请归还人: {$user->name()}[{$user->username()}]\r\n";
            $rUrl = implode('/', ['user', 'profile', 'request', $request->id()]);
            $placeholder = '[:rUrl]';
            $content .= "请求细节查看链接: <a href=\"{$placeholder}\">{$placeholder}</a>";

            @AppService::getEmail()->send('测试卡归还申请', str_replace($placeholder, site_url($rUrl), $content));
            $notify = new Notification();
            $notify->userId($admin->id());
            $notify->message(str_replace($placeholder, "/{$rUrl}", $content));
            $notify->save();

            $pdo->commit();
            $response['message'] = self::ERROR_MESSAGE[self::ERROR_NO_ERROR];
            $response['code'] = self::ERROR_NO_ERROR;
        } catch (Throwable $t) {
            $pdo->rollBack();
            $message = $t->getMessage();
            $code = $t->getCode();
            $response['result'] = false;
            $response['message'] = $message;
            $response['code'] = $code;
        }
        echo json_encode($response);
    }

    public function transferApply($id)
    {
        $response = [
            'result' => true,
        ];
        $pdo = AppService::getPDO();
        $pdo->beginTransaction();
        try {
            $user = App::getUser();
            $simCard = SimCardModal::get($id, true);
            if (!$simCard || $simCard->deleted() === SimCardModal::DELETED_YES) {
                throw new Exception(self::ERROR_MESSAGE[self::ERROR_NO_RECORD], self::ERROR_NO_RECORD);
            }
            if ($simCard->status() !== SimCardModal::STATUS_RENT_OUT || !$simCard->userId()) {
                throw new Exception(self::ERROR_MESSAGE[self::ERROR_WRONG_STATUS] . ', 无法发起转借请求', self::ERROR_WRONG_STATUS);
            }
            if ($simCard->userId() === $user->id()) {
                throw new Exception(self::ERROR_MESSAGE[self::ERROR_WRONG_STATUS] . ', 不能转借给自己', self::ERROR_WRONG_STATUS);
            }
            $request = Request::getOne([
                'assetId' => $id,
                'deleted' => Request::DELETED_NO,
                'assetType' => Request::ASSET_TYPE_SIM_CARD,
                'type' => Request::TYPE_TRANSFER,
                'status' => Request::STATUS_NEW,
            ]);
            if ($request) {
                throw new Exception(self::ERROR_MESSAGE[self::ERROR_WRONG_STATUS] . ', 已被申请', self::ERROR_WRONG_STATUS);
            }
            $request = new Request();
            $request->type(Request::TYPE_TRANSFER);
            $request->fromUserId($user->id());
            $request->toUserId($simCard->userId());
            $request->assetId($id);
            $request->assetType(Request::ASSET_TYPE_SIM_CARD);
            $request->status(Request::STATUS_NEW);
            $request->save();

            $toUser = User::get($simCard->userId());
            $label = htmlspecialchars($simCard->label());
            $content = "测试卡转借请求\r\n测试卡[标志]: {$label}\r\n";
            $content .= "{$user->name()}[{$user->username()}]向你发起对该测试卡的转借请求\r\n";
            $rUrl = implode('/', ['assets', 'inventory', 'simCard', $id]);
            $placeholder = '[:rUrl]';
            $content .= "处理链接: <a href=\"{$placeholder}\">{$placeholder}</a>";

            $notify = new Notification();
            $notify->userId($toUser->id());
            $notify->message(str_replace($placeholder, "/{$rUrl}", $content));
            $notify->save();
            if ($toUser->email()) {
                @AppService::getEmail()->send('测试卡转借请求', str_replace($placeholder, site_url($rUrl), $content), $toUser->email());
            }

            $pdo->commit();
            $response['message'] = self::ERROR_MESSAGE[self::ERROR_NO_ERROR];
            $response['code'] = self::ERROR_NO_ERROR;
        } catch (Throwable $t) {
            $pdo->rollBack();
            $message = $t->getMessage();
            $code = $t->getCode();
            $response['result'] = false;
            $response['message'] = $message;
            $response['code'] = $code;
        }
        echo json_encode($response);
    }

    public function transferConfirm($rqId)
    {
        $response = [
            'result' => true,
        ];
        $action = 'accept';
        if ('' !== ($_POST['action'] ?? '') && $_POST['action'] === 'reject') {
            $action = $_POST['action'];
        }
        $pdo = AppService::getPDO();
        $pdo->beginTransaction();
        try {
            $request = Request::get($rqId, true);
            $simCard = SimCardModal::get($request->assetId(), true);
            $user = App::getUser();
            $fromUser = User::get($request->fromUserId());

            $label = htmlspecialchars($simCard->label());
            $content = "测试卡转借\r\n测试卡[标志]: {$label}, 原借出人: {$user->name()}[{$user->username()}], ";

            if ('accept' === $action) {
                $simCard->userId($fromUser->id());
                $simCard->statusDescription("{$fromUser->name()} [{$fromUser->username()}] 借走了该测试卡");
                $request->status(Request::STATUS_DONE);
                $simCard->save();
                $response['message'] = '已同意转借';
            } else {
                $request->status(Request::STATUS_REJECT);
                $response['message'] = '已拒绝转借';
            }
            $content .= "{$response['message']}\r\n";
            $rUrl = implode('/', ['user', 'profile', 'request', $request->id()]);
            $placeholder = '[:rUrl]';
            $content .= "处理链接: <a href=\"{$placeholder}\">{$placeholder}</a>";

            $notify = new Notification();
            $notify->userId($fromUser->id());
            $notify->message(str_replace($placeholder, "/{$rUrl}", $content));
            $notify->save();
            if ($fromUser->email()) {
                $subject = '测试卡转借|' . ('accept' === $action ? '同意' : '拒绝');
                @AppService::getEmail()->send($subject, str_replace($placeholder, site_url($rUrl), $content), $fromUser->email());
            }

            $request->save();
            $pdo->commit();
        } catch (Throwable $t) {
            $pdo->rollBack();
            $message = $t->getMessage();
            $code = $t->getCode();
            $response['result'] = false;
            $response['message'] = $message;
            $response['code'] = $code;
        }
        echo json_encode($response);
    }

    public function transferConfirmView($rqId)
    {
        $request = Request::get($rqId);
        if (!$request || $request->deleted() === Request::DELETED_YES) {
            show_404();
        }
        $fromUser = User::get($request->fromUserId());
        if (!$fromUser || $fromUser->deleted() === User::DELETED_YES) {
            show_404();
        }
        $contents = [];
        App::view('templates/transfer-confirm', [
            'title' => '转借测试卡',
            'contents' => $contents,
            'url' => "/phone/transferConfirm/{$rqId}",
        ]);
    }
}