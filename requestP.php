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
/* Customize the label (the container) */
.container {
  display: block;
  position: relative;
  padding-left: 35px;
  margin-bottom: 12px;
  cursor: pointer;
  font-size: 22px;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

/* Hide the browser's default checkbox */
.container input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
  height: 0;
  width: 0;
}

/* Create a custom checkbox */
.checkmark {
  position: absolute;
  top: 0;
  left: 0;
  height: 25px;
  width: 25px;
  background-color: #eee;
}

/* On mouse-over, add a grey background color */
.container:hover input ~ .checkmark {
  background-color: #ccc;
}

/* When the checkbox is checked, add a blue background */
.container input:checked ~ .checkmark {
  background-color: #2196F3;
}

/* Create the checkmark/indicator (hidden when not checked) */
.checkmark:after {
  content: "";
  position: absolute;
  display: none;
}

/* Show the checkmark when checked */
.container input:checked ~ .checkmark:after {
  display: block;
}

/* Style the checkmark/indicator */
.container .checkmark:after {
  left: 9px;
  top: 5px;
  width: 5px;
  height: 10px;
  border: solid white;
  border-width: 0 3px 3px 0;
  -webkit-transform: rotate(45deg);
  -ms-transform: rotate(45deg);
  transform: rotate(45deg);
}
            
        }
           
        </style>

    </head>

    <body>

    <div class="part">
        
        <div class="lay">Request Pet Service</div>
        
        <ul>
        <form method="POST" class="form-signin" action="requestP.php"> <!--refresh page when submitted-->
            <input type="hidden" id="insertQueryRequest" name="insertQueryRequest">
            <li>
                <lable>YOUR GUEST ID </lable>
                <input type="text" class="form-control" name="insGid">
            </li>   
            <li>
                <lable>YOUR PET NAME </lable>
                <input type="text" class="form-control" name="insName">
            </li> 
            <br />
            We have three types of pet service: Washing, Feeding, and Glooming. Please choose one of them.
            <br /><br /><br />
            <li>
                <lable>SERVICE TYPE </lable>
                <input type="text" class="form-control" name="insSt">
            </li> 
            <input type="submit" class="button" value="SUBMIT" name="insertSubmit"></p>
        </form>
        </ul>
        
        <div class="lay">Find Available Workers</div>
        <ul>
        <form method="GET" class="form-signin" action="requestP.php"> <!--refresh page when submitted-->
            <input type="hidden" id="projectTupleRequest" name="projectTupleRequest">
            <label class="container">worker name
                <input type="checkbox" value="worker_name" name="flag[]">
                <span class="checkmark"></span>
            </label>

            <label class="container">worker id
                <input type="checkbox" value="worker_id" name="flag[]">
                <span class="checkmark"></span>
            </label>

            <label class="container">service charges per hour
                <input type="checkbox" value="salary_per_hour" name="flag[]">
                <span class="checkmark"></span>
            </label>

            <input type="submit" class="button" value="CHECK" name="projectTuples"></p>
        </form>
        </ul>
        </div>

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
            $new_name = $_POST['newName'];

            // you need the wrap the old name and new name values with single quotations
            executePlainSQL("UPDATE demoTable SET name='" . $new_name . "' WHERE name='" . $old_name . "'");
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

        function handleInsertRequest() {
            global $db_conn;

            $result = executePlainSQL("SELECT Count(*) FROM pet_service");

            if (($row = oci_fetch_row($result)) != false) {
                 $sidtemp = $row[0];
            }
 
            $ctemp = 0;

            //Getting the values from user and insert data into the table
            $tuple = array (
                ":bind1" => $_POST['insSt'],
                ":bind2" => $sidtemp,
                ":bind3" => $ctemp);

            $alltuples = array (
                $tuple
            );

            $ask = array (
                ":bind4" => $sidtemp,
                ":bind5" => $_POST['insGid']);

            $allask = array (
                $ask
            ); 
            
            $take = array (
                ":bind6" => $sidtemp,
                ":bind7" => $_POST['insGid'],
                ":bind8" => $_POST['insName']);

            $alltake = array (
                $take
            );
            executeBoundSQL("insert into pet_service values (:bind1, :bind2, :bind3)", $alltuples);
            executeBoundSQL("insert into ask_for_ps values (:bind4, :bind5)", $allask);
            executeBoundSQL("insert into pet_take values (:bind6, :bind7, :bind8)", $alltake);
            OCICommit($db_conn);
        }

        function handleProjectRequest() {
            global $db_conn;
            
            if(isset($_GET["flag"])) $ah = $_GET["flag"];
            else $ah = "null";


            if(isset($_GET["flag"])){
                foreach($ah as $value){
                    
                }
            }

            if(count($ah) == 1){
                $result = executePlainSQL("SELECT $ah[0]
                                        FROM hotel_worker");
            } else if(count($ah) == 2){
                $result = executePlainSQL("SELECT $ah[0], $ah[1]
                                        FROM hotel_worker");

            } else if(count($ah) == 3){
                $result = executePlainSQL("SELECT $ah[0], $ah[1], $ah[2]
                                        FROM hotel_worker");

            }
            
            printResult($result);
        }

        function printResult($result) { //prints results from a select statement
            echo "<table>";
            echo "<tr><th>worker name</th><th>worker ID</th><th>charges per hour</th></tr>";
    
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
                }

                disconnectFromDB();
            }
        }

        // HANDLE ALL GET ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handleGETRequest() {
            if (connectToDB()) {
                if (array_key_exists('projectTuples', $_GET)) {
                    handleProjectRequest();
                }

                disconnectFromDB();
            }
        }

		if (isset($_POST['updateSubmit']) || isset($_POST['insertSubmit'])) {
            handlePOSTRequest();
        } else if (isset($_GET['projectTupleRequest'])) {
            handleGETRequest();
        }
		?>
	</body>
</html>