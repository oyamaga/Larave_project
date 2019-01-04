<?php

namespace App\Admin\Controllers;


use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Widgets;
use App\Admin\Models\AdminUser;
use App\Admin\Models\Address;
use App\Admin\Models\UserItem;
use App\Admin\Models\Item;

use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Table;
use Encore\Admin\Layout\Row;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Encore\Admin\Layout\Column;
use App\Admin\Extensions\Tools\CsvImport;
use Goodby\CSV\Import\Standard\Lexer;
use Goodby\CSV\Import\Standard\Interpreter;
use Goodby\CSV\Import\Standard\LexerConfig;
use Illuminate\Http\Request;
use Log;

class AdminUserController extends Controller
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
        ->header('Index')
        ->description('description')
        ->body($this->box($id, $content));
        // ->body($this->form($id, $content));
        // ->body($this->tab($id, $content));
    }

    public function box($id, Content $content)
    {
        $content->header('ユーザ情報');
        // admin_users,addresses,itemsを取得
        $admin_users=AdminUser::where('id', $id)->get();
        $address=Address::where('user_id', $id)->get();
        $itemId=UserItem::where('user_id', $id)->get();
        // 全アイテム名を取得
        $itemName=Item::get();
        $list1=[];
        foreach ($itemName as $v) {
            $list1[$v['id']]=$v['item_name'];
        }
        // 所持アイテムを取得
        $list2 = [];
        foreach ($itemId as $v) {
            $list2[$v['id']]=$list1[$v['item_id']];
        }
        // カラム名を取得
        $content->row(function (Row $row) use ($admin_users, $address, $list2) {
            $row->column(3, function (Column $column) use ($list2) {
                $headers = ['アイテム名', '数量'];
                $table = new Table($headers, $list2);
                $box = new Box('ユーザー情報', $table->render());
                $box->collapsable();
                $box->style('primary');
                $box->solid();
                $column->append($box);
            });
            // $row->column(2, function (Column $column) use ($items) {
            //     $table = new Table(['アイテム名', '所持数'], $items);
            //     $box = new Box('アイテム情報', $table->render());
            //     $box->collapsable();
            //     $box->style('info');
            //     $box->solid();
            //     $column->append($box);
            // });
        });
        $columns = Schema::getColumnListing('admin_users');
        $headers = ['Id', 'username', 'name', 'address', 'email', 'item_name', 'quantity'];
        $rows = [
            [1, 'labore21@yahoo.com', 'Ms. Clotilde Gibson', 25, 'Goodwin-Watsica'],
            [2, 'omnis.in@hotmail.com', 'Allie Kuhic', 28, 'Murphy, Koepp and Morar'],
            [3, 'quia65@hotmail.com', 'Prof. Drew Heller', 35, 'Kihn LLC'],
            [4, 'xet@yahoo.com', 'William Koss', 20, 'Becker-Raynor'],
            [5, 'ipsa.aut@gmail.com', 'Ms. Antonietta Kozey Jr.', 41, 'MicroBist'],
        ];
        $table = new Table($headers, $rows);
        // $table = new Table();
        $box4 = new Box('Forth Box', $table);
        // $content->row($box1->collapsable());
        // $content->row($box2->style('danger'));
        // $content->row($box3->removable()->style('warning'));
        $content->row($box4->solid()->style('primary'));
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
        return Admin::content(function (Content $content) use ($id) {

            $content->header('ユーザー情報');
            $content->description('edit');

            $content->body($this->form()->edit($id));
        });
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
        // $grid = new Grid(new AdminUser);
        return Admin::grid(AdminUser::class, function (Grid $grid) {
            $userRoll = Admin::user()->id;
            // 一覧から管理者を非表示
            $grid->model()->where('username', '!=', 'admin');

            $grid->id('Id');
            $grid->name('ユーザー名');
            $grid->address()->address('住所');
            $grid->address()->email('メールアドレス');
            // 全てのアイテム名を取得
            $tmp=Item::get(['id','item_name',]);
            $itemName = [];
            foreach ($tmp as $v) {
                $itemName[$v['id']] =$v['item_name'];
            }
            // アイテムの数量を取得
            $tmp=UserItem::get(['id', 'quantity']);
            foreach ($tmp as $v) {
                $itemQuantity[$v['id']] =$v['quantity'];
            }
            // dd($itemQuantity);
            $grid->userItems('所持アイテム')->display(function ($userItem) use ($itemName, $itemQuantity) {
                $result='';
                foreach ($userItem as $v) {
                    if (!isset($result)) {
                        $result .= ' ';
                    }
                    $result .= "<span class='label label-warning'>{$itemName[$v['item_id']]}({$itemQuantity[$v['id']]})</span>";
                }
                return $result;
            });
            $grid->tools(function ($tools) {
                $tools->append(new CsvImport());
            });
        });
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(AdminUser::findOrFail($id));

        $show->id('Id');
        $show->username('Username');
        // $show->password('Password');
        $show->name('Name');
        $show->avatar('Avatar');
        // $show->remember_token('Remember token');
        $show->created_at('Created at');
        $show->updated_at('Updated at');

        return $show;
    }

    /**
     * Make a show builder.
     *
     * @return Form
     */
    public function form2()
    {
        return Admin::form(AdminUser::class, function (Form $form) {
            $form->tab('ユーザー情報', function ($form) {
                $form->display('username', 'ログインID');
                $form->display('name', '氏名');
            })->tab('連絡先情報', function ($form) {
                $form->display('address.address', '住所');
                $form->display('address.phone', '電話番号');
                $form->display('address.email', 'メールアドレス');
            });
            $form->disableSubmit();
            $form->disableReset();
            $form->tools(function (Form\Tools $tools) {

                // Disable `List` btn.
                $tools->disableList();

                // Disable `Delete` btn.
                $tools->disableDelete();

                // Disable `Veiw` btn.
                $tools->disableView();
            
                // TODO URL修正
                $tools->add('<a href="http://localhost:8000/admin/auth/users" class="btn btn-sm btn-default"><i class="fa fa-list"></i>&nbsp;&nbsp;一覧</a>');
            });
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(AdminUser::class, function (Form $form) {
            $form->tab('ユーザー情報', function ($form) {
                $form->text('username', 'ログインID');
                $form->password('password', 'Password');
                $form->text('name', '氏名');
            })->tab('連絡先情報', function ($form) {
                $form->text('address.address', '住所');
                $form->text('address.phone', '電話番号');
                $form->text('address.email', 'メールアドレス');
            });
        });
    }
    public function csvImport(Request $request)
    {
        $file = $request->file('file');
        // $file=file($file);
        // dd($file);
        $config = new LexerConfig();
        $lexer = new Lexer($config);
    
        $interpreter = new Interpreter();
        $rows = array();
 
        $interpreter->addObserver(function (array $row) use (&$rows) {
            $rows[] = $row;
        });
        // CSVデータをパース
        $lexer->parse($file, $interpreter);
        $data = array();

        // dd($rows);
 
        // CSVのデータを配列化
        foreach ($rows as $key => $value) {

            $arr = array();

            foreach ($value as $k => $v) {

                switch ($k) {

                    case 0:
                        $arr['username'] = $v;
                        break;

                    case 1:
                        $arr['name'] = $v;
                        break;

                    case 2:
                        $arr['password'] = $v;
                        break;

                    default:
                        break;
                }
            }
            $data[] = $arr;
        }
        AdminUser::insert($data);
        // dd($data);
    }
}
