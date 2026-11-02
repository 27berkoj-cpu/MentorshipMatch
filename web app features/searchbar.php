<?php
    require_once 'server.php';
    $sql_statement = "SELECT * FROM /*From the table containing all user data (both mentor or students)*/ ";
    $sql_advanced = " AND WHERE "; #This will hold the advanced search terms, combine this later down below with html/javascript
    $sql_input = "WHERE "; #user input sql statements
    //$result = mysqli_query($conn, $sql_statement); uncomment when server.php changes to mysqli
    if(isset($_GET['search'])){
        $search_term = $_GET['search'];
        $sql_statement .= "WHERE name LIKE '%$search_term%'";
    }
    /*
        if(user inputs another search term (like country or age) && $_GET['search'] is valid){
            then produce another sql statement.
            An array will hold all the columns of the sql database and as the user types their options, the advanced search will produce a list of the different options that they can then click on. 
        }
    */
?>
<!--This form is the action search bar, set to get so people can easily share it with others if necessary-->


<?php
    /*if(mysqli_num_rows($result) > 0){
        while($row = mysqli_fetch_assoc($result)){
            echo "Id: ".$row['id'];
            echo "Username: ".$row['username'];
            echo "First Name: ".$row['firstname'];
            echo "Last Name: ".$row['lastname'];
            echo "Email: ".$row['email'];
            //add any more necessary rows regarding the results of each mentor/student.
        }
    }This prints out all the results of the database rows, uncomment when server.php uses mysqli*/
?>