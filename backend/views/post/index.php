<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\PostSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '文章管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="post-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('新增文章', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php
    // 搜索框另外一种展示方式，不在列表表格内
    // echo $this->render('_search', ['model' => $searchModel]);
    ?>

    <!--
    GridView 小部件
        - 是 Yii 中功能最强大的小部件之一
        - 非常适合用来快速建立系统的管理后台
        - 用 dataProvider 键指定提供数据的数据提供者
        - 用 filterModel 键指定一个能够提供搜索过滤功能的搜索模型类，如果没有设置这一项，列表中就不会有搜索选项
        - 用 columns 键指定需要展示的列及其格式
    -->
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            // 序号列：行号，从 1 开始并且自动增长
            //['class' => 'yii\grid\SerialColumn'],

            /**
             * 以下是数据列，用于显示数据
             *
             * 数据列是经常需要调整的内容，以 title 列为例，这里虽然只是简单的写了一个 title，
             * 但是这是一种简写，背后有很多内容。如果需要改写，看例子：
             * [
             *      'class' => DataColumn::className(),
             *      'attribute' => 'title',
             *      'format' => 'text',
             *      'label' => '标题'
             * ]
             * 这样的一个数组是背后设定 title 这一列的内容，当然这还不是全部的内容，
             * 只是大部分都有默认值，如果我们什么都不需要改动，则可以直接写一个 title 这样的简写方式。
             *
             * 一个数据列经常需要自定义的字段有以下几个
             *  - attribute 指定需要展示的属性
             *  - label 标签名
             *  - value 值
             *  - format 格式
             *  - filter 自定义过滤条件的输入框
             *  - contentOptions 设定数据列 HTML 属性
             * 这些都可以在上面案例数组中进行定义调整
             */
            // 重写 id 的展示，设置固定宽度
//            'id',
            [
                'attribute' => 'id',
                'contentOptions' => ['width' => '30px'],
],

            'title',
            // 'content:ntext',

            // 重写 author_id 的显示，展示作者的 nickname
            // 使用新增的属性 authorName 进行查询
            // 'author_id',
            [
                'attribute' => 'authorName',
                'label' => '作者',
                'value' => 'author.nickname',
            ],
            'tags:ntext',

            // 重写 status 的显示，展示文章状态的 name
            // 'status',
            [
                'attribute' => 'status',
                'value' => 'status0.name',
                // 对过滤条件框的内容进行自定义设置
                'filter' => \common\models\Poststatus::find()
                    ->select(['name', 'id'])
                    ->orderBy('position')
                    ->indexBy('id')
                    ->column(),

            ],

            //'create_time:datetime',
//            'update_time:datetime',
            [
                'attribute' => 'update_time',
                'format' => ['date', 'php:Y-m-d H:i:s'],
            ],

            /**
             * 操作按钮列，用于显示查看，更新，删除等操作
             */
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, \common\models\Post $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                }
            ],
        ],
    ]); ?>


</div>
