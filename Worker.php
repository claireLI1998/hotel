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
        <h2>Check current assigned work</h2>
        <form method="GET" action="Worker.php"> <!--refresh page when submitted-->
            <input type="hidden" id="countTupleRequest" name="countTupleRequest">
            Your ID: <input type="text" name="ID"> <br /><br />        
            <input type="submit" name="countTuples"></p>
        </form>

        <hr />

        <h2>Update Pet Service Status</h2>
        <form method="POST" action="Worker.php"> <!--refresh page when submitted-->
            <input type="hidden" id="updateQueryRequest" name="updateQueryRequest">
            SID: <input type="text" name="updateID"> <br /><br />        
            <input type="submit" name="updateStatus"></p>
        </form>

        <hr />

        <h2>Update Maintainance Status</h2>
        <form method="POST" action="Worker.php"> <!--refresh page when submitted-->
            <input type="hidden" id="UMS" name="UMS">
            MID: <input type="text" name="upMS"> <br /><br />        
            <input type="submit" name="updateMS"></p>
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

            $old_name = $_POST['updateID'];
            $new_name = 1;

            executePlainSQL("UPDATE pet_service SET complete= " . $new_name . " WHERE s_id=" . $old_name . "");

            
            // you need the wrap the old name and new name values with single quotations
            // executePlainSQL("UPDATE room_maintenance SET room_maintanence.complete='" . $new_name . "' 
            //     FROM room_maintenance, providemt
            //     WHERE room_maintenance.maintenance_id = providemt.maintenance_id
            //     AND providemt.worker_id = '" . $old_name . "'");
            OCICommit($db_conn);
        }

        function handleUMSRequest(){
        global $db_conn;

            $old_name = $_POST['upMS'];
            $new_name = 1;

            executePlainSQL("UPDATE room_maintenance SET complete= " . $new_name . " WHERE maintenance_id =" . $old_name . "");

            
            // you need the wrap the old name and new name values with single quotations
            // executePlainSQL("UPDATE room_maintenance SET room_maintanence.complete='" . $new_name . "' 
            //     FROM room_maintenance, providemt
            //     WHERE room_maintenance.maintenance_id = providemt.maintenance_id
            //     AND providemt.worker_id = '" . $old_name . "'");
            OCICommit($db_conn);

        }

        function print_status_result($result){
            echo "<table>";
            echo "<tr><th>MAINTENANCE ID</th></tr>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row[0] . "</td></tr>"; //or just use "echo $row[0]" 
            }

            echo "</table>";
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

        function handleInsertRequest() {
            global $db_conn;

            //Getting the values from user and insert data into the table
            $tuple = array (
                ":bind1" => $_POST['insNo'],
                ":bind2" => $_POST['insName']
            );

            $alltuples = array (
                $tuple
            );

            executeBoundSQL("insert into demoTable values (:bind1, :bind2)", $alltuples);
            OCICommit($db_conn);
        }

        

        function handleCountRequest() {
            global $db_conn;

            echo "Your Pet Service Work<br>";
            $result = executePlainSQL("SELECT pet_service.s_id, pet_service.service_type, pet_take.p_name, selectroom.room_number, pet_take.guest_id  
                FROM pet_service, dops, pet_take, selectroom    
                Where pet_service.s_id = dops.s_id AND dops.worker_id = " . $_GET['ID']. " AND pet_take.s_id = dops.s_id
                        AND pet_take.guest_id = selectroom.guest_id
                        AND pet_service.complete = 0" );

            print_service_result($result);
            echo"<br />";


            echo"Your Current remaining number of Maintenance Work:";

            $result_total = executePlainSQL("SELECT COUNT(*)
                            FROM providemt p, room_maintenance r
                            WHERE r.complete = 0 AND r.maintenance_id = p.maintenance_id AND p.worker_id = " . $_GET['ID']. "");

            while (($row = oci_fetch_row($result_total)) != false) {
                echo "<br>$row[0]<br>";
            }

            $resultm = executePlainSQL("SELECT p.maintenance_id FROM providemt p, room_maintenance r Where r.complete = 0 AND r.maintenance_id = p.maintenance_id AND p.worker_id = " . $_GET['ID']. ""); 
            while (($row = oci_fetch_row($resultm)) != false) {
                echo "<br>MID: $row[0]<br>";
            }

            
        }



        function print_service_result($result) { //prints results from a select statement
            echo "<table>";
            echo "<tr><th>SERVICE ID</th><th>SERVICE TYPE</th><th>PET NAME</th><th>ROOM NUMBER</th><th>GUEST ID</th></tr>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row[0] . 
                    "</td><td>" . $row[1] .
                    "</td><td>" . $row[2] .
                    "</td><td>" . $row[3] .
                    "</td><td>" . $row[4] ."</td></tr>"; //or just use "echo $row[0]" 
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
                } else if (array_key_exists('UMS', $_POST)) {
                    handleUMSRequest();
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
                } 

                disconnectFromDB();
            }
        }

		if (isset($_POST['reset'])|| isset($_POST['updateMS']) || isset($_POST['updateSubmit'])|| isset($_POST['insertSubmit']) || isset($_POST['updateStatus'])) {
            handlePOSTRequest();
        } else if (isset($_GET['countTupleRequest'])) {
            handleGETRequest();
        }
		?>
	</body>
</html>

