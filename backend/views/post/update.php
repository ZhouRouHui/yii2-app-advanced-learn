<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Post */

$this->title = '文章修改: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => '文章管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="post-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <!--
        渲染出表单部分
        yii\web\View 也有 render 方法，作用于控制器的 render 方法相同
        _form 表示当前目录下的 _form.php 模板文件，里面定义的是表单，表单单独拎出来是为了 create 和 update 时通用
    -->
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
