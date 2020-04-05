<!--Test Oracle file for UBC CPSC304 2018 Winter Term 1
  Created by Jiemin Zhang
  Modified by Simona Radu
  Modified by Jessica Wong (2018-06-22)
  This file shows the very basics of how to execute PHP commands
  on Oracle.  
  Specifically, it will drop a table, create a table, insert values
  update values, and then query for values
 
  IF YOU HAVE A TABLE CALLED "demoTable" IT WILL BE DESTROYED

  The script assumes you already have a server set up
  All OCI commands are commands to the Oracle libraries
  To get the file to work, you must place it somewhere where your
  Apache server can run it, and you must rename it to have a ".php"
  extension.  You must also change the username and password on the 
  OCILogon below to be your ORACLE username and password -->

<html>
    <head>
        <title>CPSC 304 PHP/Oracle Demonstration</title>
    </head>

    <body>

        <h2>Check Current Reservation Details</h2>
        <form method="GET" action="info.php">
            <input type="hidden" id="displayTupleRequest" name="displayTupleRequest">
            If you want to know the custmer name related to a reservation, please type in "guest". Or, if you             want to know the room related to a reservation, please type in"selectroom".
            Your choice: <input type="text" name="insJ"> <br /><br />
            <input type="submit" name="displayTuples"></p>
        </form>

        
        <?php
        //this tells the system that it's no longer just parsing html; it's now parsing PHP

        $success = True; //keep track of errors so it redirects the page only if there are no errors
        $db_conn = NULL; // edit the login credentials in connectToDB()
        $show_debug_alert_messages = False; // set to True if you want alerts to show you which methods are being triggered (see how it is used in debugAlertMessage())

        function debugAlertMessage($message) {
            global $show_debug_alert_messages;

            if ($show_debug_alert_messages) {
                echo "<script type='text/javascript'>alert('" . $message . "');</script>";
            }
        }

        function executePlainSQL($cmdstr) { //takes a plain (no bound variables) SQL command and executes it
            //echo "<br>running ".$cmdstr."<br>";
            global $db_conn, $success;	

            $statement = OCIParse($db_conn, $cmdstr); 
            //There are a set of comments at the end of the file that describe some of the OCI specific functions and how they work

            if (!$statement) {
                echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
                $e = OCI_Error($db_conn); // For OCIParse errors pass the connection handle
                echo htmlentities($e['message']);
                $success = False;
            }

            $r = OCIExecute($statement, OCI_DEFAULT);
            if (!$r) {
                echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
                $e = oci_error($statement); // For OCIExecute errors pass the statementhandle
                echo htmlentities($e['message']);
                $success = False;
            }

            return $statement;
        }

        function executeBoundSQL($cmdstr, $list) {
            /* Sometimes the same statement will be executed several times with different values for the variables involved in the query.
        In this case you don't need to create the statement several times. Bound variables cause a statement to only be
        parsed once and you can reuse the statement. This is also very useful in protecting against SQL injection. 
        See the sample code below for how this function is used */

            global $db_conn, $success;
            $statement = OCIParse($db_conn, $cmdstr);

            if (!$statement) {
                echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
                $e = OCI_Error($db_conn);
                echo htmlentities($e['message']);
                $success = False;
            }

            foreach ($list as $tuple) {
                foreach ($tuple as $bind => $val) {
                    //echo $val;
                    //echo "<br>".$bind."<br>";
                    OCIBindByName($statement, $bind, $val);
                    unset ($val); //make sure you do not remove this. Otherwise $val will remain in an array object wrapper which will not be recognized by Oracle as a proper datatype
                }

                $r = OCIExecute($statement, OCI_DEFAULT);
                if (!$r) {
                    echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
                    $e = OCI_Error($statement); // For OCIExecute errors, pass the statementhandle
                    echo htmlentities($e['message']);
                    echo "<br>";
                    $success = False;
                }
            }
        }

        function printResult($result) { //prints results from a select statement
            echo "<br>Retrieved data from table demoTable:<br>";
            echo "<table>";
            echo "<tr><th>ID</th><th>Name</th></tr>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row["NID"] . "</td><td>" . $row["NAME"] . "</td></tr>"; //or just use "echo $row[0]" 
            }

            echo "</table>";
        }

        function connectToDB() {
            global $db_conn;

            // Your username is ora_(CWL_ID) and the password is a(student number). For example, 
            // ora_platypus is the username and a12345678 is the password.
            $db_conn = OCILogon("ora_zzxyrg", "a22851620", "dbhost.students.cs.ubc.ca:1522/stu");

            if ($db_conn) {
                debugAlertMessage("Database is Connected");
                return true;
            } else {
                debugAlertMessage("Cannot connect to Database");
                $e = OCI_Error(); // For OCILogon errors pass no handle
                echo htmlentities($e['message']);
                return false;
            }
        }

        function disconnectFromDB() {
            global $db_conn;

            debugAlertMessage("Disconnect from Database");
            OCILogoff($db_conn);
        }

        function handleUpdateRequest() {
            global $db_conn;

            $old_name = $_POST['oldName'];
            

            // you need the wrap the old name and new name values with single quotations
            executePlainSQL("Delete from hotel_worker WHERE worker_id = " . $old_name . "");
            OCICommit($db_conn);
        }

        function handleInsertRequest() {
            global $db_conn;

            //Getting the values from user and insert data into the table
            $ass = array (
                ":bind1" => $_POST['insW'],
                ":bind2" => $_POST['insM']);

            $allass = array (
                $ass
            );
            executeBoundSQL("insert into providemt values (:bind1, :bind2)", $allass);
            OCICommit($db_conn);
        }

        function handlePsRequest() {
            global $db_conn;

            //Getting the values from user and insert data into the table
            $ass = array (
                ":bind1" => $_POST['insID'],
                ":bind2" => $_POST['insPs']);

            $allass = array (
                $ass
            );
            executeBoundSQL("insert into dops values (:bind1, :bind2)", $allass);
            OCICommit($db_conn);
        }

        function handleBBRequest() {
            global $db_conn;

            //Getting the values from user and insert data into the table
            $tuple = array (
                ":bind1" => $_POST['insN'],
                ":bind2" => $_POST['insI'],
                ":bind3" => $_POST['insS']);

            $alltuples = array (
                $tuple
            );
            executeBoundSQL("insert into hotel_worker values (:bind1, :bind2, :bind3)", $alltuples);
            OCICommit($db_conn);
        }


        function handleResetRequest() {
            global $db_conn;
            // Drop old table
            executePlainSQL("DROP TABLE demoTable");

            // Create new table
            echo "<br> creating new table <br>";
            executePlainSQL("CREATE TABLE demoTable (id int PRIMARY KEY, name char(30))");
            OCICommit($db_conn);
        }

        function handleSelectRequest(){
           global $db_conn;

           $result = executePlainSQL("SELECT hotel_worker.worker_name, hotel_worker.worker_id
                                      FROM hotel_worker
                                      WHERE hotel_worker.salary_per_hour > ". $_GET['min_sal']. "");
           printInfoResult($result);
       }

       function printInfoResult($result) { //prints results from a select statement
        echo "<table>";
        echo "<tr><th>WORKER ID</th><th>WORKER ID</th><th>SALARY</th></tr>";

        while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
            echo "<tr><td>" . $row[0] . 
                "</td><td>" . $row[1] .
                "</td><td>" . $row[2] .
                "</td></tr>"; //or just use "echo $row[0]" 
        }

        echo "</table>";
    }


        function handleCountRequest() {
            global $db_conn;

            $result = executePlainSQL("SELECT * FROM hotel_worker");

            while (($row = oci_fetch_row($result)) != false) {
                echo "<br> Name: " . $row[0] . "ID:" . $row[1] . "<br>";
            }
        }

        function handleDisplayRequest(){
            global $db_conn;

            $temp = $_GET['insJ'];
            $guest = "guest";
            $room = "selectroom";
           

            if(strcmp($temp, $guest) == 0){
              $result = executePlainSQL("SELECT reservation.guest_id, reservation.rid, guest.guest_name
                                        FROM reservation, guest
                                        WHERE reservation.guest_id = guest.guest_id");


            while (($row = oci_fetch_row($result)) != false) {
                echo "<br> GUEST ID: " . $row[0] . "RESERVATION ID:" . $row[1] . "GUEST NAME:" . $row[2] .                   "<br>";}

            } else if (strcmp($temp, $room) == 0){
              $result = executePlainSQL("SELECT reservation.guest_id, reservation.rid, selectroom.room_number
                                        FROM reservation, selectroom
                                        WHERE reservation.guest_id = selectroom.guest_id");


            while (($row = oci_fetch_row($result)) != false) {
                echo "<br> GUEST ID: " . $row[0] . "RESERVATION ID:" . $row[1] . "ROOM:" . $row[2] .  "<br>";}
            }
            
       
          }

        // HANDLE ALL POST ROUTES
    // A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handlePOSTRequest() {
            if (connectToDB()) {
                if (array_key_exists('resetTablesRequest', $_POST)) {
                    handleResetRequest();
                } else if (array_key_exists('updateQueryRequest', $_POST)) {
                    handleUpdateRequest();
                } else if (array_key_exists('insertQueryRequest', $_POST)) {
                    handleInsertRequest();
                } else if(array_key_exists('BB', $_POST)){
                    handleBBRequest();
                }  else if(array_key_exists('insertPs', $_POST)){
                    handlePsRequest();
                }


                disconnectFromDB();
            }
        }

        // HANDLE ALL GET ROUTES
    // A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handleGETRequest() {
            if (connectToDB()) {
                if (array_key_exists('countTuples', $_GET)) {
                    handleCountRequest();
                } else if(array_key_exists('selectTuples', $_GET)){
                    handleSelectRequest();
                } else if(array_key_exists('displayTuples', $_GET)){
                    handleDisplayRequest();
                }
    
                disconnectFromDB();
            }
        }

        if (isset($_POST['reset'])||isset($_POST['BB']) || isset($_POST['updateSubmit'])|| isset($_POST['insertSubmit'])) {
            handlePOSTRequest();
        } else if (isset($_GET['countTupleRequest'])||isset($_GET['displayTupleRequest']) || isset($_GET['selectTupleRequest'])) {
            handleGETRequest();
        }
        ?>
    </body>
</html>