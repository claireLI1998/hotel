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
        <link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <style>

            .part{
            padding-top: 0px;
            padding-bottom: 0px;
            background-image:url("pool.jpg"); 
            background-size:100% 100%;
            }

            .lay{
            margin:auto;
            max-width: 600px;
            padding-top: 30px;
            text-align: center;
            color: ivory;
            font-size:25px;
            font-family:"Verdana";
            font-weight: bold;
            margin-bottom: 50px;
            }

            .form-signin {
            max-width: 330px;
            color:ivory;
            padding:0px;
            margin:auto;
            font-size:15px;
            font-family:"Arial";
            }

            .button{
            background-color: #C0B283;
            border: none;
            color: white;
            margin-top: 50px;
            margin-bottom: 30px;
            margin-left:100px;
            margin-right:100px;
            padding-left: 20px;
            padding-right: 20px;
            padding-top: 5px;
            padding-bottom: 5px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
        }

        </style>
    </head>

    <body>
        <div class="part">
            <div class="lay">ASSIGN MAINTENANCE</div>
            <ul>
                <form class="form-signin" method="POST" action="Manager.php"> <!--refresh page when submitted-->
                    <input type="hidden" id="insertQueryRequest" name="insertQueryRequest">
                    <li>
                        <label>WORKER ID</label>
                        <input type="text" name="insW" class="form-control" required autofocus>
                    </li>

                    <li>
                        <label>MAINTENANCE ID</label>
                        <input type="text" name="insM" class="form-control" required autofocus>
                    </li>
                    
                    <input type="submit" class="button" value="ASSIGN" name="insertSubmit"></p>
                </form>

            </ul>
        
        <hr />

            <div class="lay">ADD HOTEL WORKER</div>
            <ul>
                <form class="form-signin" method="POST" action="Manager.php"> <!--refresh page when submitted-->
        
                    <input type="hidden" id="BB" name="BB">
                    <li>
                        <label>WORKER NAME</label>
                        <input type="text" name="insN" class="form-control" required autofocus>
                    </li>

                    <li>
                        <label>WORKER ID</label>
                        <input type="text" name="insI" class="form-control" required autofocus>
                    </li>

                    <li>
                        <label>SALARY</label>
                        <input type="text" name="insS" class="form-control" required autofocus>
                    </li>

                    <input type="submit" class="button" value="ADD" name="insertSubmit"></p>
                </form>
            </ul>
        
        <hr />
        
            <div class="lay">DISMISS HOTEL WORKER</div>
            
            <ul>

                <form class="form-signin" method="POST" action="Manager.php"> <!--refresh page when submitted-->
                    <input type="hidden" id="updateQueryRequest" name="updateQueryRequest">
                    The values are case sensitive and if you enter in the wrong case, the update statement will not do anything.<br />
                    Type in the ID of the hotel worker that you want to dismiss.
                    <li>
                        <label>WORKER ID</label>
                        <input type="text" name="oldName" class="form-control" required autofocus>
                    </li>
                    
                    <input type="submit" class="button" value="UPDATE" name="updateSubmit"></p>
                </form>
            </ul>

        <hr />

            <div class="lay">CHECK HOTEL WORKERS</div>
                <ul>
                <form class="form-signin" method="GET" action="Manager.php"> <!--refresh page when submitted-->
                    <input type="hidden" id="countTupleRequest" name="countTupleRequest">
                    <input type="submit" class="button" name="countTuples"></p>
                </form>
                </ul>
        
        <hr />

            <div class="lay">Check Current Reservation Details</div>
                <ul>
                    <form class="form-signin" method="GET" action="info.php">
                        <input type="hidden" id="displayTupleRequest" name="displayTupleRequest">
                        <input type="submit" class="button" value="CHECK" name="displayTuples"></p>
                    </form>
                </ul>

        <hr />

            <div class="lay">Check total pet service charges</div>
                <ul>
                    <form method="GET" class="form-signin" action="totalsp.php"> <!--refresh page when submitted-->
                        <input type="hidden" id="countTupleRequest" name="countTupleRequest">

                        <input type="submit" class="button" value="CHECK" name="countTuples"></p>
                    </form>
                </ul>

            

        <hr />

            <div class="lay">Check hotel workers information</div>
                <ul>
                    <form method="GET" class="form-signin" action="Manager.php"> <!--refresh page when submitted-->
                        <input type="hidden" id="selectTupleRequest" name="selectTupleRequest">
                        Find the information about the hotel workers whose salary is above the salary threshold<br />
                        <li>
                            <label>SALARY THRESHOLD</label>
                            <input type="number" name="min_sal" class="form-control" required autofocus>
                        </li>

                        <input type="submit" class="button" value="SEARCH" name="selectTuples"></p>
                    </form>
                </ul>
            
        </div>

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

        function connectToDB() {
            global $db_conn;

            // Your username is ora_(CWL_ID) and the password is a(student number). For example, 
            // ora_platypus is the username and a12345678 is the password.
            $db_conn = OCILogon("ora_ruolin82", "a31764160", "dbhost.students.cs.ubc.ca:1522/stu");

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
            executePlainSQL("Delete from hotel_worker WHERE worker_id = '" . $old_name . "'");
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

        function handleCountRequest() {
            global $db_conn;

            $result = executePlainSQL("SELECT ask_for_ps.guest_id, SUM(psc.service_price) 
                                       FROM psc, pet_service, ask_for_ps
                                       WHERE pet_service.s_id = ask_for_ps.s_id 
                                                AND psc.service_type_name = pet_service.service_type
                                       GROUP BY(ask_for_ps.guest_id)");

            printResult($result);
        }

        function printResult($result) { //prints results from a select statement
            echo "<table>";
            echo "<tr><th>GUEST ID</th><th> </th><th>TOTAL PRICE</th></tr>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row[0] . 
                    "</td><td>" . $row[1] .
                    "</td></tr>"; //or just use "echo $row[0]" 
            }

            echo "</table>";
        }

       function handleDisplayRequest(){
            global $db_conn;

            $result = executeBoundSQL("SELECT reservation.guest_id, reservation.rid, guest.guest_name, selectroom.room_number 
                                        FROM reservation, guest, selectroom
                                        WHERE reservation.guest_id=guest.guest_id");
            while (($row = oci_fetch_row($result)) != false) {
                echo "<br> GUEST ID: " . $row[0] . "RESERVATION ID:" . $row[1] . "GUEST NAME:" . $row[2] . "ROOM NUMBER:" . $row[3] .  "<br>";
            }
       }

       function handleSelectRequest(){
           global $db_conn;

           $result = executePlainSQL("SELECT hotel_worker.worker_name, hotel_worker.worker_id, hotel_worker.salary_per_hour
                                      FROM hotel_worker
                                      WHERE hotel_worker.salary_per_hour > ". $_GET['min_sal']. "");
           printInfoResult($result);
       }

       function printInfoResult($result) { //prints results from a select statement
        echo "<table>";
        echo "<tr><th>WORKER NAME</th><th>WORKER ID</th><th>SALARY</th></tr>";

        while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
            echo "<tr><td>" . $row[0] . 
                "</td><td>" . $row[1] .
                "</td><td>" . $row[2] .
                "</td></tr>"; //or just use "echo $row[0]" 
        }

        echo "</table>";
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
                } else if(array_key_exists('displayTuples', $_GET)){
                    handleDisplayRequest();
                } else if(array_key_exists('selectTuples', $_GET)){
                    handleSelectRequest();
                }
                disconnectFromDB();
            }
        }

        if (isset($_POST['reset'])||isset($_POST['BB']) || isset($_POST['updateSubmit']) || isset($_POST['insertSubmit'])) {
            handlePOSTRequest();
        } else if (isset($_GET['countTupleRequest'])||isset($_GET['displayTupleRequest']) || isset($_GET['selectTupleRequest'])) {
            handleGETRequest();
        }
        ?>
    </body>
</html>