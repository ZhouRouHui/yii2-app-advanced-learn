<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Post */

$this->title = $model->title;
//$this->params['breadcrumbs'][] = ['label' => 'Posts', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => '文章管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="post-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('修改', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('删除', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '您确定删除这篇文章吗?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <!--   DetailView 数据小部件，还有 ListView 和 GridView -->
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'title',
            // ntext 以 html 编码的方法展示内容，也就是如果内容中有一些标签的话，会解析标签，而不是原样输出
            'content:ntext',
            'tags:ntext',
            // 修改 status 的显示
            // 'status',
            [
                'label' => '状态',
                'value' => $model->status0->name,
            ],
            // 修改时间格式
            // 'create_time:datetime',
            // 'update_time:datetime',
            [
                'attribute' => 'create_time',
                'value' => date('Y-m-d H:i:s', $model->create_time)
            ],
            [
                'attribute' => 'update_time',
                'value' => date('Y-m-d H:i:s', $model->update_time)
            ],
            // 另一种方式修改数据部件的展示方式
            // 'author_id',
            [
                // 定义 attribute 的话，会直接使用这个模型中 attributes 中对应字段定义的 label，如果这里需要自定义 label，还可以加上 label 的定义，比如下面
                'attribute' => 'author_id',
                'label' => '提交者',
                'value' => $model->author->nickname
            ],
        ],
        // 调整表格中内容的样式，label 就是表格中属性名的内容，value 就是表格中属性值得内容
        'template' => '<tr><th style="width: 120px;">{label}</th><td>{value}</td></tr>',
        // options 属性可以设置整个表格 table 这个标签的属性
        'options' => ['class' => 'table table-striped table-border detail-view']
    ]) ?>

</div>
