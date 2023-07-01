<?php
include "../common/backendConnector.php";

// db connection in (lms) db
$con = mysqli_connect($host, $dbUserName, $dbPassword, $database);
if (!$con) {
    die("DB connection failed");
}


// for delete content
if (isset($_GET['taken'])) {
    $id = $_GET['taken'];
    $sqlDel = "UPDATE `bookorder` SET `istaken`='1' WHERE `id`='$id'";

    if (mysqli_query($con, $sqlDel)) {
        header("Location: " . $_SERVER['PHP_SELF']);
    } else {
        echo "Cannot Update";
    }
}
if (isset($_GET['return']) && $_GET['bookid']) {
    $id = $_GET['return'];
    $bookid = $_GET['bookid'];
    $sqlDel = "UPDATE `bookorder` SET `isreturn`='1' WHERE `id`='$id'";

    if (mysqli_query($con, $sqlDel)) {
        $bookQuery = "SELECT bquantity FROM books WHERE id = '$bookid'";
        $resBook = mysqli_query($con, $bookQuery);

        if ($resBook) {
            if (mysqli_num_rows($resBook) < 1) {
                die("Book not found");
            }

            $rowBook = mysqli_fetch_assoc($resBook);
            $currentQuantity = intval($rowBook['bquantity']);

            // Update book quantity
            $newQuantity = $currentQuantity + 1;
            $sql = "UPDATE books SET bquantity = '$newQuantity' WHERE id = '$bookid'";
            $resUpdate = mysqli_query($con, $sql);

            if ($resUpdate) {
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            } else {
                echo "Failed to update book quantity: " . mysqli_error($con);
                exit();
            }
        } else {
            echo "Error: " . mysqli_error($con);
            exit();
        }
    } else {
        echo "Cannot Update";
    }
}


// ================================ for pagination (start) ==========================================
$querytotalnumberROw = "SELECT COUNT(*) as total FROM bookorder WHERE isreturn=0";
$resultRowNum = mysqli_query($con, $querytotalnumberROw);
$rowNumbers = mysqli_fetch_assoc($resultRowNum);
$totalRowNumber = $rowNumbers['total'];

// for total page 
$recordsPerPage = 10;
$totalPages = ceil($totalRowNumber / $recordsPerPage);

// my current page
$currentPage = isset($_GET['page']) ? $_GET['page'] : 1;

$offset = ($currentPage - 1) * $recordsPerPage;


$sqlFetch = "SELECT * FROM bookorder WHERE isreturn=0 ORDER BY id DESC LIMIT $offset, $recordsPerPage";
$resFetch = mysqli_query($con, $sqlFetch);



// ================================ for Search ==========================================


if (isset($_GET['search'])) {
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    // Escape the search value to prevent SQL injection
    $search = mysqli_real_escape_string($con, $search);

    // Check if the search value is set
    if (!empty($search)) {
        // Query with the search value
        $sqlFetch = "SELECT * FROM users WHERE name LIKE '%$search%'";
        $resFetch = mysqli_query($con, $sqlFetch);
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Request</title>
    <link rel="stylesheet" href="./sidestyles.css">
    <link rel="stylesheet" href="./CSS/messagemodel.css">
    <link rel="stylesheet" href="../CSS/globals.css">

    <style>
        #action,
        #snTab {
            max-width: 30px;
        }
    </style>
</head>

<body>
    <?php include "./sideNav.php"; ?>


    <div id="content">
        <div id="semicontent">
            <div id="maincontent">
            </div>
            <div class="contentTable">
                <h2>Requested List Of Books:-</h2><br>
                <table>
                    <tr>
                        <th id='snTab'>S.N</th>
                        <th>Name</th>
                        <th>Book name</th>
                        <th>Book Category</th>
                        <th>Book Image</th>
                        <th colspan='2' id='action' style="max-width: 100px;">Action</th>
                    </tr>
                    <?php
                    if (isset($_GET['page'])) {
                        $index = ($_GET['page'] - 1) * ($recordsPerPage) + 1;
                    } else {
                        $index = 1;
                    }
                    if (mysqli_num_rows($resFetch) > 0) {
                        while ($row = mysqli_fetch_assoc($resFetch)) {
                            $EachbookId = $row['bookid'];
                            $EachBook = "SELECT * FROM books WHERE id = '$EachbookId'";
                            $EachBookres = mysqli_query($con, $EachBook);

                            if (mysqli_num_rows($EachBookres) > 0) {
                                $EachRow = mysqli_fetch_assoc($EachBookres);
                                echo "
                                        <tr>
                                            <td>" . $index . "</td>
                                            <td>" . $row["username"] . "</td>
                                            <td>" . $EachRow["bname"] . "</td>
                                            <td>" . $EachRow["categoryName"] . "</td>
                                            <td><img src='" . $EachRow["bimage"] . "' height='30' width='30' style='border-radius: 0% 30% 10% 10%; object-fit: cover;'></td>
                                            <td style='max-width: 50px;'>
                                                <a href='?taken=" . $row["id"] . "'>
                                                <svg width='16' height='16' viewBox='0 0 20 20' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                                    <path d='M10 0C4.5 0 0 4.5 0 10C0 15.5 4.5 20 10 20C15.5 20 20 15.5 20 10C20 4.5 15.5 0 10 0ZM8 15L3 10L4.41 8.59L8 12.17L15.59 4.58L17 6L8 15Z' fill='" . ($row['istaken'] == '0' ? 'black' : 'green') . "'/>
                                                </svg>            
                                            </a>
                                            </td>                                       
                                            <td style='max-width: 50px;'>
                                                <a href=\"?return=" . $row["id"] . "&bookid=" . $EachRow["id"] . "\">
                                                    <svg width='20' height='20' viewBox='0 0 20 23' xmlns='http://www.w3.org/2000/svg'>                      
                                                        <path d='M12 0C12.5304 0 13.0391 0.210714 13.4142 0.585786C13.7893 0.960859 14 1.46957 14 2V18L7 15L0 18V2C0 0.89 0.9 0 2 0H12ZM6 11L12.25 4.76L10.84 3.34L6 8.18L3.41 5.59L2 7L6 11Z' />
                                                    </svg>
                                                </a>
                                            </td>
                                        </tr>";
                                $index++;
                            } else {
                                // Handle no results returned
                                echo "No results found.";
                            }
                        }
                    } else {
                        echo "<div class='pageNotFound'>Books Not Found</div>";
                    }
                    ?>
                </table>
            </div>
            <div class="pagination">
                <?php
                if ($currentPage > 1) {
                    echo '<a href="?page=' . ($currentPage - 1) . '" class="leftArrow">&laquo;</a>';
                } else {
                    echo '<a class="leftArrow">&laquo;</a>';
                }

                for ($i = 1; $i <= $totalPages; $i++) {
                    $activeClass = ($currentPage == $i) ? 'activePage' : '';
                    echo '<a href="?page=' . $i . '" class="' . $activeClass . '">' . $i . '</a>';
                }

                if ($currentPage < $totalPages) {
                    echo '<a href="?page=' . ($currentPage + 1) . '" class="rightArrow">&raquo;</a>';
                } else {
                    echo '<a class="rightArrow">&raquo;</a>';
                }
                ?>
            </div>
        </div>
    </div>
</body>

</html>