//№1 Подключение к БД
<?php
return [
  'class' => 'yii\db\Connection',
  'dsn' => 'mysql:host=localhost;dbname=php_univ',
  'username' => 'root',
  'password' => 'root',
  'charset' => 'utf8',
];

//№2 Вывод таблицы Authors всех данных
//Создание модели
<?php

namespace app\models;
use yii\db\ActiveRecord;

  class Authors extends ActiveRecord
  {
    public function getBooks()
    {
      return $this->hasMany(Books::className(), ['id_a' => 'id_authors']);
    }

    public function rules()
    {
      return [
        [['surname', 'name', 'birthday'], 'required'],
        [['birthday', 'date_death'], 'date', 'format' => 'php:Y-m-d']];
    }
  }

//Создание действия в контроллере
<?php

namespace app\controllers;
use app\models\Authors;
. . .
  public function actionLab2()
  {
    $authorList = Authors::find()->all();
    return $this->render('lab2', compact('$authorList'));
  }

//Создание представления
<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>
  
    <table class="table">
        <tr>
            <th>Id</th>
            <th>Фамилия</th>
            <th>Имя</th>
            <th>Отчество</th>
            <th>День рождения</th>
            <th>День смерти</th>
            <th></th>
        </tr>
    <?php foreach($authorList as $authors): ?>
    <tr>
        <td><?php echo $authors -> id_authors; ?></td>
        <td><?php echo $authors -> surname; ?></td>
        <td><?php echo $authors -> name; ?></td>
        <td> <?php echo $authors -> middle_name; ?></td>
        <td><?php echo $authors -> birthday; ?></td>
        <td><?php echo $authors -> date_death; ?></td>
    </tr>
        <?php endforeach; ?>
    </table>
</div>

//№3 Вывод таблицы Книги с двумя связаными таблицами, Авторы и Жанры
//Создание модели
<?php
namespace app\models;

use yii\db\ActiveRecord;

class Books extends ActiveRecord
{
    public $findtitle;

    public function rules()
    {
        return [
            [['name', 'year_writing'], 'required'],
            [['year_writing'], 'date', 'format' => 'php:Y-m-d'],
            ['findtitle', 'required']
        ];
    }
  
    public function getAuthors(){
        return $this->hasOne(Authors::className(),['id_a'=>'id_a']);
    }
    public function getGenre(){
        return $this->hasOne(Genre::className(), ['id_g'=>'id_g']);
    }
}
                         
//Создание действия в контроллере
<?php
namespace app\controllers;
use Yii;
use yii\web\Controller;
use app\models\Books;

class BooksController extends Controller
{
    public function actionIndex(){ 

        $query = Books::find();
        
        return $this->render('index', compact('query'));
    }
                             
//Создание представления
<?php
use yii\helpers\Html;
?>

<div class="site-lab2">
  <table class="table">
    <tr>
      <th scope="col">ID</th>
      <th scope="col">Название</th>
      <th scope="col">Автор</th>
      <th scope="col">Жанр</th>
      <th scope="col">Год написания</th>
    </tr>
    
  <?php 
  foreach ($book as $books): ?>
    <tr>
      <td><?= Html::encode($books->id_b)?></td>
      <td><?= Html::encode($books->title)?></td>
      <td><?= Html::encode($books->authors->full_name)?></td>
      <td><?= Html::encode($books->genre->title)?></td>
      <td><?= Html::encode($books->yow)?></td>
    </tr>
  <?php endforeach; ?>
</table>
</div>

//№4 Найти книги, написанные в 20 веке. Отсортировать по году написания.
//Модель используется та же, представление соответвует обычному выводу книг
//Создание действия в контроллере

<?php
public function actionBooks_20()
    {
        $filter = Books::find()
            ->where(['between', 'year_writing', 1900, 1999])
            ->orderBy('year_writing')
            ->all();
        return $this->render('books_20', compact('filter'));
    }
  
//№5 Вывести авторов и количество написанных ими книг.
//Создаем действие в контроллере
namespace app\controllers;
use app\models\Authors;
  
public function actionBooks_authors_col()
    {
        $bookAuthors = Authors::find()->all();
        return $this->render('books_authors_col', [
            'bookAuthors' => $bookAuthors,
        ]);
    }
 
//Создаем представление
<?php

use yii\helpers\Html;

<div class="site-lab2">
    
    <table class="table">
        <tr>
            <th>Автор</th>
            <th>Количество написанных книг</th>
        </tr>
    <?php foreach($bookAuthors as $authors): ?>
    <tr>
        <td><?php echo $authors -> name . ' ' . $authors -> surname; ?></td>
        <td><?php $books = $authors->books; echo count($books)?></td>
    </tr>
        <?php endforeach; ?>
    </table>
</div>

//№6 Найти книги, в названии которых содержится слово. Слово вводить в форму.
//Создаем действие в контроллере
<?php
namespace app\controllers;
use app\models\Books;
public function actionLab2_1()
    {
        $model = new Books();
        if ($model->load(Yii::$app->request->post())) {
            $query = Books::find()->where(['like', 'name', $model->findtitle]);
        } else {
            $query = Books::find();
        }
        $bookList = $query->orderBy('id_book')->all();
        return $this->render('lab2_1', compact('bookList'));
    }

 //Создаем представление
<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

<div class="site-lab2">
    <?php $from = ActiveForm::begin(); ?>
    <?= $from->field($model, 'findtitle')->label('Название книги') ?>
    <?= Html::submitButton('Найти', ['class' => 'btn btn-primary']) ?>
    <?php ActiveForm::end(); ?>
    <br>
    <table class="table">
        <tr>
            <th>Id</th>
            <th>Книга</th>
            <th>Описание</th>
            <th>Год написания</th>
            <th>Автор</th>
            <th>Жанр</th>
        </tr>
    <?php foreach($bookList as $books): ?>
    <tr>
        <td><?php echo $books -> id_book; ?></td>
        <td><?php echo $books -> name; ?></td>
        <td><?php echo $books -> description; ?></td>
        <td> <?php echo $books -> year_writing; ?></td>
        <td><?php echo $books -> authors -> name . ' ' . $books -> authors -> surname; ?></td>
        <td><?php echo $books -> genre -> name; ?></td>
    </tr>
        <?php endforeach; ?>
    </table>
</div>

//№7 Создайте форму для добавления нового автора. Выполните добавление введенных данных в таблицу.
//Создаем дествие в контроллере
<?php
public function actionCreate()
    {
        $model = new Authors(); 

        if ($model->load(Yii::$app->request->post()) && $model->save())

        return $this->render('create', [
            'model' => $model,
        ]);
    }
  
//Создаем представление
<?php 
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<?php $form = ActiveForm::begin(); ?>

    <?php echo $form->field($model, 'surname')->label('Фамилия:'); ?>
    <?php echo $form->field($model, 'name')->label('Имя:'); ?>
    <?php echo $form->field($model, 'middle_name')->label('Отчество:'); ?>
    <?php echo $form->field($model, 'birthday')->label('Дата рождения:'); ?>
    <?php echo $form->field($model, 'date_death')->label('Дата смерти:'); ?>

    <?php echo Html::submitButton('Save',['class' => 'btn btn-primary']); ?>

<?php ActiveForm::end(); ?>

// №8 Создайте форму для удаления автора. Введите в форму id автора и удалите его из таблицы.
//Создаем действие в контроллере
<?php
public function actionDelete(){
        $model = new FormDeleteAuthors();
        if ($model->load(Yii::$app->request->post())){
            $delete = Authors::findOne($model->id_a);
            $delete->delete();
        };
        
        return $this->render('delete', compact('model'));

    }
  
//Создание представления
<?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model, 'id_a')->textInput(); ?>
        <?= Html::submitButton('Удалить', ['class' => 'btn btn-info']); ?>
<?php ActiveForm::end(); ?>
