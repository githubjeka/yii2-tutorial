<?php

namespace frontend\behaviors;

use yii\base\Behavior;
use yii\web\Controller;
use yii\web\HttpException;

class AccessOnce extends Behavior
{
    public $actions = [];
    public $message = 'Доступ ограничен. Вы ранее совершали действия на этой странице.';

    public function events()
    {
        $owner = $this->owner;

        if ($owner instanceof Controller) {
            return [
                $owner::EVENT_BEFORE_ACTION => 'checkAccess',
                $owner::EVENT_AFTER_ACTION => 'closeDoor',
            ];
        }
    }

    public function closeDoor(\yii\base\ActionEvent $event)
    {
        if (in_array($event->action->id, $this->actions, true)) {
            \Yii::$app->session->set($event->action->id . '-access-lock', 1);
        }
    }

    public function checkAccess(\yii\base\ActionEvent $event)
    {
        if (in_array($event->action->id, $this->actions, true)) {
            if (\Yii::$app->session->get($event->action->id . '-access-lock') !== null) {
                throw new HttpException(403, $this->message);
            }
        }
    }
}