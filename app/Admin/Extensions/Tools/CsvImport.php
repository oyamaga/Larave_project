<?php

namespace App\Admin\Extensions\Tools;
 
use Encore\Admin\Admin;
use Encore\Admin\Grid;
use Encore\Admin\Grid\Tools\AbstractTool;
use Illuminate\Support\Facades\Request;

class CsvImport extends AbstractTool
{


    /**
     * Set up script for import button.
     */
    protected function script()
    {
        return <<< SCRIPT

// ボタン押下でCSVインポート
$('.csv-import').click(function() {
    var select = document.getElementById('files');
    document.getElementById("files").click();
    select.addEventListener('change',function() {
        var fd = new FormData();
        fd.append( "file", $("input[name='hoge']").prop("files")[0] );
        console.log(fd)
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type : "POST",
            url : "/admin/auth/import",
            dataType : "json",
            data : fd,
            processData : false,
            contentType : false,
        })
        .then(
            // 成功時のコールバック
            function (data) {
                console.log('成功しました')
            },
            // 失敗時のコールバック
            function () {
                console.log('失敗しました')
            }
        );
    });
});

SCRIPT;
    }
     
    /**
     * Render Import button.
     *
     * @return string
     */
    public function render()
    {
     
            Admin::script($this->script());

            return view('csv_upload');
    }
}
