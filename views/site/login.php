<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Entrar no TabWiter';
?>

<div class="min-h-screen bg-slate-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="mt-6 text-center text-3xl font-extrabold text-slate-900">
            Acesse sua conta
        </h2>
        <p class="mt-2 text-center text-sm text-slate-600">
            Ou <a href="<?= \yii\helpers\Url::to(['site/signup']) ?>"
                class="font-medium text-brand-600 hover:text-brand-500">crie uma nova conta gratuitamente</a>
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10 border border-slate-200">
            <?php $form = ActiveForm::begin([
                'id' => 'login-form',
                'fieldConfig' => [
                    'template' => "{label}\n{input}\n{error}",
                    'labelOptions' => ['class' => 'block text-sm font-medium text-slate-700'],
                    'inputOptions' => ['class' => 'appearance-none block w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm placeholder-slate-400 focus:outline-none focus:ring-brand-500 focus:border-brand-500 sm:text-sm'],
                    'errorOptions' => ['class' => 'mt-1 text-sm text-red-600'],
                ],
            ]); ?>

            <div class="space-y-6">
                <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>

                <?= $form->field($model, 'password')->passwordInput() ?>

                <div class="flex items-center justify-between">
                    <?= $form->field($model, 'rememberMe', [
                        'template' => "<div class=\"flex items-center\">{input}\n{label}</div>",
                        'labelOptions' => ['class' => 'ml-2 block text-sm text-slate-900'],
                        'inputOptions' => ['class' => 'h-4 w-4 text-brand-600 focus:ring-brand-500 border-slate-300 rounded'],
                    ])->checkbox() ?>

                    <div class="text-sm">
                        <a href="#" class="font-medium text-brand-600 hover:text-brand-500">Esqueceu a senha?</a>
                    </div>
                </div>

                <div>
                    <?= Html::submitButton('Entrar', ['class' => 'w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-brand-600 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition']) ?>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>