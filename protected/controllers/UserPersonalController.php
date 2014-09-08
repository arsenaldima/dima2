<?php

class UserPersonalController extends Controller
{

    public $layout='//layouts/column3';

	public function actionIndex($id)
	{

        $model= CmsUser::model()->findByPk($id);


		$this->render('index',array('model'=>$model,'id'=>$id));
	}
    public function actionAvatar()
    {
        $model=CmsUser::model()->findByPk(Yii::app()->user->id);
        $model->scenario='ava';

        if(isset($_POST['CmsUser']))
        {
                $model->attributes=$_POST['CmsUser'];

            if($model->validate())
            {
                $model->SaveImage();
               CmsUser::model()->updateByPk(Yii::app()->user->id,array('picture'=>$model->picture));}

            $this->redirect(array('/UserPersonal/index','id'=>Yii::app()->user->id));

        }
            $this->render('avatar',array('model'=>$model));
    }
    public function actionChangeEmail($id)
    {
        if($id==0)
        {
            if(CmsUser::sendChange())
                Yii::app()->user->setFlash('success','На ваш email отправлено письмо. Для смены email перейдите по ссылке в письме');
            else
                Yii::app()->user->setFlash('error','Письмо не отправленно');

            $flag=false;

        }
        else
        {
            if($id==Yii::app()->user->id)
            {
               $flag=true;

                if(isset($_POST['email']))
                {
                    if(CmsUser::model()->updateByPk(Yii::app()->user->id,array('email'=>$_POST['email'])))
                    {Yii::app()->user->setFlash('success','Ваш email изменён'); $flag=false;}
                    else
                        Yii::app()->user->setFlash('error','email не изменён');

                    $this->render('ChangeEmail',array('flag'=>$flag));
                    Yii::app()->end();
                }


            }
        }

        $this->render('ChangeEmail',array('flag'=>$flag));
    }

    //Приглошение друга
    public function actionPriglDruga()
    {


        if(isset($_POST['email']))
        {

            if(CmsUser::sendInvite($_POST['email']))
            {
                Yii::app()->user->setFlash('success','письмо успешно отправлено');

            }
            else
                Yii::app()->user->setFlash('error','письмо не отправлено');


                $this->render('PriglDruga');
                Yii::app()->end();
        }

        $this->render('PriglDruga');
    }
    public function actionChangePassword($id,$time)
    {
        $model=CmsSetting::model()->findByPk(1);

        if(($id==0)&&($time==0))
        {
            if(CmsUser::sendPas())
                Yii::app()->user->setFlash('success','На ваш email отправлено письмо. Для смены пароля перейдите по ссылке в письме');
            else
                Yii::app()->user->setFlash('error','Письмо не отправленно');

            $flag=false;

        }
        else
        {
            if($id==Yii::app()->user->id)
            {
                $flag=true;

                if($model->time<time()-$time)
                {


                    if(isset($_POST['password']))
                    {
                        if(CmsUser::model()->updateByPk(Yii::app()->user->id,array('password'=>md5('lkjhgfd'.$_POST['password']))))
                        {
                            $flag=false;
                            Yii::app()->user->setFlash('success','Ваш пароль изменён');
                        }
                        else
                            Yii::app()->user->setFlash('error','пароль не изменён');

                        $this->render('ChangePassword',array('flag'=>$flag));
                        Yii::app()->end();
                    }

                }
            }

        }

        $this->render('ChangePassword',array('flag'=>$flag));
    }
    public function actionCreate()
    {
        $model=new CmsPage;

        if(isset($_POST['ajax']) && $_POST['ajax']==='cms-page-form')
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }

        if(isset($_POST['CmsPage']))
        {

            $model->attributes=$_POST['CmsPage'];

            if(isset($_POST['data']))
            {
                if(!empty($_POST['data']))
                $model->created=strtotime($_POST['data']);
            }

      //     $purifier = new HTMLPurifier();
       //   $model->content = $purifier->purify($model->content);

             if($model->save())
                $this->redirect(array('/page/View','id'=>$model->id));

        }

        $this->render('create',array('model'=>$model, ));
    }

    public function actionUpdate($id)
    {
        $model=CmsPage::model()->findByPk($id);

        if(isset($_POST['ajax']) && $_POST['ajax']==='cms-page-form')
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }

        if(isset($_POST['CmsPage']))
        {
            if($model->validate())
            {


                $model->SaveImage();

                CmsPage::model()->updateByPk($id,array('path_img'=>$model->path_img,'content'=>$model->content, 'title'=>$model->title));

            }
                $this->redirect(array('index','id'=>Yii::app()->user->id));
        }

        $this->render('update',array(
            'model'=>$model,
        ));
    }
    public function actionAxjaxQuery()
    {
        $model=CmsUser::model()->findByPk(Yii::app()->user->id);


        if($model->podpis==0)
            return CmsUser::model()->updateByPk(Yii::app()->user->id,array('podpis'=>'1'));
        else
            return CmsUser::model()->updateByPk(Yii::app()->user->id,array('podpis'=>'0'));

    }
    public function actionAxjaxMail()
    {

        if(isset($_POST['text']))
        {
         return  CmsUser::sendSms($_POST['text'],$_POST['id']);
        }
    }

    public function actionUpload()
    {

        if (!empty($_FILES)) {

            $model = new CmsPage();
            $model->image=$_FILES['file'];

            $image=CUploadedFile::getInstance($model,'image');
            $model->image=$image;
            $rand=uniqid();
            $model->image->saveAs('./images/pages/'.$rand.$model->image->name);
            $model->path_img = $rand.$model->image->name;
            return true;
        }
        return false;
    }
}