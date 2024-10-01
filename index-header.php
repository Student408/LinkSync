<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LinkSync</title>
    <link rel="stylesheet" href="css/index-header.css">
</head>
<body>
    <header>
        <nav>
            <div class="nav-container">
                <div class="logo">
                    <img src="icons/linksync.svg" alt="LinkSync">
                    <a href="land.php">LinkSync</a>
                </div>
                <div class="options-menu">
                    <ul>
                        <li><a href="add.php" class="icon-btn"><img src="icons/add.svg" alt="Add"></a></li>
                        <li><a href="search.php">Search+</a></li>
                        <li>
                            <div class="theme-switch">
                                <label class="switch">
                                    <input type="checkbox" id="themeSwitch">
                                    <span class="slider round"></span>
                                </label>
                                <span>Night</span>
                            </div>
                        </li>
                        <li>
                            <form method="post" action="">
                                <button type="submit" name="logout" class="logout-btn">Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
</body>
</html>
