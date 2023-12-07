<?php

namespace app\models;

use Yii;
//use yii\web\UploadedFile;

/**
 * This is the model class for table "product".
 *
 * @property int $id
 * @property string $name_product
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
class EditProduct extends \yii\db\ActiveRecord
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
            [['price'], 'number', 'min' => 0],
            [['discount'], 'number', 'min' => 0],
            [['name_product', 'description'], 'string'],
            [['photo'], 'string', 'max' => 250],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['category_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    /*public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name_product' => 'Name Product',
            'photo' => 'Photo',
            'price' => 'Price',
            'discount' => 'Discount',
            'count' => 'Count',
            'description' => 'Description',
            'timestamp' => 'Timestamp',
            'category_id' => 'Category ID',
        ];
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
