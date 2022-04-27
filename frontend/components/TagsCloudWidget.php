<?php/** * Created By IntelliJ IDEA * @author Roy * @date: 2022/4/27 * @time: 10:49 */namespace frontend\components;use yii\base\Widget;/** * 自定义的标签云小部件 */class TagsCloudWidget extends Widget{    public $tags;    public function init()    {        parent::init();    }    public function run()    {        $tagString = '';        $fontStyle = [            '6' => 'danger',            '5' => 'info',            '4' => 'warning',            '3' => 'primary',            '2' => 'success',        ];        foreach ($this->tags as $tag => $weight) {            $tagString .= '<a href="' .\Yii::$app->homeUrl.'?r=post/index&PostSearch[tags]'.$tag.'">'                .'<h'.$weight.' style="display:inline-block;">                    <span class="label label-'.$fontStyle[$weight].'">'.$tag.'</span>                </h'.$weight.'>            </a>';        }        return $tagString;    }}