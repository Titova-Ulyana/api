<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $phone
 * @property string $email
 * @property string $password
 * @property string $token
 * @property int|null $admin
 *
 * @property Orders[] $orders
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }


    public static function findIdentity($id)
    {
   
    }

    public static function findIdentityByAccessToken($token, $type = null)
     {
     return static::findOne(['token' => $token]);
     }

     public function getId()
     {
     return $this->id;
     }
    

     public function getAuthKey()
    {

    }

    public function validateAuthKey($authKey)
    {
        
    }



    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['first_name', 'last_name', 'phone', 'email', 'password', 'token'], 'required'],
            [['admin'], 'boolean'],
            [['first_name', 'last_name'], 'string', 'max' => 100],
            [['phone'], 'string', 'max' => 20],
            [['email'], 'email'],
            [['password'], 'string', 'max' => 400],
            [['token'], 'string', 'max' => 250],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'phone' => 'Phone',
            'email' => 'Email',
            'password' => 'Password',
            'token' => 'Token',
            'admin' => 'Admin',
        ];
    }

    public function fields()
    {
        $fields = parent::fields();
        // удаляем небезопасные поля
        unset($fields['admin'], $fields['password'],
        $fields['token']);
        return $fields;
    }

    /**
     * Gets query for [[Orders]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Orders::class, ['user_id' => 'id']);
    }
}
