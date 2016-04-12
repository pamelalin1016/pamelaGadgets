<?php
if(isset($_POST['json_data'])){
    $arr = '';

    $json_data = trim($_POST['json_data']);
    $json_data=str_replace("\\\\", "\\", $json_data);
    $json_data=str_replace('\"', '"', $json_data);

    if($_POST['act'] == 'json'){
        $arr = json_decode($json_data,true);
    }elseif($_POST['act'] == 'url'){
        $arr = urldecode($json_data);
    }elseif($_POST['act'] == 'serialize'){
        $arr = unserialize($json_data);
    }elseif($_POST['act'] == 'md5'){
        $arr = md5($json_data);
    }elseif($_POST['act'] == 'sha1'){
        $arr = sha1($json_data);
    }elseif($_POST['act'] == 'base64'){
        $arr = base64_decode($json_data);
    }elseif($_POST['act'] == 'enbase64'){
        $arr = base64_encode($json_data);
    }elseif($_POST['act'] == 'enjson'){
        $arr_old = array();
        if(!empty($json_data)){
            $arr_old = get_arr($json_data);
        }

        $arr = json_encode($arr_old);
    }elseif($_POST['act'] == 'enurl'){
        $arr = urlencode($json_data);
    }elseif($_POST['act'] == 'enserialize'){
        $arr_old = array();
        if(!empty($json_data)){
            $arr_old = get_arr($json_data);
        }

        $arr = serialize($arr_old);
    }


    print_r($arr);
    exit;
}
?>

<html>
    <head>
        <title>show gadgets</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
        <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
        <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
        <script>
        var col = 0;
        function aaa(actVal){
            $.post( "//<?php echo $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']?>", { json_data: $('#get_json').val() , act: actVal},function(data){
                    var tpl = $('#htmlTpl').html();
                    var color = '#EEE';
                    if(col>0){
                        color = '#EFE';
                        col=0;
                    }else{
                        col++;
                    }
                    tpl = tpl.replace(/\[json_json\]/, $('#get_json').val());
                    tpl = tpl.replace(/\[get_type\]/, actVal);
                    tpl = tpl.replace(/\[arr_json\]/, data);
                    tpl = tpl.replace(/\[bk_color\]/, color);

                    $('#showData').html( tpl + $('#showData').html() );
                } );
        }
        </script>
    </head>
    <body>
        <h3>show gadgets</h3>
        <textarea style="width:500px;height:150px;" id="get_json"></textarea>
        要使用EN請用,分隔 EX:pid[aa][2][3]=1,pid[1][2]=2,aid=2,pid[1][3]=3
        <br/>
        <button onclick="aaa('json');">Show Json</button>
        <button onclick="aaa('url');">Show Url</button>
        <button onclick="aaa('serialize');">Show serialize</button>
        <button onclick="aaa('md5');">Show Md5</button>
        <button onclick="aaa('sha1');">Show sha1</button>
        <button onclick="aaa('base64');">Show base64</button>
        <br/>
        <button onclick="aaa('enjson');">EN Json</button>
        <button onclick="aaa('enurl');">EN Url</button>
        <button onclick="aaa('enbase64');">EN base64</button>
        <button onclick="aaa('enserialize');">EN serialize</button>
        <div id="showData">
            <hr/>
        </div>
        <div id="htmlTpl" style="display: none;">
            <hr/>
            <div style="width:800px;margin-top: 10px; background-color: [bk_color];">
                <div style="width:800px;word-break: break-all; ;">([get_type])[json_json]</div>
                <br/>
                <pre>[arr_json]</pre>
            </div>
        </div>
    </body>
</html>
<?php
function get_arr($str){
    $arr = array();

    $arr_temp = explode(',', $str);
    if(count($arr_temp) > 0){
        foreach($arr_temp as $key => $temp_row){
            $temp_arr = get_arr2($temp_row);
            $arr = array_merge_recursive($arr,$temp_arr);
        }
    }

    return $arr;
}

function get_arr2($str,$col=0){
    $pos1 = stripos($str, '[');
    $pos2 = stripos($str, ']');
    $arr = false;

    if($pos1>='0' && $pos2>0){
        if($col == 0){
            $name = substr($str, 0,$pos1);
            $col_name = substr($str, ($pos1+1),($pos2-$pos1-1));
            $temp_col = $col+1;
            $arr[$name][$col_name] = get_arr2(substr($str, ($pos2+1)),$temp_col);
        }else{
            $col_name = substr($str, ($pos1+1),($pos2-$pos1-1));
            $temp_col = $col+1;
            $arr[$col_name] = get_arr2(substr($str, ($pos2+1)),$temp_col);
        }
    }
    else{
        if($col == 0 ){
            $arr_temp = explode('=', $str);
            $arr[$arr_temp[0]] = $arr_temp[1];
        }else{
            $arr_temp = explode('=', $str);
            $arr = $arr_temp[1];
        }
    }
    return $arr;
}
?>