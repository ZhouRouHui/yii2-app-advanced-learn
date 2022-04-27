<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "comment".
 *
 * @property int $id
 * @property string $content
 * @property int $status
 * @property int|null $create_time
 * @property int $userid
 * @property string $email
 * @property string|null $url
 * @property int $post_id
 * @property int|null $remind 0未提醒 1已提醒
 *
 * @property Post $post
 * @property Commentstatus $status0
 * @property User $user
 */
class Comment extends \yii\db\ActiveRecord
{
    public function attributes()
    {
        return array_merge(parent::attributes(), ['user.username']);
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'comment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['content', 'status', 'userid', 'email', 'post_id'], 'required'],
            [['content'], 'string'],
            [['status', 'create_time', 'userid', 'post_id', 'remind'], 'integer'],
            [['email', 'url'], 'string', 'max' => 128],
            [['post_id'], 'exist', 'skipOnError' => true, 'targetClass' => Post::className(), 'targetAttribute' => ['post_id' => 'id']],
            [['status'], 'exist', 'skipOnError' => true, 'targetClass' => Commentstatus::className(), 'targetAttribute' => ['status' => 'id']],
            [['userid'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['userid' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
//        return [
//            'id' => 'ID',
//            'content' => 'Content',
//            'status' => 'Status',
//            'create_time' => 'Create Time',
//            'userid' => 'Userid',
//            'email' => 'Email',
//            'url' => 'Url',
//            'post_id' => 'Post ID',
//        ];
        return [
            'id' => 'ID',
            'content' => '内容',
            'status' => '状态',
            'create_time' => '创建时间',
            'userid' => '用户',
            'email' => '邮箱',
            'url' => '链接地址',
            'post_id' => '所属文章',
            'remind' => '是否提醒',
        ];
    }

    /**
     * Gets query for [[Post]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPost()
    {
        return $this->hasOne(Post::className(), ['id' => 'post_id']);
    }

    /**
     * Gets query for [[Status0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStatus0()
    {
        return $this->hasOne(Commentstatus::className(), ['id' => 'status']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'userid']);
    }

    /**
     * 用 Getter 接口的方式定义一个属性
     * @return string
     */
    public function getBeginning()
    {
        $tmpStr = strip_tags($this->content);
        $tmpLen = mb_strlen($tmpStr);
        $retStr = mb_substr($tmpStr, 0, 10, 'utf-8');
        return $tmpLen > 10 ? $retStr . '...' : $retStr;
    }

    /**
     * 审核功能
     * @return bool
     */
    public function approve()
    {
        $this->status = 2;
        return $this->save();
    }

    /**
     * getter 方式添加属性，获取带审核的评论数量
     * @return bool|int|string|null
     */
    public static function getPendingCommentCount()
    {
        return self::find()->where(['status' => 1])->count();
    }

    /**
     * 重写 beforeSave，定义 create_time 字段
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->create_time = time();
            }
            return true;
        }
        return false;
    }

    /**
     * 获取最近的评论
     * @param int $limit
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function findRecentComments($limit = 10)
    {
        return Comment::find()->where(['status' => 2])->orderBy('create_time desc')->limit($limit)->all();
    }
}
