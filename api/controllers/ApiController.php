<?php

namespace app\controllers;

use app\components\AppStoreException;
use app\components\DeviceAuth;
use app\models\News;
use app\models\NewsPhoto;
use app\models\Company;
use app\models\Favorite;
use app\models\CompanyFile;
use app\models\CompanyPhoto;
use app\models\CompanyValue;
use app\models\Device;
use app\models\MessageQueue;
use app\models\Product;
use app\models\Profile;
use app\models\Purchase;
use app\models\Version;
use app\models\Currency;
use app\models\RegistrationRequest;
use Exception;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\rest\Serializer;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use Yii;
use yii\web\ServerErrorHttpException;
use yii\helpers\Url;
use yii\web\UploadedFile;
use yii\db\ActiveQuery;

class ApiController extends Controller
{
    public $enableCsrfValidation = false;

    public function init()
    {
        parent::init();
        Yii::$app->language = 'en-US';
        $user = Yii::$app->user;
        $user->identityClass = Device::className();
        $user->enableSession = false;
    }

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'contentNegotiator' => [
                'class' => ContentNegotiator::className(),
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
            'authenticator' => [
                'class' => DeviceAuth::className(),
                'except' => [ 'auth', 'subscribe' ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'only' => [/*'subscribe',*/ 'unsubscribe', 'remove-device', 'purchase', 'profile', 'setup-profile', 'setup-phone', 'setup-phone-confirm'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'auth' => ['post'],
                    'purchase' => ['post'],
                    'subscribe' => ['post'],
                    'unsubscribe' => ['post'],
                    'set-tokens' => ['post'],
                    'set-profile' => ['post'],
                    'set-phone' => ['post'],
                    'set-phone-confirm' => ['post'],
                    'set-language' => ['post'],
                    'remove-device' => ['post'],
                ],
            ],
        ]);
    }

    public function actionProfile()
    {
        return $this->loadDevice();
    }

    public function actionProfileAll()
    {
        $device = $this->loadDevice();
        $profile = null !== $device ? $device->getProfile()->one() : null;


        $result = [
            'lang' => null !== $device ? $device->language : null,
            'apns_token' => null !== $device ? $device->apns_token : null,
            'firebase_token' => null !== $device ? $device->firebase_token : null,
            'device_token' => null !== $device ? $device->device_token : null,
            'access_token' => null !== $device ? $device->access_token : null,

            'id' => null !== $profile ? $profile->id : null,
            'nickname' => null !== $profile ? $profile->nickname : null,
            'first_name' => null !== $profile ? $profile->first_name : null,
            'last_name' => null !== $profile ? $profile->last_name : null,
            'email' => null !== $profile ? $profile->email : null,
            'phone' => null !== $profile ? $profile->phone : null,
            'expired_at' => null !== $profile ? $profile->expired_at : null,
            // 'avatar' => null !== $profile ? rtrim(Url::home(true), '/') . $profile->getImageFileUrl('avatar') : null,
            'avatar' => (null !== $profile) ? $profile->avatar : null,

            'device' => $device,
            'profile' => $profile,

            'is_purchase' => null !== $device && !!$this->isPurchase(),
        ];

        return $result;
    }

    public function actionProfileAllForPhone($phone)
    {
        $device = $this->loadDevice();

        $q = "
            SELECT * FROM `msfo_profile` WHERE REPLACE( REPLACE( REPLACE( REPLACE( REPLACE(  `phone` ,  ' ',  '' ) ,  '+',  '' ) ,  '(',  '' ) ,  ')',  '' ) ,  '-',  '' ) = $phone 
        ";
        foreach (Yii::$app->db->createCommand($q)->queryAll() as $data) {
            $forphone = $data;
        }

        $my_device = Device::find()->select(['access_token'])->where([ 'id' => $forphone['device_id'] ])->one();

        $result = [
            'lang' => null !== $device ? $device->language : null,
            'apns_token' => null !== $device ? $device->apns_token : null,
            'firebase_token' => null !== $device ? $device->firebase_token : null,
            'device_token' => null !== $device ? $device->device_token : null,
            'access_token' => null !== $device ? $device->access_token : null,

            'id' => $forphone['id'],
            'nickname' => $forphone['nickname'],
            'first_name' => $forphone['first_name'],
            'last_name' => $forphone['last_name'],
            'email' => $forphone['email'],
            'phone' => $forphone['phone'],
            'expired_at' => $forphone['expired_at'],
            // 'avatar' => null !== $profile ? rtrim(Url::home(true), '/') . $profile->getImageFileUrl('avatar') : null,
            'avatar' => $forphone['avatar'],

            'device' => $device,
            'profile' => $forphone,

            'is_purchase' => null !== $device && !!$this->isPurchase(),
            'my_access_token' => $my_device->access_token,
        ];

        return $result;

    }

    public function actionVersion()
    {
        return (int)Version::find()->max('id');
    }

    public function actionNews($company_id = null)
    {
        $query = News::find()->with(['photos'])
            ->joinWith(['companies companies'])
            ->where(['publish' => 1])
            ->andFilterWhere(['companies.id' => $company_id])
            ->orderBy(['id' => SORT_DESC]);

        $provider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'class' => Pagination::className(),
                'pageParam' => 'page',
                'pageSize' => 20
            ],
        ]);

        return (new Serializer())->serialize($provider);
    }

    public function actionCompanies()
    {
        if ($this->isPurchase()) {
            // отдаем все
            $query = Company::find()->with(['mode', 'group'])->orderBy(['id' => SORT_ASC]);
        } else {
            // отдаем только бесплатные
            $query = Company::find()->with(['mode', 'group'])->where(['free' => 1])->orderBy(['id' => SORT_ASC]);
        }

        return $query->all();
    }

    public function actionCompanyid($comp_id)
    {
       $query = Company::find()->with(['mode', 'group'])->where(['id' => $comp_id])->orderBy(['id' => SORT_ASC]);

        return $query->all();
    }
    //

    /*
     *
     */
    public function actionCompaniesVersions()
    {
        $res = [];

        $query = Company::find()->select(['id', 'version']);
        foreach ($query->all() as $company) {
            $res[] = "{$company->id} - {$company->version}";
        }

        return json_encode($res);
    }

    /*
     *
     */
    public function actionCompany($company_id)
    {
        $query = Company::find()->where(['id' => $company_id]);

        return $query->all();
    }

    /*
     *
     */
    public function actionCompanyStructurePhoto($company_id)
    {
        $res = [];

        $query = Company::find()->select(['photo_struct_ru', 'photo_struct_en'])->where(['id' => $company_id]);

        foreach ($query->all() as $company) {
            if ($company->photo_struct_ru) {
                $res['ru'] = $company->photo_struct_ru;
            }

            if ($company->photo_struct_en) {
                $res['en'] = $company->photo_struct_en;
            }
        }

        return $res;
    }

    /*
     *
     */
    public function actionSetCompanyChatroomId($company_id, $chatroom_id)
    {
        $company = Company::find()->where(['id' => $company_id])->one();
        $company->firechat_chat_id = $chatroom_id;
        $company->save();

        return ['op_status' => true];
    }

    /*
     *
     */
    public function actionCompaniesChats()
    {
        $res = [];

        $query = Company::find()->select('id, firechat_chat_id')->where(['not', ['firechat_chat_id' => null]]);

        foreach ($query->all() as $company) {
            $res[$company->id] = $company->firechat_chat_id;
        }

        return $res;
    }

    /*
     *
     */
    public function actionCompanyChatPushOn($profile_id, $company_id)
    {
        try {
            Yii::$app->db->createCommand()->insert('msfo_push_subscription', [
                'profile_id' => $profile_id,
                'company_id' => $company_id
            ])->execute();

            //return true;
        } catch (\Exception $e) {
            //return false;
        }

        return true;
    }

    /*
     *
     */
    public function actionCompanyChatPushOff($profile_id, $company_id)
    {
        try {
            Yii::$app->db->createCommand()->delete('msfo_push_subscription', "profile_id = {$profile_id} AND company_id = {$company_id}")->execute();

            //return true;
        } catch (\Exception $e) {
            //return false;
        }

        return true;
    }

    /*
     *
     */
    public function actionNewCompanyChatMessage($company_id)
    {
        try {
            Yii::$app->db->createCommand()->insert('msfo_new_company_chat_message', [
                'company_id' => $company_id
            ])->execute();

            //return true;
        } catch (\Exception $e) {
            //return false;
        }

        return true;
    }

    /*
     *
     */
    public function actionLastMessagesToUser($token, $limit = 3)
    {
        $messages = [];

        $device_id = Device::find()->where(['access_token' => $token])->one()->id;
        $query = MessageQueue::find()->joinWith('message')->where(['device_id' => $device_id])->orderBy(['created_at' => SORT_DESC])->limit($limit);
        foreach ($query->all() as $message) {
            $message = $message->getMessage()->one();

            $messages[] = [
                'id'        => $message->id,
                'message'   => $message->message
            ];
        }

        return $messages;
        //return json_encode($messages, JSON_UNESCAPED_UNICODE);
    }

    /*
     *
     */
    public function actionCron()
    {
        $subscriptions = [];

        //

        $q = "
            SELECT `company_id`, `name`
                FROM `msfo_new_company_chat_message` AS `t1` INNER JOIN `msfo_company` AS `t2` ON `company_id` = `id`
        ";
        foreach (Yii::$app->db->createCommand($q)->queryAll() as $data) {
            $subscriptions[$data['company_id']] = [
                'name' => $data['name'],
                'profiles' => []
            ];
        }

        //

        foreach ($subscriptions as $company_id => &$profiles) {
            $q = "
                SELECT `profile_id`, `device_id`
                    FROM `msfo_push_subscription` AS `t1` INNER JOIN `msfo_profile` AS `t2` ON `profile_id` = `id`
                    WHERE `company_id` = {$company_id}
            ";
            foreach (Yii::$app->db->createCommand($q)->queryAll() as $data) {
                $profiles['profiles'][$data['profile_id']] = [
                    'device_id' => $data['device_id']
                ];
            }
        }

        //


        foreach ($subscriptions as $company_id => $data) {
            $res = Yii::$app->db->createCommand()->insert('msfo_message', [
                'created_at'    => time(),
                'message'       => "Новые сообщения в чате компании \"{$data['name']}\".",
                'target'        => 'all',
                'language'      => 'RU',
                'type'          => 1
            ])->execute();

            if ($res) {
                $message_id = Yii::$app->db->getLastInsertID();

                foreach ($data['profiles'] as $profile_id => $profile) {
                    $res = Yii::$app->db->createCommand()->insert('msfo_message_queue', [
                        'message_id'    => $message_id,
                        'device_id'     => $profile['device_id'],
                        'type'          => 1
                    ])->execute();
                }
            }

            Yii::$app->db->createCommand()->delete('msfo_new_company_chat_message', "company_id = {$company_id}")->execute();
        }

        //print_r($subscriptions);
        //die;

        return true;
    }

    /*
     *
     */
    public function actionCron2()
    {
        $q = MessageQueue::find()->where(['type' => 1]);

        $res = [];
        foreach ($q->all() as $queue_element) {
            $res[$queue_element->id] = Yii::$app->runAction('messages/send', [
                'id' => $queue_element->message_id,
                'type' => 1
            ]);
        }

        return $res;
    }

    /*
     *
     */
    public function actionCheckPhone($phone, $mode = 0)
    {
        $phone = str_replace(
            [
                ' ',
                '+',
                '(',
                ')',
                '-'
            ],
            '',
            $phone
        );

        // #1
        switch ($mode) {

            case 1:
                $res = null;
                break;

            default:
                $res = false;

        }

        $phones = [];

        $q = "
            SELECT REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(`phone`, ' ', ''), '+', ''), '(', ''), ')', ''), '-', '') AS `phone`, `id` AS `profile_id`
                FROM `msfo_profile`
                ORDER BY `phone`, `expired_at`
        ";
        foreach (Yii::$app->db->createCommand($q)->queryAll() as $data) {
            $phones[$data['phone']] = $data['profile_id'];
        }

        // #2
        switch ($mode) {

            case 1:

                // новый код для авторизации
//                $device = Device::find()->where(['profile_id' => $phones[$phone]])->one();
//
//                $res = [
//                    'device_id'     => $device->id,
//                    'device_token'  => $device->device_token,
//                    'access_token'  => $device->access_token
//                ];

                 $profile = Profile::find()->where(['id' => $phones[$phone]])->one();
                 $device_id = $profile->device_id;

                 $device = Device::find()->where(['id' => $device_id])->one();

                 $res = [
                     'device_id'     => $device_id,
                     'device_token'  => $device->device_token,
                     'access_token'  => $device->access_token
                 ];
                break;

            default:
                if (array_key_exists($phone, $phones)) {
                    $res = true;
                }

        }

        return $res;
    }

    /*
     *
     */
    public function actionProfileAvatar($profile_id)
    {
        $profile = Profile::find()->where(['id' => $profile_id])->one();

        return $profile->avatar;
    }

    /*
     *
     */
    public function actionAppendFileToChat()
    {
        $id = 0;

        $companyId = Yii::$app->request->post('company_id');
        $profileId = Yii::$app->request->post('profile_id');

        /*
            print_t($_FILES);
            Array
            (
                [photo_file] => Array
                    (
                        [name] => btn2-documents-h.png
                        [type] => image/png
                        [tmp_name] => /var/www/ifinik/data/mod-tmp/phpGvti93
                        [error] => 0
                        [size] => 480
                    )

            )
        */

        /*print_r($_FILES);
        die;*/

        if (!empty($_FILES)) {
            if (
                !$_FILES['photo_file']['error']
                and
                ($_FILES['photo_file']['size'] <= 5242880)
            ) {
                switch ($_FILES['photo_file']['type']) {
                    case 'image/jpeg':
                        $f_ext = 'jpg';
                        break;
                    case 'image/png':
                        $f_ext = 'png';
                        break;
                    case 'image/gif':
                        $f_ext = 'gif';
                        break;
                    case 'image/bmp':
                        $f_ext = 'bmp';
                        break;
                    case 'application/pdf':
                        $f_ext = 'pdf';
                        break;
                    case 'application/msword':
                        $f_ext = 'doc';
                        break;
                    case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                        $f_ext = 'docx';
                        break;
                    case 'application/vnd.ms-excel':
                        $f_ext = 'xls';
                        break;
                    case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
                        $f_ext = 'xlsx';
                        break;
                    case 'application/x-zip-compressed':
                        $f_ext = 'zip';
                        break;
                    case 'application/octet-stream':
                        $f_ext = 'rar';
                        break;
                    case 'video/mpeg':
                        $f_ext = 'mpeg';
                        break;
                    default:
                        return $id;
                }

                Yii::$app->db->createCommand()->insert('msfo_chat_file', [
                    'company_id' => $companyId,
                    'profile_id' => $profileId
                ])->execute();

                if ($id = Yii::$app->db->getLastInsertID()) {
                    $filename = "file-{$id}.{$f_ext}";

                    //

                    if (
                        $res = move_uploaded_file(
                            $_FILES['photo_file']['tmp_name'],
                            __DIR__."/../web/upload/chat/{$filename}"
                        )
                    ) {
                        Yii::$app->db->createCommand()->update(
                            'msfo_chat_file',
                            [
                                'filename' => $filename
                            ],
                            "id = {$id}"
                        )->execute();
                    }
                }
            }
        }

        return $id;
    }

    /*
     *
     */
    public function actionChatFilenameById($chat_file_id)
    {
        $q = "
            SELECT `filename`
                FROM `msfo_chat_file`
                WHERE `id` = {$chat_file_id}
        ";

        return Yii::$app->db->createCommand($q)->queryAll()[0]['filename'];
    }

    //

    public function actionValues($company_id, $year = null)
    {
        if (empty($company_id)) {
            $query = CompanyValue::find(); // убрать ->orderBy(['value_v'=>SORT_DESC])
        } else {
            $query = CompanyValue::find()->where(['company_id' => $company_id])->with(['reportType'])->orderBy('year');
        }

        $query->andFilterWhere(['year' => $year]);
        if (!$this->isPurchase()) {
            $query->free();
        }
        return $query->all();
    }



    public function actionCompanyWithValues($year = null)
    {
        // $query = 'SELECT * FROM (SELECT * FROM `msfo_company` LEFT JOIN `msfo_company_value` ON msfo_company.id = msfo_company_value.company_id WHERE msfo_company_value.year <= 2017 ORDER BY msfo_company_value.year DESC) msfo_company GROUP BY msfo_company.id';

        // $query = 'SELECT * FROM `msfo_company` LEFT JOIN `msfo_company_value` ON msfo_company.id = msfo_company_value.company_id WHERE msfo_company_value.year ='.$year;

        if (!$this->isPurchase()) {
            $query = 'SELECT * FROM (SELECT * FROM `msfo_company` LEFT JOIN `msfo_company_value` ON msfo_company.id = msfo_company_value.company_id WHERE msfo_company.free = 1 AND msfo_company_value.year <= '.$year.' ORDER BY msfo_company_value.year DESC) msfo_company GROUP BY msfo_company.id';
        } else {
            $query = 'SELECT * FROM (SELECT * FROM `msfo_company` LEFT JOIN `msfo_company_value` ON msfo_company.id = msfo_company_value.company_id WHERE msfo_company_value.year <= '.$year.' ORDER BY msfo_company_value.year DESC) msfo_company GROUP BY msfo_company.id';
        }
        $result = Yii::$app->db->createCommand($query)->queryAll();
        return $result;
    }


    public function actionValuesRaw($company_id, $year = null)
    {
        $query = CompanyValue::find()->where(['company_id' => $company_id])->orderBy('year');
        $query->andFilterWhere(['year' => $year]);
        if (!$this->isPurchase()) {
            $query->free();
        }

        $result = [ ];
        foreach($query->all() as $value) {
            $result[] = $value->getRaw();
        }

        foreach ($result as $obj) {
            foreach ($obj as $prop => &$val) {
                if ($prop >= 36 and ('' == trim($val) or null === $val)) {
                    $val = 'n/a';
                }
            }
        }

        return $result;
    }

    public function actionFiles($company_id, $year = null, $lang = null)
    {
        $query = CompanyFile::find()->where(['company_id' => $company_id])->orderBy('year');
        $query->andFilterWhere([
            'year' => $year,
            'lang' => $lang,
        ]);
        if (!$this->isPurchase()) {
            $query->free();
        }
        if (!$models = $query->all()) {
            $query = CompanyFile::find()->where(['company_id' => $company_id])->orderBy('year');
            $query->andFilterWhere([
                'year' => $year,
                'lang' => 'ru',
            ]);
            if (!$this->isPurchase()) {
                $query->free();
            }
            $models = $query->all();
        }
        return $models;
    }

    public function actionFile($id)
    {
        if (empty($id)) {
            throw new BadRequestHttpException('Invalid id param');
        }
        /** @var CompanyFile $model */
        if (!$model = CompanyFile::findOne(['file' => $id . '.pdf'])) {
            throw new NotFoundHttpException('File not found');
        }

        return Yii::$app->response->sendFile($model->getFileSrc());
    }

    public function actionPhotos($company_id)
    {
        $query = CompanyPhoto::find()->where(['company_id' => $company_id])->orderBy('id');

        if (!$this->isPurchase()) {
            $query->free();
        }
        if (!$models = $query->all()) {
            $query = CompanyPhoto::find()->where(['company_id' => $company_id])->orderBy('id');
            if (!$this->isPurchase()) {
                $query->free();
            }
            $models = $query->all();
        }
        return $models;
    }

    public function actionPhoto($id, $full = false)
    {
        if (empty($id)) {
            throw new BadRequestHttpException('Invalid id param');
        }
        /** @var CompanyPhoto $model */
        if (!$model = CompanyPhoto::findOne(['file' => $id])) {
            throw new NotFoundHttpException('File not found');
        }

        $file_path = null;

        if(!$full) {
            $model->createThumbs();

            $file_path = $model->getThumbFilePath('file', 'thumb');
        } else {
            $file_path = $model->getThumbFilePath('file', 'photo');
        }

        return Yii::$app->response->sendFile($file_path);
    }

    public function actionCurrencies()
    {
        return Currency::find()->orderBy('year')->all();
    }

    public function actionAuth()
    {
        if ($token = Yii::$app->request->post('device_token')) {
            /** @var Device $device */
            if (!$device = Device::find()->where(['device_token' => $token])->limit(1)->one()) {
                throw new NotFoundHttpException('Device is not found');
            }

            $device->generateAccessToken();

            if (!$device->save()) {
                throw new ServerErrorHttpException(json_encode($device->errors));
            }
        } else {
            $device = new Device();
            $device->generateAccessToken();
        }

        Yii::$app->response->getHeaders()->set('X-User-Token', $device->access_token);

                 return $device->access_token;
    }

    public function actionSetupProfile() {

        /* application/x-www-form-urlencoded */

        if (!$firstName = Yii::$app->request->post('first_name')) {
            throw new BadRequestHttpException('Empty first_name param');
        }

        if (!$lastName = Yii::$app->request->post('last_name')) {
            throw new BadRequestHttpException('Empty last_name param');
        }

        $email = Yii::$app->request->post('email');

        if (!$nickname = trim(Yii::$app->request->post('nickname'))) {
            // throw new BadRequestHttpException('Empty nickname param');
            $nickname = "{$firstName}_{$lastName}";
        }

        if(!$device = $this->loadDevice()) {
            throw new NotFoundHttpException("Device not found");
        }

        if(!$profile = $device->getProfile()->one()) { // $device->getProfile()
            throw new NotFoundHttpException("Profile not found");
        }

        if (
            !$firstName
            and
            !$lastName
            and
            !$email
            and
            !$nickname
        ) {
            $firstName  = '0000';
            $lastName   = '0000';
            $email      = '0000';
            $nickname   = '0000';
        }

        $profile->first_name    = $firstName;
        $profile->last_name     = $lastName;
        $profile->email         = $email;
        $profile->nickname      = $nickname;

        if (!empty($_FILES)) {

            /* multipart/form-data */

            /*

                print_r($_FILES);
                Array
                (
                    [avatar] => Array
                        (
                            [name] => test.jpg
                            [type] => image/jpeg
                            [tmp_name] => /var/www/ifinik/data/mod-tmp/phpp06mEW
                            [error] => 0
                            [size] => 19426
                        )

                )

            */

            if (!$_FILES['ava']['error']) {
                switch ($_FILES['ava']['type']) {
                    case 'image/jpeg':
                        $f_ext = 'jpg';
                        break;
                    case 'image/png':
                        $f_ext = 'png';
                        break;
                }
                $filename = "avatar.{$f_ext}";

                //
                if (!file_exists(__DIR__."/../web/upload/avatar/profile-{$profile->id}")) {
                    mkdir(__DIR__."/../web/upload/avatar/profile-{$profile->id}");
                }

                if (
                    $res = move_uploaded_file(
                        $_FILES['ava']['tmp_name'],
                        __DIR__."/../web/upload/avatar/profile-{$profile->id}/{$filename}"
                    )
                ) {
                    Yii::$app->db->createCommand()->update(
                        'msfo_profile',
                        [
                            'avatar' => $filename
                        ],
                        "id = {$profile->id}"
                    )->execute();
                }
                //
            }
        }
        //$profile->avatar = UploadedFile::getInstance($profile, 'avatar');

        if (!$profile->save()) {
            throw new ServerErrorHttpException(json_encode($profile->errors));
        }

        return "ok";
    }

    public function actionSetupPhone()
    {
        if(!$device = $this->loadDevice()) {
            throw new NotFoundHttpException("Device not found");
        }

        if(!$device->id) {
            if (!(Yii::$app->request->post('device_id'))) {
                throw new NotFoundHttpException("Device not registered");
            }

            $device->id = Yii::$app->request->post('device_id');
        }

        if (!$phone = Yii::$app->request->post('phone')) {
            throw new BadRequestHttpException('Empty phone param');
        }

        $reg_request = RegistrationRequest::prepareRequest($phone);
        $reg_request->profile_data = serialize(
            [
                'login' => Yii::$app->request->post('login'),
                //'password' => Yii::$app->request->post('password')
            ]
        );

        if ($registered_on = Yii::$app->request->post('registered_on')) {
            $reg_request->registered_on = $registered_on;
        }

        if (!$reg_request->save()) {
            throw new ServerErrorHttpException(json_encode($reg_request->errors));
        }

        $this->getSmsSender()->send($phone, 'Your confirmation code: ' . $reg_request->confirm_code);

        //return $reg_request->confirm_code;
        return 'ok';
    }

    public function actionSetupPhoneConfirm()
    {
        if (!$code = Yii::$app->request->post('confirm_code')) {
            throw new BadRequestHttpException('Invalid confirm code param');
        }

        if (!$reg_request = RegistrationRequest::find()->where(['confirm_code' => $code ])->limit(1)->one()) {
            throw new BadRequestHttpException('Incorrect confirm code');
        }

        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $reg_request->confirmed = true;
            $reg_request->confirm_code = null;
            $reg_request->save();

            if(!$device = $this->loadDevice()) {
                throw new NotFoundHttpException("Device not found");
            }

            if(!$device->id) {
                if (!(Yii::$app->request->post('device_id'))) {
                    throw new NotFoundHttpException("Device not registered");
                }

                $device->id = Yii::$app->request->post('device_id');
            }

            //
            $phone = str_replace(
                [
                    ' ',
                    '+',
                    '(',
                    ')',
                    '-'
                ],
                '',
                $reg_request->phone
            );

            $condition = "
                REPLACE(
                    REPLACE(
                        REPLACE(
                            REPLACE(
                                REPLACE(
                                    `phone`,
                                    ' ',
                                    ''
                                ),
                                '+',
                                ''
                            ),
                            '(',
                            ''
                        ),
                        ')',
                        ''
                    ),
                    '-',
                    ''
                ) = {$phone}
            ";
            //

            // if (!$profile = Profile::find()->where([ 'phone' => $reg_request->phone ])->limit(1)->one()) {
            if (!$profile = Profile::find()->where($condition)->limit(1)->one()) {

                // п-ль ввёл "+7(903) 181-25-79"
                // в бд хранится "+79031812579"
                // поэтому происходит "Duplicate entry '6294' for key 'idx_profile_device_id'"...

                $profile = new Profile();

                $profile->first_name = '';
                $profile->last_name = '';
                $profile->email = '';

                $profile->expired_at = time() + 3600 * 24 * 30;
                $profile->registered_on = $reg_request->registered_on;
                $profile->login = unserialize($reg_request->profile_data)['login'];
                //$profile->password = unserialize($reg_request->profile_data)['password'];

                /** @var Product $product */
                if (!$product = Product::findOne([ 'pid' => \Yii::$app->params['profileNewProductPid'] ])) {
                    throw new NotFoundHttpException('Product not found');
                }

                $purchase = new Purchase();
                $purchase->device_id = $device->id;
                $purchase->product_id = $product->id;
                $purchase->receipt = null;
                $purchase->created_at = time();
                $purchase->expired_at = $product->days ? time() + $product->days * 3600 * 24 : null;
                $purchase->notified = false;

                $purchase->save();
            } else {
                // переносим покупки
                if($purchases = Purchase::find()->where([ 'device_id' => $profile->device_id])->all()) {
                    foreach ($purchases as $pur) {
                        $pur->device_id = $device->id;
                        $pur->save();
                    }
                }
            }

            $profile->device_id = $device->id;

            $profile->phone = $reg_request->phone;
            $profile->phone_confirm_code = null;
            $profile->confirm = true;

            //Это хитрый ход
            //$device->profile_id = $profile->id;

            if (!$profile->save() || !$device->save()) {
                throw new ServerErrorHttpException(json_encode($profile->errors));
            }

            $transaction->commit();

            return (string)$reg_request->phone;
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

//    public function actionSetupPhoneConfirmMy()
//    {
//        if (!$code = Yii::$app->request->post('confirm_code')) {
//            throw new BadRequestHttpException('Invalid confirm code param');
//        }
//
//        if (!$reg_request = RegistrationRequest::find()->where(['confirm_code' => $code ])->limit(1)->one()) {
//            throw new BadRequestHttpException('Incorrect confirm code!');
//        }
//
//        $transaction = \Yii::$app->db->beginTransaction();
//        try {
//            $reg_request->confirmed = true;
//            $reg_request->confirm_code = null;
//            $reg_request->save();
//
//
//            //
//            $phone = str_replace(
//                [
//                    ' ',
//                    '+',
//                    '(',
//                    ')',
//                    '-'
//                ],
//                '',
//                $reg_request->phone
//            );
//
//            $condition = "
//                REPLACE(
//                    REPLACE(
//                        REPLACE(
//                            REPLACE(
//                                REPLACE(
//                                    `phone`,
//                                    ' ',
//                                    ''
//                                ),
//                                '+',
//                                ''
//                            ),
//                            '(',
//                            ''
//                        ),
//                        ')',
//                        ''
//                    ),
//                    '-',
//                    ''
//                ) = {$phone}
//            ";
//
//
//            $q = "
//            SELECT REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(`phone`, ' ', ''), '+', ''), '(', ''), ')', ''), '-', '') AS `phone`, `id` AS `profile_id`
//                FROM `msfo_profile`
//                ORDER BY `phone`, `expired_at`
//        ";
//            foreach (Yii::$app->db->createCommand($q)->queryAll() as $data) {
//                $phones[$data['phone']] = $data['profile_id'];
//            }
//
//            $device = $this->loadDevice();
//
////            $profile = Profile::find()->where(['id' => $phones[$phone]])->one();
////            $device_id = $profile->device_id;
////
////            $device = Device::find()->where(['id' => $device_id])->one();
//
//
//            if (!$profile = Profile::find()->where($condition)->limit(1)->one()) {
//
//                $profile = new Profile();
//
//                $profile->first_name = '';
//                $profile->last_name = '';
//                $profile->email = '';
//
//                $profile->expired_at = time() + 3600 * 24 * 30;
//                $profile->registered_on = $reg_request->registered_on;
//                $profile->login = unserialize($reg_request->profile_data)['login'];
//
//                /** @var Product $product */
//                if (!$product = Product::findOne([ 'pid' => \Yii::$app->params['profileNewProductPid'] ])) {
//                    throw new NotFoundHttpException('Product not found');
//                }
//
//                $purchase = new Purchase();
//                $purchase->device_id = $device->id;
//                $purchase->product_id = $product->id;
//                $purchase->receipt = null;
//                $purchase->created_at = time();
//                $purchase->expired_at = $product->days ? time() + $product->days * 3600 * 24 : null;
//                $purchase->notified = false;
//
//                $purchase->save();
//            } else {
//                // переносим покупки
//                if($purchases = Purchase::find()->where([ 'device_id' => $profile->device_id])->all()) {
//                    foreach ($purchases as $pur) {
//                        $pur->device_id = $device->id;
//                        $pur->save();
//                    }
//                }
//            }
//
//            $profile->device_id = $device->id;
//
//            $profile->phone = $reg_request->phone;
//            $profile->phone_confirm_code = null;
//            $profile->confirm = true;
//
//            if (!$profile->save() || !$device->save()) {
//                throw new ServerErrorHttpException(json_encode($profile->errors));
//            }
//
//            $transaction->commit();
//
//            return (string)$phone;
//        } catch (Exception $e) {
//            $transaction->rollBack();
//            throw $e;
//        }
//    }

    public function actionSetTokens() {
        $apns_token = Yii::$app->request->post('apns_token');
        $firebase_token = Yii::$app->request->post('firebase_token');

        if(!$device = $this->loadDevice()) {
            throw new NotFoundHttpException("Device not found");
        }

        if($firebase_token) {
            $device->firebase_token = $firebase_token;
        }

        if($apns_token) {
            $device->apns_token = $apns_token;
        }

        if (!$device->save()) {
            throw new ServerErrorHttpException(json_encode($device->errors));
        }
        return 'ok';
    }


    public function actionSubscribe()
    {
        if (!$token = Yii::$app->request->post('device_token')) {
            throw new BadRequestHttpException('Invalid device_token param');
        }

        /** @var Device $device */
        if ($device = Device::find()->where(['device_token' => $token])->limit(1)->one()) {
            $this_device = $this->loadDevice();

            if($this_device) {
                $device->access_token = $this_device->access_token;
            }

            if (!$device->save()) {
                throw new ServerErrorHttpException(json_encode($device->errors));
            }
            return 'ok';
        }

        // $device = NULL
        // #1

        if(!$device = $this->loadDevice()) {

            // $device = NULL
            // #2

            $device = new Device();
        }

        // #3

        $device->device_token = $token;

        if (!$device->save()) {
            throw new ServerErrorHttpException(json_encode($device->errors));
        }

        return 'ok';
    }

    public function actionUnsubscribe()
    {
        $device = $this->loadDevice();

        if (empty($device->device_token)) {
            throw new NotFoundHttpException('Subscribe is not found');
        }

        $device->device_token = null;
        $device->apns_token = null;
        $device->firebase_token = null;

        if (!$device->save()) {
            throw new ServerErrorHttpException(json_encode($device->errors));
        }

        return 'ok';
    }

    public function actionRemoveDevice()
    {
        $device = $this->loadDevice();

        if (empty($device->device_token)) {
            throw new NotFoundHttpException('Device is not found');
        }

        if (!$device->delete()) {
            throw new ServerErrorHttpException(json_encode($device->errors));
        }

        return 'ok';
    }

    public function actionSetLanguage()
    {
        if (!$language = Yii::$app->request->post('language')) {
            throw new BadRequestHttpException('Invalid language param');
        }

        $device = $this->loadDevice();

        if (empty($device->device_token)) {
            throw new NotFoundHttpException('Subscribe is not found');
        }

        $device->language = $language;

        if (!$device->save()) {
            throw new ServerErrorHttpException(json_encode($device->errors));
        }

        return 'ok';
    }

    public function actionPurchase()
    {
        if (!$receipt = Yii::$app->request->post('receipt')) {
            throw new BadRequestHttpException('Invalid receipt param');
        }
        if (!$productId = Yii::$app->request->post('pid')) {
            throw new BadRequestHttpException('Empty pid param');
        }

        $device = $this->loadDevice();

        /** @var Product $product */
        if (!$product = Product::findOne(['pid' => $productId])) {
            throw new NotFoundHttpException('Product not found');
        }

        /** @var Product $product */
        if (Purchase::find()->andWhere(['receipt' => $receipt])->exists()) {
            throw new BadRequestHttpException('Purchase already exists');
        }

        $verifier = $this->getAppStoreVerifier();

        try {
            if (!$verifier->validateReceipt($receipt, $product->bid, $product->pid)) {
                throw new BadRequestHttpException('Receipt is not valid');
            }
        } catch (AppStoreException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if (!$device->save()) {
            throw new ServerErrorHttpException(json_encode($device->errors));
        }

        $purchase = new Purchase();
        $purchase->device_id = $device->id;
        $purchase->product_id = $product->id;
        $purchase->receipt = $receipt;
        $purchase->created_at = time();
        $purchase->expired_at = $product->days ? time() + $product->days * 3600 * 24 : null;
        $purchase->notified = false;

        if (!$purchase->save()) {
            throw new ServerErrorHttpException(json_encode($purchase->errors));
        }

        return 'ok';
    }

    public function actionFavorite($profile_id)
    {
        $query = Favorite::find()->where(['profile_id' => $profile_id]);
        return $query->all();
    }

    public function actionAddFavorite($profile_id, $company_id)
    {
        $favorite = new Favorite();
        $favorite->profile_id = $profile_id;
        $favorite->company_id = $company_id;

        if (!$favorite->save()) {
            throw new ServerErrorHttpException(json_encode($favorite->errors));
        }

        return 'ok';

    }
    public function actionDeleteFavorite($profile_id, $company_id)
    {
        $query = Favorite::find()->where(['company_id' => $company_id])->andWhere(['profile_id' => $profile_id])->one();
        $query->delete();
    }

    /**
     * @return bool
     */
    private function isPurchase()
    {
        return $this->loadDevice()->getPurchase();
    }

    /**
     * @return Device
     */
    private function loadDevice()
    {
        return Yii::$app->user->identity;
    }

    /**
     * @return \app\components\AppStoreVerifier
     */
    private function getAppStoreVerifier()
    {
        return Yii::$app->get('appStoreVerifier');
    }

    /**
     * @return \app\components\SmsSender
     */
    private function getSmsSender()
    {
        return Yii::$app->get('smsSender');
    }
}