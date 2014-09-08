<?php
/**
 * Created by JetBrains PhpStorm.
 * User: дима
 * Date: 07.09.14
 * Time: 10:04
 * To change this template use File | Settings | File Templates.
 */
class MessageController extends ApiController
{

    public function actionIndex()
    {
        if($id=Yii::app()->request->getParam('id'))
        {
            return $this->sendResponse(self::STATUS_OK,CmsComment::model()->findAllByAttributes(array('page_id'=>$id)));
        }
        else
            return $this->sendResponse(self::STATUS_BAD_REQUEST,"Недостаточно параметров");
    }//Получения коментариев по id страницы

    public function actionMyComments()
    {
        return $this->sendResponse(self::STATUS_OK,CmsComment::model()->findAllByAttributes(array('user_id'=>Yii::app()->user->id)));
    }//Получение коментариев текущего пользователя

    public function actionNewComment()
    {
        $model=new CmsComment();
        $model->attributes=$_POST;
        $model->page_id=Yii::app()->request->getParam('id');

        if(!Yii::app()->user->isGuest)
            $model->user_id=Yii::app()->user->id;
        if($model->save())
            return $this->sendResponse(self::STATUS_OK,$model);
        else
            return $this->sendResponse(self::STATUS_BAD_REQUEST,"Данные не корректны");
    }//Создание нового комментария

    public function actionDeleteComment()
{
    if($id=Yii::app()->request->getParam('id'))
        return $this->sendResponse(self::STATUS_OK,CmsComment::model()->deleteByPk($id));
    else
        return $this->sendResponse(self::STATUS_INTERNAL_SERVER_ERROR,"Запись не удалена");
}//Удалить комментария по id


}