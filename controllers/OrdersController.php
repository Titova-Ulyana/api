<?php
namespace app\controllers;
use app\models\Orders;
use app\models\Product;
use Yii;
use yii\filters\auth\HttpBearerAuth;
class OrdersController extends FunctionController
{
public $modelClass = 'app\models\Orders';

public function behaviors()
 {
    $behaviors = parent::behaviors();
    $behaviors['authenticator'] = [
    'class' => HttpBearerAuth::class,
    'only'=>['create', 'delete', 'change']
    ];
    return $behaviors;
 }


public function actionCreate()
{
    $data=\Yii::$app->request->post();
    $orders=new Orders();
    $orders->load($data, '');
    if (!$orders->validate()) return $this->validation($orders);
    $product=new Product($orders->getProduct()->one());
        if($orders->count > $product->count)
            {
                $error=['error'=> ['code'=>422, 'message'=>'Validation error',
                'errors'=>'this quantity of products is not available']];
                 return $this->send(422, $error);
            }
    else
    {
        $orders->save();
        $answer=['data'=>['status'=>'ОК', 'id'=>(int)$orders->id]]; 
        return $this->send(200, $answer);
    }
}


public function actionDelete($id)
{

    $orders= Orders::findOne($id);
    if($orders){
        if($orders->status_order == "В обработке")
        {
            $orders -> delete();
            $answer=['data'=>['status'=>'ОК']]; 
            return $this->send(200, $answer);
        }
        else 
        {
            $error=['error'=> ['code'=>403, 'message'=>'Access denied',
            'errors'=>'order status accepted or refused']];
            return $this->send(422, $error);
        }
    }
    return $this->send(404, $this->not_found);
}


public function actionChange($id)
{
    if (!$this->admin()) return $this->send(403, $this->auth_adm);

    $orders= Orders::findOne($id);
    $status = Yii::$app->request->getBodyParams();
    $orders->status_order=$status['status_order'];
    if (!$orders->validate()) return $this->validation($orders);
    $orders->save();

    $answer=['data'=>['status'=>'ОК']]; 
    return $this->send(200, $answer);
}



}