<?php

namespace backend\controllers;

use backednd\models\SignupForm;
use common\models\Adminuser;
use common\models\AdminuserSearch;
use common\models\AuthAssignment;
use common\models\AuthItem;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AdminuserController implements the CRUD actions for Adminuser model.
 */
class AdminuserController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Adminuser models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new AdminuserSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Adminuser model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Adminuser model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Adminuser();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Adminuser model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Adminuser model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Adminuser model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Adminuser the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Adminuser::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionPrivilege($id)
    {
        // 找出所有权限，提供给 checkbox list
        $allPrivileges = AuthItem::find()->select(['name', 'description'])->where(['type' => 1])
            ->orderBy('description')->all();
        $allPrivilegesArray = [];
        foreach ($allPrivileges as $pri) {
            $allPrivilegesArray[$pri->name] = $pri->description;
        }

        // 当前用户的权限
        $authAssignments = AuthAssignment::find()->select(['item_name'])->where(['user_id' => $id])->all();
        $AuthAssignmentsArray = [];
        foreach ($authAssignments as $authAssignment) {
            array_push($AuthAssignmentsArray, $authAssignment->item_name);
        }

        // 更新权限
        if (isset($_POST['newPri'])) {
            AuthAssignment::deleteAll('user_id=:id', [":id" => $id]);
            $newPri = $_POST['newPri'];
            $arrLength = count($newPri);
            for ($x = 0; $x < $arrLength; $x++) {
                $aPri = new AuthAssignment();
                $aPri->item_name = $newPri[$x];
                $aPri->user_id = $id;
                $aPri->created_at = time();
                $aPri->save();
            }
            return $this->redirect(['index']);
        }

        // 渲染表单
        return $this->render('privilege', ['id' => $id, 'AuthAssignmentsArray' => $AuthAssignmentsArray,
            'allPrivilegesArray' => $allPrivilegesArray]);
    }
}
