<?php
namespace app\controllers;
use app\models\Category;
use app\models\Product;

use Yii;
class CategoryController extends FunctionController
{
public $modelClass = 'app\models\Category';

public function actionCreate()
{



    //401/403/admin
    $data=Yii::$app->request->post();
    $category=new Category();
    $category->load($data, '');
    if (!$category->validate()) return $this->validation($category);
    $category->save();
    $answer=['data'=>['status'=>'ОК', 'id'=>(int)$category->id]]; 
    return $this->send(200, $answer);
}

public function actionDelete($id)
{


    //401/403/admin
    $category= Category::findOne($id);
    if(!$category) return $this->send(404, $this->not_found);
    $category->delete();
    $answer=['data'=>['status'=>'ОК']]; 
    return $this->send(200, $answer);
}

}