<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Encore\Admin\Auth\Database\Administrator;

class AdminUser extends Administrator
{
    public function address()
    {
        // 第2引数にリレーション先のキー
        return $this->hasOne(Address::class, 'user_id');
    }

    public function userItems()
    {
        return $this->hasMany(UserItem::class, 'user_id');
    }
}
