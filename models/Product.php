<?php

namespace app\models;

use Yii;
use yii\web\UploadedFile;

/**
 * This is the model class for table "product".
 *
 * @property int $id
 * @property int $name_product
 * @property string|null $photo
 * @property float $price
 * @property int|null $discount
 * @property int $count
 * @property string $description
 * @property string $timestamp
 * @property int $category_id
 *
 * @property Category $category
 * @property Orders[] $orders
 */
class Product extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name_product', 'price', 'count', 'description', 'category_id'], 'required'],
            [['count', 'category_id'], 'integer'],
            [['price'], 'number'],
            [['discount'], 'compare', 'compareValue' => '0', 'operator' => '>=', 'type' => 'integer'],
            [['name_product', 'description'], 'string'],
            [['photo'], 'file', 'extensions' => ['png', 'jpg', 'gif', 'jpeg'], 'maxSize' => 1024*1024*2],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['category_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Id',
            'name_product' => 'Name Product',
            'photo' => 'Photo',
            'price' => 'Price',
            'discount' => 'Discount',
            'count' => 'Count',
            'description' => 'Description',
            'category_id' => 'Category ID',
        ];
    }

    public function beforeValidate()
    {
        $this->photo=UploadedFile::getInstanceByName('photo');
        return parent::beforeValidate();
    }
       

    public function fields()
{
$fields = parent::fields();
// удаляем небезопасные поля
unset($fields['category_id']);
return $fields;
}

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    /**
     * Gets query for [[Orders]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Orders::class, ['product_id' => 'id']);
    }
}
