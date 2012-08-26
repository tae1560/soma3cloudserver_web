<?PHP echo '<' . '?xml version="1.0" encoding="utf-8"?' . '>';?>
<!DOCTYPE html
PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <title>the singer invitation - purchase</title>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        <link type="text/css" rel="stylesheet" href="styles/common.css" />
        <link type="text/css" rel="stylesheet" href="styles/login.css" />
    </head>
    <body>
        <div class="wrapper">
            <form accept-charset="UTF-8" action="/users/sign_in" class="simple_form user" id="user_new" method="post">
                <div class="block center login small">
                    <div class="block_content">
                        <div class="input string id required">
                            <label class="id required" for="user_id">ID</label>
                            <input class="string id required" id="user_id" maxlength="255" name="id" required="required" size="50" type="text" />
                        </div>
                        <div class="input password required">
                            <label class="password required" for="user_password">Password</label>
                            <input class="password required" id="user_password" name="password" required="required" size="30" type="password" />
                        </div>
                        
                        <input class="half-width-button" id="user_login" name="login" type="submit" value="Login!">
                        <input class="half-width-button" id="user_join" name="join" type="submit" value="Join!">
                    </div>
                </div>
            </form>
        </div>
    </body>
</html>