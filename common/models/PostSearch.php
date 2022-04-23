<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Post;

/**
 * 用于实现模型搜索功能
 * PostSearch represents the model behind the search form of `common\models\Post`.
 */
class PostSearch extends Post
{
    /**
     * 重写 attributes 方法，为模型类添加属性
     * 这里添加一个 authorName 属性解决文章列表页可以通过作者姓名来查询文章
     * @return array|string[]
     */
    public function attributes()
    {
        return array_merge(parent::attributes(), ['authorName']);
    }

    /**
     * 重写父类的 rules 方法
     * 父类中的 rules 定义的内容是为了模型创建和更新时进行数据校验的，
     * 而这里的 rules 定义的内容是为了对搜索表单的内容进行数据校验，可能校验的规则不一样，所以需要重写
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status', 'create_time', 'update_time', 'author_id'], 'integer'],
            [['title', 'content', 'tags', 'authorName'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Post::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            // 设置分页
            'pagination' => ['pageSize' => 5],
            // 设置排序
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
                // 默认 sort 里面不指定 attributes 的话，页面表头每个字段都可以点击更改排序方式
                // 如果设置 attributes 的话，表示显示指定，不在指定范围内的字段就无法点击更改排序
//                'attributes' => ['id', 'title'],
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // 如果验证不通过，并且不希望返回数据，可以放开下面这句 where 条件，默认是屏蔽的
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'post.id' => $this->id,
            'post.status' => $this->status,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
            'author_id' => $this->author_id,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'content', $this->content])
            ->andFilterWhere(['like', 'tags', $this->tags]);

        // 添加 authorName 查询，需要关联 adminuser 表进行查询
        $query->join('inner join', 'Adminuser', 'post.author_id = Adminuser.id');
        $query->andFilterWhere(['like', 'Adminuser.nickname', $this->authorName]);

        // 给 authorName 添加点击排序功能
        $dataProvider->sort->attributes['authorName'] = [
            'asc' => ['Adminuser.nickname' => SORT_ASC],
            'desc' => ['Adminuser.nickname' => SORT_DESC],
        ];

        return $dataProvider;
    }
}
