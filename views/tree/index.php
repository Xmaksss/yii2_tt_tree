<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Tree';

$script = "Tree.init(document.getElementById('parent-id').value);";
$this->registerJs($script, yii\web\View::POS_READY);
?>

<div class="row">
    <div class="col-md-12">
        <?php $form = ActiveForm::begin(['id' => 'contact-form']); ?>

        <?= $form->field($model, 'count')->textInput(['autofocus' => true, 'value' => 1]) ?>
        <input type="hidden" id="parent-id" value="<?=$id?>">

        <div class="form-group">
            <?= Html::submitButton('Генерировать дерево', ['class' => 'btn btn-success', 'name' => 'contact-button']) ?>
        </div>

        <?php ActiveForm::end(); ?>

        <?php if (Yii::$app->session->hasFlash('success')): ?>
        <div class="alert alert-success">
            <?=Yii::$app->session->getFlash('success')?>
        </div>
        <?php endif;?>

        <?php if (Yii::$app->session->hasFlash('error')): ?>
        <div class="alert alert-danger">
            <?=Yii::$app->session->getFlash('error')?>
        </div>
        <?php endif;?>

    </div>

    <div class="col-md-12">
        <svg viewBox="0 0 100 50" version="1.1" xmlns="http://www.w3.org/2000/svg" id="tree"></svg>
    </div>
</div>