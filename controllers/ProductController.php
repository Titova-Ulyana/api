<?php
namespace app\controllers;
use app\models\Product;

use Yii;
use yii\web\UploadedFile;
class ProductController extends FunctionController
{
public $modelClass = 'app\models\Product';

public function actionCreate()
{



    //401/403/admin
    $data=Yii::$app->request->post();
    $product=new Product();
    $product->load($data, '');
    if (!$product->validate()) return $this->validation($product);

    $product->photo=UploadedFile::getInstanceByName('photo');
    $hash=hash('sha256', $product->photo->baseName) . '.' . $product->photo->extension;
    $product->photo->saveAs(\Yii::$app->basePath. '/assets/upload/' . $hash);
    $product->photo=$hash;
    $product->save(false);
    $answer=['data'=>['status'=>'ОК', 'id'=>(int)$product->id]]; 
    return $this->send(200, $answer);
}

public function actionView($id)
{
    $product= Product::findOne($id);
    if ($product) {

    $answer=['data'=>['status'=>'ОК', 'product'=>$product]];
    return $this->send(200, $answer);
    }
    return $this->send(404, $this->not_found);
}

public function actionIndex()
{
    $products= Product::find()->all();
    if ($products) {
      //убрать поля  
    $answer=['data'=>['status'=>'ОК', 'product'=>$products]]; 
    return $this->send(200, $answer);
    }
    return $this->send(404, $this->not_found);
}

public function actionDelete($id)
{
    



    //401/403/admin
    $product = Product::findOne($id);
    if(!$product) return $this->send(404, $this->not_found);
    $product->delete();
    $answer=['data'=>['status'=>'ОК']]; 
    return $this->send(200, $answer);
}


public function actionChange($id)
{
    



    //401/403/admin
    $product = Product::findOne($id);
    if(!$product) return $this->send(404, $this->not_found);
    $data = Yii::$app->request->getBodyParams();
    
    /*$product->photo=UploadedFile::getInstanceByName('photo');
    $hash=hash('sha256', $product->photo->baseName) . '.' . $product->photo->extension;
    $product->photo->saveAs(\Yii::$app->basePath. '/assets/upload/' . $hash);
    $product->photo=$hash;*/

    //$product->photo=$data['photo'];
    
    $product->price=$data['price'];
    $product->discount=$data['discount'];
    $product->count=$data['count'];

    if (!$product->validate()) return $this->validation($product);
    $product->save(false);




    $answer=['data'=>['status'=>'ОК']]; 
    return $this->send(200, $answer);
}



}