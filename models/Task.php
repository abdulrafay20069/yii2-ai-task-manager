<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "task".
 *
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property string $status
 * @property int $priority
 * @property string|null $due_date
 * @property string|null $created_at
 */
class Task extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'task';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description', 'due_date'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 'todo'],
            [['priority'], 'default', 'value' => 2],
            [['title'], 'required'],
            [['description'], 'string'],
            [['priority'], 'integer'],
            [['due_date', 'created_at'], 'safe'],
            [['title'], 'string', 'max' => 150],
            [['status'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'description' => 'Description',
            'status' => 'Status',
            'priority' => 'Priority',
            'due_date' => 'Due Date',
            'created_at' => 'Created At',
        ];
    }

}
