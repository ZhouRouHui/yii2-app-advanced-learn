<?php

namespace frontend\controllers;

use common\models\Comment;
use common\models\Post;
use common\models\PostSearch;
use common\models\Tag;
use common\models\User;
use yii\db\Query;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PostController implements the CRUD actions for Post model.
 */
class PostController extends Controller
{
    // 是否是新评论提交
    public $added = 0;
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
                /**
                 * 页面缓存配置
                 */
                'pageCache' => [
                    'class' => 'yii\filters\PageCache',
                    'only' => ['index'],    // 指定当前控制器需要缓存的页面
                    'duration' => 600,  // 过期时间配置
                    // 配置缓存内容可以根据一些参数的更改而生成新的缓存
                    'variations' => [
                        // 接收 page 参数，根据 page 的不同值生成对应的缓存，解决分页情况下其他页面都只是用第一次缓存的内容的问题
                        \Yii::$app->request->get('page'),
                        // 接收 PostSearch 参数，根据 PostSearch 的不同值生成对应的缓存，解决搜索时每次得到的都是第一次搜索缓存的内容的问题
                        \Yii::$app->request->get('PostSearch'),
                    ],
                    // 指定缓存依赖
                    'dependency' => [
                        'class' => 'yii\caching\DbDependency',  // 依赖项
                        'sql' => 'select count(id) from post',  // 依赖条件
                    ]
                ],

                /**
                 * http 缓存配置，也就是 http 304 的缓存
                 */
                'httpCache' => [
                    'class' => 'yii\filters\HttpCache',
                    'only' => ['detail'],
                    'lastModified' => function ($action, $params) {
                        $q = new Query();
                        return $q->from('post')->max('update_time');
                    },
                    'etagSeed' => function ($action, $params) {
                        $post = $this->findModel(\Yii::$app->request->get('id'));
                        return serialize([$post->title, $post->content]);
                    },
                    'cacheControlHeader' => 'public,max-age=600'
                ]
            ]
        );
    }

    /**
     * Lists all Post models.
     *
     * @return string
     */
    public function actionIndex()
    {
        // 标签云数据
        $tags = Tag::findTagWeights();
        // 最近评论数据
        $recentComments = Comment::findRecentComments();
        $searchModel = new PostSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'tags' => $tags,
            'recentComments' => $recentComments,
        ]);
    }

    /**
     * Displays a single Post model.
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
     * Creates a new Post model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Post();

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
     * Updates an existing Post model.
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
     * Deletes an existing Post model.
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
     * Finds the Post model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Post the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Post::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * 文章详情页面
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionDetail($id)
    {
        // 准备数据模型
        $model = $this->findModel($id);
        $tags = Tag::findTagWeights();
        $recentComments = Comment::findRecentComments();

        $userMe = User::findOne(\Yii::$app->user->id);
        $commentModel = new Comment();
        $commentModel->email = $userMe->email;
        $commentModel->userid = $userMe->id;

        // 当评论提交时，处理评论
        if ($commentModel->load(\Yii::$app->request->post())) {
            $commentModel->status = 1; // pending
            $commentModel->post_id = $id;
            if ($commentModel->save()) {
                $this->added = 1;
            }
        }

        // 传输局给视图渲染
        return $this->render('detail', [
            'model' => $model,
            'tags' => $tags,
            'recentComments' => $recentComments,
            'commentModel' => $commentModel,
            'added' => $this->added,
        ]);
    }
}
