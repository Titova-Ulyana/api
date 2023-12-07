<?php
namespace app\controllers;
use app\models\User;
use app\models\Product;
use app\models\Orders;
use app\models\Category;
use app\models\LoginForm;




use Yii;
use yii\filters\auth\HttpBearerAuth;
class UserController extends FunctionController 
{
public $modelClass = 'app\models\User';

public function behaviors()
 {
    $behaviors = parent::behaviors();
    $behaviors['authenticator'] = [
    'class' => HttpBearerAuth::class,
    'only'=>['view']
    ];
    return $behaviors;
 }


public function actionCreate()
{
    $data=Yii::$app->request->post();
    $user=new User();
    $user->load($data, '');
    $user->password=Yii::$app->getSecurity()->generatePasswordHash($user->password);
    $user->token=Yii::$app->getSecurity()->generateRandomString(80);
    if (!$user->validate()) return $this->validation($user);
    $user->save();
    $data=['data'=>['status'=>'ОК', 'id'=>(int)$user->id]]; 
    return $this->send(200, $data);
}

public function actionLogin()
{
    $data=\Yii::$app->request->post();
    $login_data=new LoginForm();
    $login_data->load($data, '');

   if (!$login_data->validate()) return $this->validation($login_data);
    $user=User::find()->where(['email'=>$login_data->email])->one();
   if (!is_null($user)) {
    if (\Yii::$app->getSecurity()->validatePassword($login_data->password, $user->password)) {
    $token = \Yii::$app->getSecurity()->generateRandomString(80);
    $user->token = $token;
    $user->save(false);//false — произвести запись без валидации

    $data = ['data' => ['token' => $token]];
    return $this->send(200, $data);
    }
    }    
    return $this->send(401, $this->unauth);
}
   


   public function actionView()
{
    
    $user=Yii::$app->user->identity;
    $orders=$user->getOrders()->all();
        $total_price=0;
        $orderItems='';
        $in_process=[];
        $accepted=[];
        $refual=[];
        if (!is_null($user)) {
      foreach ($orders as $order){
            $order=new Orders($order);
            $product=new Product($order->getProduct()->one());
            $category=new Category($product->getCategory()->one());
            $price=(double)$product->price;
            $discount=(double)$product->discount;
            $total_price=$order->count*($price*(100-$discount)/100);
            $product->category_id = $category->name_category;
            $orderItems=['name_product'=>$product->name_product, 'photo'=>$product->photo, 'price'=>$total_price, 'discount'=>$product->discount, 'count'=>$order->count, 'category'=>$product->category_id,  'description'=>$product->description, 'status_order'=>$order->status_order];
            
                switch ($order->status_order){
                    case 'В обработке': 
                        $in_process[]=$orderItems;
                        $orderItems=[];
                        break;
                    case 'Подтвержден': 
                        $accepted[]=$orderItems;
                        $orderItems=[];
                        break;
                    case 'Отменен': 
                        $refual[]=$orderItems;
                        $orderItems=[];
                        break;
                        
                }
        }

    $answer=['data'=>['user'=>$user, 'orders'=>['in_process'=>$in_process, 'accepted'=>$accepted, 'refual'=>$refual]]]; 
    return $this->send(200, $answer);
        }
        return $this->send(404, $this->not_found);

}


}
