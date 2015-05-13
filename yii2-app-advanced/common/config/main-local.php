<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'sqlite:' . __DIR__ . '/../../sqlite.db',
            'on ' . yii\db\Connection::EVENT_AFTER_OPEN => function ($event) {
                $event->sender->createCommand('PRAGMA foreign_keys = ON;')->execute();
            }
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
    ],
];
