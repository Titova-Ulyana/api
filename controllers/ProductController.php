<?php
namespace app\controllers;
use app\models\Category;
use app\models\EditProduct;
use app\models\Product;

use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\web\UploadedFile;
class ProductController extends FunctionController
{
public $modelClass = 'app\models\Product';

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
    $product=new Product();
    $product->load(Yii::$app->request->post(), '');

    if (!$this->admin()) return $this->send(403, $this->auth_adm);

    if (!$product->validate()) return $this->validation($product);
    if (UploadedFile::getInstanceByName('photo')){
    $product->photo=UploadedFile::getInstanceByName('photo');
    $hash=hash('sha256', $product->photo->baseName) . '.' . $product->photo->extension;
    $product->photo->saveAs(\Yii::$app->basePath. '/assets/upload/' . $hash);
    $product->photo=$hash;
    }
    $product->save(false);
    $answer=['data'=>['status'=>'ОК', 'id'=>(int)$product->id]]; 
    return $this->send(200, $answer);
}

public function actionView($id)
{
    $product= Product::findOne($id);
    if (!$product) return $this->send(404, $this->not_found);

    $category=new Category($product->getCategory()->one());
    $product->category_id = $category->name_category;
    $answer=['data'=>['product'=>$product]];
    return $this->send(200, $answer);
    
}

public function actionIndex()
{
    $products= Product::find()->select(['id', 'name_product', 'photo', 'price', 'count'])->all();
    if ($products) 
    {

          $answer=['data'=>['products'=>$products]]; 
          return $this->send(200, $answer);
    }
    return $this->send(404, $this->not_found);

}
public function actionDelete($id)
{
    $product = Product::findOne($id);
    if(!$product) return $this->send(404, $this->not_found);
    if (!$this->admin()) return $this->send(403, $this->auth_adm);

    $product->delete();
    $answer=['data'=>['status'=>'ОК']]; 
    return $this->send(200, $answer);
}


public function actionChange($id)
{

    if (!$this->admin()) return $this->send(403, $this->auth_adm);

    $product = Product::findOne($id);
    if(!$product) return $this->send(404, $this->not_found);
    $product->load(Yii::$app->request->post(), '');

    if (UploadedFile::getInstanceByName('photo')){
       
        $url=Yii::$app->basePath.$product->photo;
         $product->photo = UploadedFile::getInstanceByName('photo');
        
         if (!$product->validate()) return $this->validation($product);
         @unlink($url);
 
         $photo_name='/assets/upload/photo_product_' . Yii::$app->getSecurity()->generateRandomString(40) .'.'. $product->photo->extension;
        
         $product->photo->saveAs(Yii::$app->basePath.$photo_name);
         $product->photo=$photo_name; 
        }
         else {
             $editproduct=new EditProduct($product);    
             if (!$editproduct->validate()) return $this->validation($editproduct);
         }
     
         $product->save(false);
    $answer=['data'=>['status'=>'ОК']]; 
    return $this->send(200, $answer);
}

}