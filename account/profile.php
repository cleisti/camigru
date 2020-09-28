<?php
    include_once 'get_user.php';

    if (!$_SESSION) {
        header('Location: index.php?page=account/logout');
    }

    // $user = get_user($_SESSION['logged_user']);

?>

<!DOCTYPE html>
<html>
<head>
    <script src="scripts/profile.js"></script>
</head>
    <body>
        <div class="d-flex p-2 justify-content-center align-content-around">

    <table>
        <tr rowspan="2">
            <td></td>
            <th><h2 id="userTitle"></h2></th>
        </tr>
        <tr rowspan="2">
            <td colspan="2"><div id="message"></div></td>
        </tr>
            <tr>
                <th>Notifications</th>
                <td><label class="switch">
                <input type="checkbox" id="notifications">
                <span class="slider round"></span>
                </label></td>
            </tr>
            <form id="username" action="" enctype="multipart/form-data" method="post">
            <tr>
                <th style="height: 50px;">Username</th>
                <td><input type="text" name="new_un" minlength="4" maxlength="25" value="" required /></td>
            </tr>
            <tr>
				<td></td>
				<td><input id="change_un" class="button" type="submit" value="Change username"  /></td>
            </tr>
            </form>
            <form id="email" action="" method="post">
            <tr>
                <th>Email</th>
                <td><input type="email" name="new_email" value="" required /></td>
            </tr>
            <tr>
                <td></td>
                <td><input type="email" name="validate_email" value="" placeholder="Validate email" required /></td>
            </tr>
            <tr>
                <td></td>
                <td><input id="change_email" class="button" type="submit" name="submit" value="Change email" /></td>
            </tr>
            </form>
            <form id="password" action="" method="post">
            <tr>
                <th>New password</th>
                <td><input type="password" name="new_pw" minlength="8" maxlength="20" value="" placeholder="********" required /></td>
            </tr>
            <tr>
                <td></td>
                <td><input type="password" name="validate_pw" minlength="8" maxlength="20" value="" placeholder="Validate password" required /></td>
            </tr>
            <tr>
                <td></td>
                <td><input type="password" name="old_pw" minlength="8" maxlength="20" value="" placeholder="Your Password" required /></td>
            </tr>
            <tr>
                <td></td>
                <td><input id="change_password" class="button" type="submit" name="submit" value="Change password" /></td>
            </tr>
            </form>
        </table>
</div>
    </body>
</html>