<?php
/**
 * Created by JetBrains PhpStorm.
 * User: дима
 * Date: 01.09.14
 * Time: 12:43
 * To change this template use File | Settings | File Templates.
 */
class UserController extends ApiController
{


    public function actionAuth()
    {
        $loginForm = new LoginForm();

        if (empty($_POST['username']))
            $errors['username'] = 'Поле Логин должно быть заполнено';

        if (empty($_POST['password']))
            $errors['password'] = 'Поле Пароль должно быть заполнено';

        if (!empty($errors))
            $this->sendResponse(self::STATUS_BAD_REQUEST, $errors);

        $loginForm->attributes = $_POST;

        if ($loginForm->login())
        {
            $user = CmsUser::model()->findByPk(Yii::app()->user->id);
            $this->actionResponse = array(
                'user' =>  $user,
                'token' => CmsUser::getAuthToken(Yii::app()->user->id, Yii::app()->user->name),
            );
        }
    }//Авторизация пользователя

    public function actionRegistration($id)
{
    $model=new CmsUser;
    //$model->scenario='registration';

   if($id!=0)
       $model->prigl_id=$id;


    $model->attributes = $_POST;



        if($model->save())
        {
            $loginForm = new LoginForm();

            $loginForm->username=$model->username;
            $loginForm->password=$_POST['password'];

            if ($loginForm->login())
            {
                $user = CmsUser::model()->findByPk(Yii::app()->user->id);
                $this->actionResponse = array(
                    'user' =>  $user,
                    'token' => CmsUser::getAuthToken(Yii::app()->user->id, Yii::app()->user->name),
                );
            }

        }
else

    $this->sendResponse(self::STATUS_BAD_REQUEST, "Пользователь с таким ником уже существует");


}//Регистрация нового пользователя

    public function actionIndex($userId)
    {
        $this->actionResponse = CmsUser::model()->findByPk($userId);
    }//Информация о пользователя по id

    public function actionPriglPolzovatela()
    {
        if (empty($_POST['email']))
            $errors['email'] = 'Поле email должно быть заполнено';

        if (!empty($errors))
            $this->sendResponse(self::STATUS_BAD_REQUEST, $errors);

        if(CmsUser::sendInvite($_POST['email']))
        {

            $this->sendResponse(self::STATUS_OK,"email отправлен");

        }
        else
            $this->sendResponse(self::STATUS_INTERNAL_SERVER_ERROR,"email не отправлен");
    }//Приглашения пользователя

    public function actionEditChangeEmail()
    {

        $id=Yii::app()->request->getParam('id');

     if(!Yii::app()->request->getParam('id'))
        if(CmsUser::sendChange())
        {

            $this->sendResponse(self::STATUS_OK,"email отправлен");

        }
        else
            $this->sendResponse(self::STATUS_INTERNAL_SERVER_ERROR,"email не отправлен");
       else
           if($id==Yii::app()->user->id)
           {
               $flag=true;

               if(isset($_POST['email']))
               {
                   if(CmsUser::model()->updateByPk(Yii::app()->user->id,array('email'=>$_POST['email'])))
                   {
                       $this->sendResponse(self::STATUS_OK,"Пароль изменён");}
                   else
                       $this->sendResponse(self::STATUS_INTERNAL_SERVER_ERROR,"Пароль yt изменён");
               }


           }
    }//Смена email

     public function actionSendSms()
    {

        if (empty($_POST['id']))
            $errors['id'] = 'Поле email должно быть заполнено';

        if (empty($_POST['text']))
            $errors['text'] = 'Поле email должно быть заполнено';

        if (!empty($errors))
            $this->sendResponse(self::STATUS_BAD_REQUEST, $errors);

        if(CmsUser::sendSms($_POST['text'],$_POST['id']))
        {

            $this->sendResponse(self::STATUS_OK,"email отправлен");

        }
        else
            $this->sendResponse(self::STATUS_INTERNAL_SERVER_ERROR,"email не отправлен");
    }//Отправка личного сообщения

    public function actionEditPassword(){

        $model=CmsSetting::model()->findByPk(1);

        if((!Yii::app()->request->getParam('id'))&&(!Yii::app()->request->getParam('time')))
        {
            if(CmsUser::sendPas())
                $this->sendResponse(self::STATUS_OK,"email отправлен");
           else
                $this->sendResponse(self::STATUS_INTERNAL_SERVER_ERROR,"email не отправлен");
        }
        else
        {
            $id=Yii::app()->request->getParam('id');
            $time=Yii::app()->request->getParam('time');

            if($id==Yii::app()->user->id)
            {

                if($model->time<time()-$time)
                {
                    if(isset($_POST['password']))
                    {
                        if(CmsUser::model()->updateByPk(Yii::app()->user->id,array('password'=>md5('lkjhgfd'.$_POST['password']))))
                        {
                            $this->actionResponse = array(
                                'user' =>  CmsUser::model()->findByPk(Yii::app()->user->id),
                            );
                        }
                        else
                            $this->sendResponse(self::STATUS_INTERNAL_SERVER_ERROR,"пароль изменён");
                    }

                }
            }
        }

    }//Смена пароля

    public function actionChangeAvatar()
    {
        $model=CmsUser::model()->findByPk(Yii::app()->user->id);
        $model->scenario='ava';
        $model->attributes=$_POST['CmsUser'];

            if($model->validate())
            {   $model->SaveImage();
                CmsUser::model()->updateByPk(Yii::app()->user->id,array('picture'=>$model->picture));
            return $this->sendResponse(self::STATUS_OK,CmsUser::model()->findByPk(Yii::app()->user->id));
            }
        else
            return $this->sendResponse(self::STATUS_BAD_REQUEST,"");

    }//Смена аватара

}