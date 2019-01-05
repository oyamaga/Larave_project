<?php

namespace App\Admin\Controllers;

use App\Admin\Models\TestUser;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use App\Admin\Extensions\Tools\CsvImport;
use App\Admin\Extensions\Tools\CsvExport;
use Goodby\CSV\Import\Standard\Lexer;
use Goodby\CSV\Import\Standard\Interpreter;
use Goodby\CSV\Import\Standard\LexerConfig;
use Illuminate\Http\Request;
use App\Admin\Models\Item;

class TestUserController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('Index')
            ->description('description')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('Detail')
            ->description('description')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('Edit')
            ->description('description')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('Create')
            ->description('description')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new TestUser);

        $grid->id('Id');
        $grid->name('Name');
        $grid->age('Age');
        $grid->email('Email');
        $grid->created_at('Created at');
        $grid->updated_at('Updated at');
        $grid->tools(function ($tools) {
            $tools->append(new CsvImport());
        });
        $grid->tools(function ($tools) {
            $tools->append(new CsvExport());
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(TestUser::findOrFail($id));

        $show->id('Id');
        $show->name('Name');
        $show->age('Age');
        $show->email('Email');
        $show->created_at('Created at');
        $show->updated_at('Updated at');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new TestUser);

        $form->text('name', 'Name');
        $form->number('age', 'Age');
        $form->email('email', 'Email');

        return $form;
    }
    public function csvImport(Request $request)
    {
        $file = $request->file('file');
        $config = new LexerConfig();
        $lexer = new Lexer($config);
    
        $interpreter = new Interpreter();
        $rows = array();
        // 行の一貫性は無視
        $interpreter->unstrict();
        $interpreter->addObserver(function (array $row) use (&$rows) {
            $rows[] = $row;
        });


        // CSVデータをパース
        $lexer->parse($file, $interpreter);
        $data = array();

        // CSVのデータを配列化
        foreach ($rows as $key => $value) {

            $arr = array();

            foreach ($value as $k => $v) {

                switch ($k) {

                    case 0:
                        $arr['item_name'] = $v;
                        break;

                    case 1:
                        $arr['quantity'] = $v;
                        break;

                    case 2:
                        $arr['price'] = $v;
                        break;

                    default:
                        break;
                }
            }
            $data[] = $arr;
        }
        Item::insert($data);
        return response()->json(
            [
                'data' => '成功'
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
}
