<?php
/**
 * Created by JetBrains PhpStorm.
 * User: дима
 * Date: 03.09.14
 * Time: 10:51
 * To change this template use File | Settings | File Templates.
 */

class PageController extends ApiController
{

     public function actionUpdate($id)
       {
           $model=$this->loadModel($id);
           $model->SaveImage();
           CmsPage::model()->updateByPk($id,array('path_img'=>$model->path_img,'content'=>$model->content, 'title'=>$model->title, 'category'=>$model->category_id));
           $this->sendResponse($model);
          }

       /**
        * Deletes a particular model.
        * If deletion is successful, the browser will be redirected to the 'admin' page.
        * @param integer $id the ID of the model to be deleted
        */
    public function actionDelete($id)
    {

        $this->loadModel($id)->delete();
        $this->sendResponse(self::STATUS_OK);

    }


    public function actionCreate()
    {
        $model=new CmsPage;

        $model->attributes=$_POST;
        $model->SaveImage();

         if($model->save())
             $this->sendResponse($model);

    }


    public function actionIndex($id)//получить пост по id
    {

       $this->sendResponse(200,CmsPage::model()->findByPk($id));

    }

    public function actionMyPost()//получить мои посты
    {

        $this->sendResponse(200,CmsPage::model()->findAllByAttributes(array('user_id'=>Yii::app()->user->id)));

    }

    public function loadModel($id)
    {
        $model=CmsPage::model()->findByPk($id);
        if($model===null)
            throw new CHttpException(404,'The requested page does not exist.');
        return $model;
    }

}