<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Post */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="post-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'content')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'tags')->textarea(['rows' => 6]) ?>

    <?php
    // 重写文章状态，改为下拉菜单形式

    // $form->field($model, 'status')->textInput()

    /**
     * 第一种方式：硬编码方式写死下拉菜单中 option 的内容
     */
    // $form->field($model, 'status')->dropDownList([
    //         1 => '草稿',
    //         2 => '已发布'
    // ], [
    //         // prompt 设置下拉菜单的提示信息
    //         'prompt' => '请选择状态'
    // ])

    /**
     * 第二种方式，option 为动态，手动查询数据，放入配置中
     * \yii\helpers\ArrayHelper::map() 方法将对象集合中的数据转换成一个数组，参数二为数组键，参数三为数组值
     */
    // $psObjs = \common\models\Poststatus::find()->all();
    // $allStatus = \yii\helpers\ArrayHelper::map($psObjs, 'id', 'name');

    /**
     * 第三种方式，使用 QueryBuilder 查询构造器查询数据
     */
    // $allStatus = (new yii\db\Query())->select(['name', 'id'])
    //  ->from('poststatus')->indexBy('id')->column();

    /**
     * 第四种方式，使用 ActiveRecord 配合 QueryBuilder 构建查询
     * 可以这么用是因为 find() 方法返回的 yii\db\ActiveQuery 对象和 yii\db\Query 对象都实现了 yii\db\QueryInterface 接口
     */
    $allStatus = \common\models\Poststatus::find()->select(['name', 'id'])
        ->orderBy('position')->indexBy('id')->column();
    ?>
    <?= $form->field($model, 'status')->dropDownList($allStatus, [
        // prompt 设置下拉菜单的提示信息
        'prompt' => '请选择状态'
    ]) ?>

    <?= $form->field($model, 'author_id')->dropDownList(\common\models\Adminuser::find()
        ->select(['nickname', 'id'])->indexBy('id')->column(), [
                'prompt' => '请选择作者'
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
