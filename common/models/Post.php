<?php

namespace common\models;

use Yii;
use yii\helpers\Html;

/**
 * This is the model class for table "post".
 *
 * @property int $id
 * @property string $title
 * @property string $content
 * @property string|null $tags
 * @property int $status
 * @property int|null $create_time
 * @property int|null $update_time
 * @property int $author_id
 *
 * @property Adminuser $author
 * @property Comment[] $comments
 * @property Poststatus $status0
 */
class Post extends \yii\db\ActiveRecord
{
    /**
     * 用于保存数据中原有的 tag 信息
     * @var $_oldTags string
     */
    private $_oldTags;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'post';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'content', 'status', 'author_id'], 'required'],
            [['content', 'tags'], 'string'],
            [['status', 'create_time', 'update_time', 'author_id'], 'integer'],
            [['title'], 'string', 'max' => 128],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => Adminuser::className(), 'targetAttribute' => ['author_id' => 'id']],
            [['status'], 'exist', 'skipOnError' => true, 'targetClass' => Poststatus::className(), 'targetAttribute' => ['status' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
//        return [
//            'id' => 'ID',
//            'title' => 'Title',
//            'content' => 'Content',
//            'tags' => 'Tags',
//            'status' => 'Status',
//            'create_time' => 'Create Time',
//            'update_time' => 'Update Time',
//            'author_id' => 'Author ID',
//        ];
        return [
            'id' => 'ID',
            'title' => '标题',
            'content' => '内容',
            'tags' => '标签',
            'status' => '状态',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'author_id' => '作者',
        ];
    }

    /**
     * Gets query for [[Author]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(Adminuser::className(), ['id' => 'author_id']);
    }

    /**
     * Gets query for [[Comments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(Comment::className(), ['post_id' => 'id']);
    }

    /**
     * Gets query for [[Status0]].
     * 为什么方法名里面会有一个 0?
     * 首先这部分代码是通过 gii 脚手架生成的，由于当前 post 表中本身有一个字段叫做 status，
     * 而这里定义的是与 poststatus 表的关联，为了区分字段和关联表，不被混淆，特地加上一个 0 做区分。
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStatus0()
    {
        return $this->hasOne(Poststatus::className(), ['id' => 'status']);
    }

    /**
     * 重写 save 的声明周期函数
     * @param bool $insert 表示调用此方法是是否是新创建数据，还是更新数据
     * @return bool
     */
    public function beforeSave($insert)
    {
        // 一定要调用父类的 beforeSave 方法，保证父类的代码会被执行
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->create_time = time();
                $this->update_time = time();
            } else {
                $this->update_time = time();
            }

            return true;
        }

        return false;
    }

    /**
     * 重写 find() 方法生命周期的 afterFind() 方法
     */
    public function afterFind()
    {
        parent::afterFind();
        $this->_oldTags = $this->tags;
    }

    /**
     * 重写 save() 方法生命周期的 afterSave() 方法，处理标签云相关的功能
     * @param bool $insert
     * @param array $changedAttributes
     * @throws \yii\db\StaleObjectException
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        Tag::updateFrequency($this->_oldTags, $this->tags);
    }

    /**
     * 重写 delete() 方法生命周期的 afterDelete() 方法，处理标签云相关的功能
     * @throws \yii\db\StaleObjectException
     */
    public function afterDelete()
    {
        parent::afterDelete();
        Tag::updateFrequency($this->tags, '');
    }

    /**
     * 使用 urlManager 组件创建一个 url
     * @return string
     */
    public function getUrl()
    {
        return Yii::$app->urlManager->createUrl(['post/detail', 'id' => $this->id, 'title' => $this->title]);
    }

    /**
     * 用 Getter 接口的方式定义一个属性
     * @return string
     */
    public function getBeginning($length= 288)
    {
        $tmpStr = strip_tags($this->content);
        $tmpLen = mb_strlen($tmpStr);
        $retStr = mb_substr($tmpStr, 0, $length, 'utf-8');
        return $tmpLen > 10 ? $retStr . '...' : $retStr;
    }

    /**
     * 获取标签
     * @return mixed
     */
    public function getTagLinks()
    {
        $links = [];
        foreach (Tag::string2array($this->tags) as $tag) {
            $links[] = Html::a(Html::encode($tag), ['post/index', 'PostSearch[tags]' => $tag]);
        }
        return $links;
    }

    /**
     * 获取文章的评论条数
     * @return bool|int|string|null
     */
    public function getCommentCount()
    {
        return Comment::find()->where(['post_id' => $this->id, 'status' => 2])->count();
    }
}
