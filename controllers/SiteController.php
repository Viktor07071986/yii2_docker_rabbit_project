<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use app\models\AddMessageRabbitMq;

class SiteController extends AppController {

	public $sample=array();
	public $end_sample;

    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@']
                    ]
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post']
                ]
            ]
        ];
    }

    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction'
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null
            ]
        ];
    }

    public function actionIndex() {
    	$this->setMeta('Главная страница', 'Главная страница', 'Главная страница');
        return $this->render('index');
    }

	public function actionPublisher() {
		$this->setMeta('Publisher', 'Publisher', 'Publisher');
		$model = new AddMessageRabbitMq();
		if (Yii::$app->request->isPost) {
			$connection = new AMQPStreamConnection(Yii::$app->params['rabbit_host'], Yii::$app->params['rabbit_port'], Yii::$app->params['rabbit_login'], Yii::$app->params['rabbit_password']);
			$channel = $connection->channel();

			/*
				// Exchange: (AMQP default (Default Exchange)) example
				$channel->queue_declare('dev_queue', false, true, false, false);
				$messageBody = Yii::$app->request->post()['AddMessageRabbitMq']['text_message'];
				$message = new AMQPMessage($messageBody, array('content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));
				$channel->basic_publish($message, '', 'dev_queue');
			*/

			/*
				// amq.direct (Direct Exchange) example
				$channel->exchange_declare('direct_logs1', AMQPExchangeType::DIRECT, false, true, false);
				$severities = ['error', 'info', 'warning'];
				//$channel->queue_declare(Yii::$app->params['rabbit_queue'], false, true, false, false);
				$channel->queue_declare('', false, true, false, false);
				foreach ($severities as $severity) {
					//$channel->queue_bind(Yii::$app->params['rabbit_queue'], 'direct_logs1', $severity);
					$channel->queue_bind('', 'direct_logs1', $severity);
					$messageBody = Yii::$app->request->post()['AddMessageRabbitMq']['text_message'];
					$message = new AMQPMessage($messageBody, array('content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));
					$channel->basic_publish($message, 'direct_logs1', $severity);
				}
			*/

			/*
				// amq.fanout (Fanout Exchange) example
				$channel->exchange_declare('notifier', AMQPExchangeType::FANOUT, false, true, false);
				$channel->queue_declare('notifier_queue', false, true, false, false);
				//$channel->queue_declare('', false, true, false, false);
				$channel->queue_bind('notifier_queue', 'notifier');
				//$channel->queue_bind('', 'notifier');
				$messageBody = Yii::$app->request->post()['AddMessageRabbitMq']['text_message'];
				$message = new AMQPMessage($messageBody, array('content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));
				$channel->basic_publish($message, 'notifier');
			*/

			$channel->exchange_declare(Yii::$app->params['rabbit_exchange'], AMQPExchangeType::DIRECT, false, true, false);
			$channel->queue_declare(Yii::$app->params['rabbit_queue'], false, true, false, false);
			$channel->queue_bind(Yii::$app->params['rabbit_queue'], Yii::$app->params['rabbit_exchange']);
			$messageBody = Yii::$app->request->post()['AddMessageRabbitMq']['text_message'];
			$message = new AMQPMessage($messageBody, array('content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));
			$channel->basic_publish($message, Yii::$app->params['rabbit_exchange']);

			$channel->close();
			$connection->close();
			return $this->refresh();
		}
		return $this->render('publisher', compact('model'));
	}

	public function actionConsumer() {
		$this->setMeta('Consumer', 'Consumer', 'Consumer');
		$model = new AddMessageRabbitMq();
		$connection = new AMQPStreamConnection(Yii::$app->params['rabbit_host'], Yii::$app->params['rabbit_port'], Yii::$app->params['rabbit_login'], Yii::$app->params['rabbit_password']);
		$channel = $connection->channel();
		$channel->exchange_declare(Yii::$app->params['rabbit_exchange'], AMQPExchangeType::DIRECT, false, true, false);
		$channel->queue_declare(Yii::$app->params['rabbit_queue'], false, true, false, false);
		list($queue, $messageCount, $consumerCount) = $channel->queue_declare(Yii::$app->params['rabbit_queue'], true);
		$error_count = 0;
		$alls_queues_rabbits_shows = AddMessageRabbitMq::find()->asArray()->all();
		if (Yii::$app->request->isPost) {
			if (Yii::$app->request->post()["AddMessageRabbitMq"]["int_message_count"] > $messageCount) {
				$error_count = 1;
			} else {
				$channel->queue_bind(Yii::$app->params['rabbit_queue'], Yii::$app->params['rabbit_exchange']);
				$rabbitForm = Yii::$app->request->post()["AddMessageRabbitMq"]["int_message_count"];
				for ($i = 0; $i < $rabbitForm; $i++) {
					$result = $channel->basic_get('RabbitMQQueue', true, null)->body;
					$add_rabbit_message_db = new AddMessageRabbitMq();
					$add_rabbit_message_db->text_message = $result;
					$add_rabbit_message_db->save(false);
				}
				$channel->close();
				$connection->close();
				return $this->refresh();
			}
		}
		return $this->render('consumer', compact('model', 'messageCount', 'error_count', 'alls_queues_rabbits_shows'));
	}

    public function actionLogin() {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        $model->password = '';
        return $this->render('login', compact('model'));
    }

    public function actionLogout() {
        Yii::$app->user->logout();
        return $this->goHome();
    }

    public function actionContact() {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');
            return $this->refresh();
        }
        return $this->render('contact', compact('model'));
    }

    public function actionAbout() {
        return $this->render('about');
    }

}