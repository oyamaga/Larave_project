<?php

use Illuminate\Database\Seeder;
use App\Admin\Models\Address;
use App\Admin\Models\AdminUser;
use App\Admin\Models\Item;
use App\Admin\Models\UserItem;
use App\Admin\Models\Comment;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Address::truncate();
        Address::create([
            'user_id' => 1,
            'address' => 'テスト住所',
            'email'     => 'testemail@mail',
        ]);

        Item::truncate();
        Item::create([
            'item_name' => 'テストアイテム',
        ]);

        Comment::truncate();
        Comment::create([
            'user_id' => '1',
            'comment' => 'テストコメント',
        ]);

        UserItem::truncate();
        UserItem::create([
            'item_id' => '1',
            'user_id' => '1',
            'quantity' => '1',
        ]);
    }
}
