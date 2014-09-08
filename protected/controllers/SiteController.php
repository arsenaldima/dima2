<?php

class SiteController extends Controller
{
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
		$this->render('index');
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}

	/**
	 * Displays the contact page
	 */



    public function actionRegistration($id)
    {



        $model=new CmsUser;
        $model->scenario='registration';
        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);




        if(isset($_POST['CmsUser']))
        {
            $model->attributes=$_POST['CmsUser'];

            if($id!=0)
                $model->prigl_id=$id;

            if($model->save())
            {
               if(mkdir('./images/'.$model->id.'/'))
               {
                   mkdir('./images/'.$model->id.'/'.'avatars/');
                   mkdir('./images/'.$model->id.'/'.'pages/');
               }
                $this->redirect(array('login'));
            }
        }

        $this->render('registration',array(
            'model'=>$model,
        ));
    }


	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{

        /**
         * Авторизация по токену для восстановления пароля
         */
        if ($recoveryPasswordToken = Yii::app()->request->getParam('token'))
        {

           if ($user = CmsUser::getByRecoveryPasswordToken($recoveryPasswordToken))
            {
                $log=new LoginForm();
                $log->username=$user->username;
                $log->password=$user->password;
                $log->login();
                $this->redirect($this->createAbsoluteUrl('UserPersonal/index', array('id' =>$user->id )));

            }
        }


        $service = Yii::app()->request->getQuery('service');
        if (isset($service)) {
            $authIdentity = Yii::app()->eauth->getIdentity($service);
            $authIdentity->redirectUrl = Yii::app()->user->returnUrl;
            $authIdentity->cancelUrl = $this->createAbsoluteUrl('site/login');

            if ($authIdentity->authenticate()) {
                $identity = new ServiceUserIdentity($authIdentity);

                // Успешный вход
                if ($identity->authenticate()) {
                    Yii::app()->user->login($identity);

                    // Специальный редирект с закрытием popup окна
                    $authIdentity->redirect();
                }
                else {
                    // Закрываем popup окно и перенаправляем на cancelUrl
                    $authIdentity->cancel();
                }
            }
            $this->redirect(array('site/login'));
        }
            //авторизация с помошью соц сетей


		$model=new LoginForm();

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
           // $model1=CmsUser::model()->findByAttributes(array('username'=>$model->username));


            $model_set=CmsSetting::model()->findByPk(1);

            if($model_set->podtv_email==1)
                {
                        $user= CmsUser::model()->findByAttributes(array('username'=>$model->username));
                        $user->sendRecoveryPasswordMessage();
                        $this->render('login',array('model'=>$model,'flag'=>true));
                        Yii::app()->end();

                }
            else
                if($model->validate() && $model->login())
                  {
                    $this->redirect(array('UserPersonal/index','id'=>Yii::app()->user->id));
                  }

        }

		// display the login form
		$this->render('login',array('model'=>$model));
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->user->returnUrl);
	}



}