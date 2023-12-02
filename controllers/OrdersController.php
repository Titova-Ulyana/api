<?php
namespace app\controllers;
use app\models\Orders;
use Yii;
class OrdersController extends FunctionController
{
public $modelClass = 'app\models\Orders';

public function actionCreate()
{


    //401/403/admin
    $data=Yii::$app->request->post();
    $orders=new Orders();
    $orders->load($data, '');
    if (!$orders->validate()) return $this->validation($orders);
    $orders->save();
    $answer=['data'=>['status'=>'ОК', 'id'=>(int)$orders->id]]; 
    return $this->send(200, $answer);
}

public function actionDelete($id)
{


    //401/403/admin
    $orders= Orders::findOne($id);
    if($orders){
    $orders -> delete();
    $answer=['data'=>['status'=>'ОК']]; 
    return $this->send(200, $answer);
    }
    return $this->send(404, $this->not_found);
}


public function actionChange($id)
{





    
    //401/403/admin
    $orders= Orders::findOne($id);
    $status = Yii::$app->request->getBodyParams();
    $orders->status_order=$status['status_order'];
    if (!$orders->validate()) return $this->validation($orders);
    $orders->save();

    $answer=['data'=>['status'=>'ОК']]; 
    return $this->send(200, $answer);
}



}