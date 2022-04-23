<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\CommentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '评论管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="comment-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php // Html::a('Create Comment', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            //'id',
            [
                'attribute' => 'id',
                'contentOptions' => ['width' => '30px']
            ],

            // 重写 content 字段的展示，超出 20 个字符用 ... 代替，这里有两种方式
            // 'content:ntext',
            [
                'attribute' => 'content',

                // 方式一：匿名函数设置 value 的方式
                // $model 当前行的对象
                // $key 当前行的键
                // $index 当前行的索引
                // $column 数据列的对象
                // 'value' => function ($model, $key, $index, $column) {
                //     $tmpStr = strip_tags($model->content);
                //     $tmpLen = mb_strlen($tmpStr);
                //     $retStr = mb_substr($tmpStr, 0, 20, 'utf-8');
                //     return $tmpLen > 20 ? $retStr . '...' : $retStr;
                // }

                // 方式二：在 comment 模型文件中使用 Getter 接口添加一个属性 begging，也就是实现一个 getBeginning 的方法
                // 这里就可以直接使用了
                'value' => 'beginning'
            ],

            //'userid',
            [
                'attribute' => 'user.username',
                'label' => '作者',
                'value' => 'user.username'
            ],

            //'status',
            [
                'attribute' => 'status',
                'value' => 'status0.name',
                'filter' => \common\models\Commentstatus::find()
                    ->select(['name', 'id'])
                    ->orderBy('position')
                    ->indexBy('id')
                    ->column(),
                'contentOptions' => function ($model) {
                    return ($model->status == 1) ? ['class' => 'bg-danger'] : [];
                }
            ],

            //'create_time:datetime',
            [
                'attribute' => 'create_time',
                'format' => ['date', 'php:Y-m-d H:i:s']
            ],

            //'email:email',
            //'url:url',
            //'post_id',
            'post.title',


            [
//                'class' => ActionColumn::className(),
//                'urlCreator' => function ($action, \common\models\Comment $model, $key, $index, $column) {
//                    return Url::toRoute([$action, 'id' => $model->id]);
//                }

                'class' => 'yii\grid\ActionColumn',
                // view update delete 是 ActionColumn 默认设置的，approve 是自己添加的审核按钮
                'template' => '{view} {update} {delete} {approve}',
                'buttons' => [
                    // $url 动作列为按钮创建的 url
                    // $model 当前要渲染的模型对象
                    // $key 数据提供者数组中模型的键
                    // 点击后对应的的 action 默认是当前控制器的 actionApprove 方法
                    'approve' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('yii', '审核'),
                            'aria-label' => Yii::t('yii', '审核'),
                            'data-confirm' => Yii::t('yii', '你确定通过这条评论吗？'),
                            'data-method' => 'post',
                            'data-pjax' => '0',
                        ];
                        // todo 课程中 span 引入样式的按钮在页面中无法显示，到 bootstrap 官网标签库中拿到 svg 的图标可显示
//                        $text = Html::tag('span', '', ['class' => "glyphicon glyphicon-list"]);
//                        return Html::a($text, $url, $options);
//                        return Html::a('<i class="bi bi-card-checklist"></i>', $url, $options);
                        return Html::a('<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-card-checklist" viewBox="0 0 16 16">
  <path d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h13zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-13z"/>
  <path d="M7 5.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm-1.496-.854a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 1 1 .708-.708l.146.147 1.146-1.147a.5.5 0 0 1 .708 0zM7 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm-1.496-.854a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 0 1 .708-.708l.146.147 1.146-1.147a.5.5 0 0 1 .708 0z"/>
</svg>', $url, $options);
                    }
                ],
                //'contentOptions' => ['width' => '100px']
            ],
        ],
    ]); ?>


</div>
