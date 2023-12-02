<?php
namespace app\controllers;
use yii\rest\Controller;
use yii\web\Response;
use yii\widgets\ActiveForm;
class FunctionController extends Controller{


public $not_found = ["error"=>["code"=> 404, "messege"=>"Item not found"]];

public $unauth = ["error"=>["code"=> 401, "messege"=>"Unauthorized"]];

public $auth_adm = ["error"=>["code"=> 403, "messege"=>"Access denied"]];



 public function send($code, $data){
 $response=$this->response;
 $response->format = Response::FORMAT_JSON;
 $response->data=$data;
 $response->statusCode=$code;
 return $response;
 }

 public function validation($model){
 $error=['error'=> ['code'=>422, 'message'=>'Validation error',
'errors'=>ActiveForm::validate($model)]];
 return $this->send(422, $error);
 }

 public function is_admin(){
    if (Yii::$app->user->identity->is_admin==1) return true; else return false;
     /*Через тернарный оператор*/
  //  return Yii::$app->user->identity->is_admin==1 ? true : false;
 }


}
