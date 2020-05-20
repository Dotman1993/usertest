<?php

namespace app\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\UsersRecord;
use app\models\CityRecord;
use app\models\SkillsRecord;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
					'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
		
		$query = UsersRecord::find()->with([
			'city',
			'skills' => function($q){
				$q->orderBy('skills.name');
			},
		]);

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => false,
			'sort' => false,
		]);
		
		if (Yii::$app->request->isAjax) {
			return $this->renderAjax('index', [
				'dataProvider' => $dataProvider,
			]);
		}
		
        return $this->render('index', [
			'dataProvider' => $dataProvider,
		]);
    }
	
	public function actionCreate($random = false)
    {
		
		$user = new UsersRecord();
		$city = new CityRecord();
		$skills = [new SkillsRecord()];
		
		$post = Yii::$app->request->post();
		
		if ($random) {
			$faker = \Faker\Factory::create('ru_RU');
			$faker->addProvider(new \app\components\Faker\ru_RU\Skills($faker));
			
			$post[$user->formName()] = ['name' => $faker->firstName];
			$post[$city->formName()] = ['name' => $faker->city];
			$post[$skills[0]->formName()] = [];
			
			for($i = 0; $i < rand(0, 10); $i++) {
				$post[$skills[0]->formName()][$i] = ['name' => $faker->skillName];
			}
		}
		
		$count = count($post[$skills[0]->formName()]);
		
		for($i = 1; $i < $count; $i++) {
			$skills[] = new SkillsRecord();
		}
		
		if ($user->load($post) &&
			$city->load($post))
		{
			\yii\base\Model::loadMultiple($skills, $post);
			
			$city_bd = $city::find()->andWhere(['name' => $city->name])->one();
			
			$city_is_valid = false;
			
			if ($city_bd !== null) {
				$city = $city_bd;
				$city_is_valid = true;
			} else {
				if ($city->save()) {
					$city_is_valid = true;
				}
			}
			
			if ($city_is_valid) {
			
				$user->city_id = $city->id;
				
				if($user->save()) {
				
					$skill_names = \yii\helpers\ArrayHelper::getColumn($skills, 'name');

					$skills_bd = SkillsRecord::find()->andWhere(['name' => $skill_names])
					->indexBy(function ($row) {
						return mb_strtolower($row['name'], 'UTF-8');
					})->all();
					
					foreach($skills as $k => $skill) {
						$key = mb_strtolower($skill['name'], 'UTF-8');
						if(!empty($skills_bd[$key])) {
							$user->link('skills', $skills_bd[$key]);
							unset($skills_bd[$key]);
						} else {
							if($skill->save()) {
								$user->link('skills', $skill);
							}
						}
					}
					
					if (Yii::$app->request->isAjax) {
						Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
						return ['success' => true];
					}
					
					return $this->redirect(['index']);
				}
			}
		}
		
		if (Yii::$app->request->isAjax) {
			return $this->renderAjax('create', [
				'user' => $user,
				'city' => $city,
				'skills' => $skills,
			]);
		}
		
		return $this->render('create', [
			'user' => $user,
			'city' => $city,
			'skills' => $skills,
		]);
    }
	
	public function actionDelete($id)
	{
		$this->findModel($id)->delete();
		
		if (Yii::$app->request->isAjax) {
			Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
			return ['success' => true];
		}
		return $this->redirect(['index']);
	}

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
	
	protected function findModel($id)
    {
		$model = UsersRecord::find()->andWhere(['id' => $id])->one();
		
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
