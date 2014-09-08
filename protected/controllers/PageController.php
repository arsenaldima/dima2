<?php

class PageController extends Controller
{


	public function actionIndex($id)
	{

        $category= CmsCategory::model()->findByPk($id);

        $criteria= new CDbCriteria;

        $criteria->condition = 'status = 2 AND category_id = '.$category->id.' AND '.'created < '.time();

        $model=CmsSetting::model()->findByPk(1);
        $prow= new CActiveDataProvider('CmsPage',array('criteria'=>$criteria,'pagination'=>array('pageSize'=>$model->ct_page),));

		$this->render('index',array('category'=>$category, 'data'=>$prow));
	}

    public function actionView($id)
    {
        $model= CmsPage::model()->findByPk($id);
        $model1 = new CmsComment();
        $ar=$model1->getCommentsTree($id);

        if(isset($_POST['CmsComment']))
        {
            $model1->page_id=$id;

            if(!Yii::app()->user->isGuest)
                $model1->user_id=Yii::app()->user->id;// esli polzovatel ne gost tokda soxranaem ego id

            $model1->attributes=$_POST['CmsComment'];

            if($model1->save())
            {
                if(($model1->parent_id!=null)&&(!Yii::app()->user->isGuest))
                {
                    CmsComment::sendOtvet($model1->parent_id);
                }
            $this->refresh();
            }
        }

        if(Yii::app()->user->isGuest)
            $model1->scenario='ComSet';

        $this->render('view',array('model1'=> $model1,'model'=> $model,'comments'=>$ar));
    }

public function actionDelete($id)
{
    $model=CmsComment::model()->findByPk($id);
    if(CmsComment::model()->deleteByPk($id))
    $this->redirect(array('/page/view','id'=>$model->page_id));

}

    public function actionPageCriteria()
    {
        if($dataStr=Yii::app()->request->getParam('data'))
        {
            if(!empty($dataStr))
            {
            $data=strtotime($dataStr);
                if($data<=time())
                {
                    $criteria= new CDbCriteria;
                    $criteria->condition = 'status = 2';
                    $criteria->addBetweenCondition('created',$data+1,$data+86399);//1 nachalo dna 1409864400 1409950800
                    $model=CmsSetting::model()->findByPk(1);
                    $prow= new CActiveDataProvider('CmsPage',array('criteria'=>$criteria,'pagination'=>array('pageSize'=>$model->ct_page)));
                }else
                {
                    $criteria= new CDbCriteria;
                    $criteria->condition = 'status = 2 AND created < '.time();

                    $model=CmsSetting::model()->findByPk(1);
                    $prow= new CActiveDataProvider('CmsPage',array('criteria'=>$criteria,'pagination'=>array('pageSize'=>$model->ct_page)));
                    $dataStr=null;
                }
             }
            $this->render('index',array('data'=> $prow, 'val'=>$dataStr));
        }


    }
    public function actionAjaxComment()
    {


        $model=CmsUser::model()->findByPk(Yii::app()->user->id);


        if($model->podpis==0)
            return CmsUser::model()->updateByPk(Yii::app()->user->id,array('podpis'=>'1'));
        else
            return CmsUser::model()->updateByPk(Yii::app()->user->id,array('podpis'=>'0'));

          /*  if(isset($_POST['content']))
            {
             $model=new CmsComment();
                if(Yii::app()->user->isGuest)
                    $model->scenario='ComSet';

                $model->content=$_POST['content'];

                if(isset($_POST['email']))
                    $model->guest=$_POST['email'];
                else
                    $model->user_id=Yii::app()->user->id;
                $model->parent_id=$_POST['parent'];

                if($model->save())
                   return true;
                else
                    return false;
            }
            return false;

        $model=new CmsComment();
        $model->content="asdfasdfsdf";
        $model->page_id=1;
        $model->user_id=2;
        $model->save();*/
    }
}