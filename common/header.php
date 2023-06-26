<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start the session
}
if (!isset($_SESSION['status'])) {
    header("Location: /lms/auth/login.php");
    exit();
}

include "./common/backendConnector.php";

$con = mysqli_connect($host, $dbUserName, $dbPassword, $database);
if (!$con) {
    die("DB connection failed");
}

$id = $_SESSION['id'];
$name = $_SESSION['name'];
$email = $_SESSION['email'];

$sqlFetch = "SELECT * FROM bookorder WHERE userid = $id AND isreturn = 0";
$resFetch = mysqli_query($con, $sqlFetch);

?>

<html>

<head>
    <link rel="stylesheet" href="./CSS/homes.css">
    <style>
        #userIcon svg {
            height: 43px;
            width: 43px;
            padding: 5px;
            background-color: rgba(0, 0, 0, 0.7);
            fill: white;
            border-radius: 50%;
            cursor: pointer;
        }

        #content {
            position: absolute;
            content: "";
            top: 60px;
            right: 18px;
            min-height: 110px;
            width: 200px;
            color: rgba(0, 0, 0, 1);
            text-align: left;
            text-decoration: none;
            font-size: 12px;
            cursor: pointer;
            padding: 10px 15px;
            border-radius: 2px;
            box-shadow: 0px 1px 3px 1px rgba(0, 0, 0, 0.2);
            line-height: 20px;
            letter-spacing: 1px;
            font-family: Arial;
            background-color: rgba(241, 241, 252, 1);
            font-weight: 400;
        }

        #name {
            font-size: 14px;
            text-transform: capitalize;
        }

        #head {
            font-weight: 400;
            font-size: 13px;
            color: rgba(0, 0, 0, 0.7);
        }


        #logout-btn {
            border: none;
            padding: 8px;
            text-transform: uppercase;
            font-size: 14px;
            font-weight: 600;
            background-color: rgba(144, 132, 214, 1);
            width: 100%;
            margin-top: 5px;
            cursor: pointer;
            border-radius: 2px;
            transition: 0.2s;
            letter-spacing: 2px;
        }

        #logout-btn:hover,
        #showContentButton:hover {
            background-color: rgb(39, 29, 94);
            color: white;
        }

        #maindiv {
            height: 100vh;
            width: 100%;
            background-color: rgba(0, 0, 0, 0.2);
            position: fixed;
            z-index: 10000000000000000000000000000000000000000;

            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #secondMaindiv {
            max-height: 500px;
            max-width: 550px;
            background-color: white;
            min-height: 200px;
            padding: 20px 50px 55px 50px;
            border-radius: 5px;
            position: relative;
            z-index: 1;
        }

        #cross {
            position: absolute;
            top: 10px;
            right: 15px;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 23px;
            cursor: pointer;
            font-weight: 600;
            color: rgba(0, 0, 0, 0.7);
        }

        #cross:hover {
            color: rgba(0, 0, 0, 1);
        }

        .userprofile {
            display: flex;
            justify-content: start;
            align-items: center;
            padding: 10px 0;
            gap: 10px;
        }

        .userlogo {
            height: 45px;
            width: 45px;
            background-color: rgba(0, 0, 0, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #username {
            text-transform: capitalize;
        }

        #useremail {
            font-size: 13px;
            color: rgba(0, 0, 0, 0.6);
            text-transform: lowercase;
        }

        .profileUserContent {
            font-weight: 600;
            font-family: Arial, Helvetica, sans-serif;
            letter-spacing: 1px;
            color: rgba(0, 0, 0, 0.7);
        }

        #superModalDiv {
            display: none;
        }

        #logoName {
            letter-spacing: 1px;
            margin: 10px 0;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        #logoName p:nth-child(1) {
            font-size: 22px;
            font-weight: 600;
        }

        #logoName p:nth-child(2) {
            text-align: justify;
        }

        #request {
            cursor: pointer;
            letter-spacing: 1px;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
            font-weight: 600;
            color: rgba(0, 0, 0, 0.6);
            margin-bottom: 10px;
            display: inline-block;
            padding-top: 10px;
        }

        #request:hover {
            color: rgba(0, 0, 0, 1);
        }

        #Dynamiccontent {
            max-height: 140px;
            min-height: 30px;
            display: flex;
            flex-direction: column;
            gap: 7px;
            overflow-y: auto;

        }

        .eachsection {
            height: 40px;
            width: 100%;
            border-radius: 2px;
            padding: 3px 10px;
            background-color: rgba(0, 0, 0, 0.2);
        }

        .content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 35px;
            font-family: Arial, Helvetica, sans-serif;

        }

        .bookName {
            display: flex;
            gap: 5px;
            font-size: 14px;
            letter-spacing: 1px;
        }

        .bookName span:nth-child(1) {
            font-weight: 700;

        }

        #dynamiccontentmain {
            padding: 5px 0;
        }

        .catname {
            font-size: 11px;
            letter-spacing: 0;
            font-weight: 600;
        }
    </style>

</head>

<body>
    <div>
        <nav class="navbar">
            <img src="./images/image 2.png" alt="LOGO" id="logo">
            <div class="menu">
                <ul class="list">
                    <?php
                    $currentURL = $_SERVER['REQUEST_URI'];

                    $homeURL = "/lms/index.php";
                    $allBooksURL = "/lms/allBook.php";
                    $contactUsURL = "/lms/contactUs.php";
                    $aboutUsURL = "/lms/about.php";


                    function isActive($currentURL, $targetURL)
                    {
                        if (strpos($currentURL, $targetURL) !== false) {
                            return true;
                        }
                        return false;
                    }
                    ?>

                    <li>
                        <a href="<?php echo $homeURL; ?>">Home</a>
                        <?php echo isActive($currentURL, $homeURL) ? '
                        <div class="activeTracker"></div>
                        ' : '' ?>
                    </li>
                    <li>
                        <a href="<?php echo $allBooksURL; ?>">All Books</a>
                        <?php echo isActive($currentURL, $allBooksURL) ? '
                        <div class="activeTracker"></div>
                        ' : '' ?>
                    </li>
                    <li>
                        <a href="<?php echo $contactUsURL; ?>">Contact Us</a>
                        <?php echo isActive($currentURL, $contactUsURL) ? '
                        <div class="activeTracker"></div>
                        ' : '' ?>
                    </li>
                    <li>
                        <a href="<?php echo $aboutUsURL; ?>">About Us</a>
                        <?php echo isActive($currentURL, $aboutUsURL) ? '
                        <div class="activeTracker"></div>
                        ' : '' ?>
                    </li>

                    <li id="userIcon"><svg onclick="showContent()" width="45" height="45" viewBox="0 0 45 45" xmlns="http://www.w3.org/2000/svg">
                            <path d="M22.5 7.5C24.4891 7.5 26.3968 8.29018 27.8033 9.6967C29.2098 11.1032 30 13.0109 30 15C30 16.9891 29.2098 18.8968 27.8033 20.3033C26.3968 21.7098 24.4891 22.5 22.5 22.5C20.5109 22.5 18.6032 21.7098 17.1967 20.3033C15.7902 18.8968 15 16.9891 15 15C15 13.0109 15.7902 11.1032 17.1967 9.6967C18.6032 8.29018 20.5109 7.5 22.5 7.5ZM22.5 26.25C30.7875 26.25 37.5 29.6062 37.5 33.75V37.5H7.5V33.75C7.5 29.6062 14.2125 26.25 22.5 26.25Z"></path>
                        </svg></li>
                </ul>
            </div>
        </nav>
    </div>
    <div id="superModalDiv">
        <div id="maindiv" onclick="crossModalClk(event)">
            <div id="secondMaindiv">
                <div id="cross" onclick="closeModal()">X</div>
                <div id="contentDiv">
                    <div class="userprofile">
                        <div class="userlogo">
                            <svg onclick="showContent()" width="35" height="35" viewBox="0 0 45 45" xmlns="http://www.w3.org/2000/svg">
                                <path d="M22.5 7.5C24.4891 7.5 26.3968 8.29018 27.8033 9.6967C29.2098 11.1032 30 13.0109 30 15C30 16.9891 29.2098 18.8968 27.8033 20.3033C26.3968 21.7098 24.4891 22.5 22.5 22.5C20.5109 22.5 18.6032 21.7098 17.1967 20.3033C15.7902 18.8968 15 16.9891 15 15C15 13.0109 15.7902 11.1032 17.1967 9.6967C18.6032 8.29018 20.5109 7.5 22.5 7.5ZM22.5 26.25C30.7875 26.25 37.5 29.6062 37.5 33.75V37.5H7.5V33.75C7.5 29.6062 14.2125 26.25 22.5 26.25Z"></path>
                            </svg>
                        </div>
                        <div class="usercontent">
                            <p id="username" class="profileUserContent"><?php echo $name; ?></p>
                            <p id="useremail" class="profileUserContent"><?php echo $email; ?></p>
                        </div>
                    </div>

                    <div id="logoName">
                        <p>Library Management System</p>
                        <p>Organize, discover and explore with our Library Management System - your gateway to knowledge</p>
                    </div>
                    <div id="dynamiccontentmain">
                        <div id="Dynamiccontent">
                            <?php
                            if (mysqli_num_rows($resFetch) > 0) {
                                while ($row = mysqli_fetch_assoc($resFetch)) {
                                    $EachbookId = $row['bookid'];
                                    $EachBook = "SELECT * FROM books WHERE id = '$EachbookId'";
                                    $EachBookres = mysqli_query($con, $EachBook);

                                    if (mysqli_num_rows($EachBookres) > 0) {
                                        $EachRow = mysqli_fetch_assoc($EachBookres);
                                        echo '
                                    <div class="eachsection">
                                        <div class="content">
                                            <div class="bookName" style="width: 60%;"><span>Book Name: </span><span>' . substr($EachRow['bname'], 0, 20) . '</span></div>
                                            <div class="bookName catname"><span>' . substr($EachRow['categoryName'], 0, 10) . '</span></div>
                                            <div class="bookCategory"></div>
                                            <div class="bookImage">
                                            <img src="' . $EachRow["bimage"] . '" height="30" width="30" style="border-radius: 0% 30% 10% 10%; object-fit: cover;">
                                            </div>
                                        </div>
                                    </div>
                                    ';
                                    }
                                }
                            } else {
                                echo
                                '<div class="eachsection">
                                        <div class="content">
                                            <div>Note not found</div>
                                        </div>
                                    </div>

                                ';
                            }
                            ?>
                        </div>
                    </div>
                    <div id="request" onclick="showDynamicContent()">Show Request</div>
                    <form action="./auth/logOut.php" method="POST">
                        <button id="logout-btn" name="logOutSubmit" type="submit">Sign out</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        function showContent() {
            document.getElementById("superModalDiv").style.display = "block";
            document.body.style.overflow = "hidden";
        }

        function closeModal() {
            document.getElementById("superModalDiv").style.display = "none";
            document.body.style.overflow = "auto";
        }

        function crossModalClk(event) {
            if (event.target.id === "maindiv") {
                closeModal();
            }
        }

        const dynamicContent = document.getElementById("dynamiccontentmain");
        dynamicContent.style.display = "none";

        function showDynamicContent() {
            if (dynamicContent.style.display === "none" || dynamicContent.style.display === "") {
                dynamicContent.style.display = "block";
            } else {
                dynamicContent.style.display = "none";
            }
        }
    </script>
</body>

</html>