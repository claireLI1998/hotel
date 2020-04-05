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

        .reserve{
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
          margin: auto;
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

        .ar{
            max-width: 600px;
            padding-top: 10px;
            text-align: center;
            color:black;
            margin:auto;
            font-size:25px;
            font-family:"Verdana";
            font-weight: bold;
            margin-bottom: 50px;
        }

        .far{
            color:#DCD0C0;
            font-size:15px;
        }
    </style>
    </head>

    <body>
        <div class="part">
            <div class="reserve">MAKE RESERVATION</div>
            <ul>
                
                <form class="form-signin" method="POST" action="final1.php"> <!--refresh page when submitted-->
                    <input type="hidden" id="insertQueryRequest" name="insertQueryRequest">
                               
                    Already Has A Reservation? You can update here.<br>
                    <a href="upres.php" class="far" target="_blank">Update</a>

                    <li>
                        <label>NAME</label>
                        <input type="text" name="insName" class="form-control" required autofocus>
                    </li>

                    <li>
                        <label>ID</label>
                        <input type="text" name="insid" class="form-control" required autofocus>
                    </li>

                    <li>
                        <label>Email</label>
                        <input type="text" name="insEmail" class="form-control" required autofocus>
                    </li>

                    <li>
                        <label>PET NAME</label>
                        <input input type="text" name="insPname" class="form-control" required autofocus>
                    </li>

                    <li>
                        <label>PET TYPE  </label>
                        <input input type="text" name="insPtype" class="form-control" required autofocus>
                    </li>

                    <li>
                        <label>CHECK IN DATE  </label>
                        <input input type="text" name="insCin" class="form-control" required autofocus>
                        (Format:YYYY-MM-DD)
                    </li>

                    <li>
                        <label>CHECK OUT DATE</label>
                        <input input type="text" name="insCout" class="form-control" required autofocus>
                        (Format:YYYY-MM-DD)
                    </li>

                    <li>
                        <label>ROOM NUMBER*</label>
                        <input input type="text" name="insRnumber" class="form-control" required autofocus>
                    </li>

                    <a href="room.php" class="far" target="_blank">Find available room</a>


                    <input type="submit" class="button" value="SUBMIT" name="insertSubmit"></p>
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

        // function handleUpdateRequest() {
        //     global $db_conn;

        //     $old_name = $_POST['oldName'];
        //     $new_name = $_POST['newName'];

        //     // you need the wrap the old name and new name values with single quotations
        //     executePlainSQL("UPDATE demoTable SET name='" . $new_name . "' WHERE name='" . $old_name . "'");
        //     OCICommit($db_conn);
        // }

        // function handleResetRequest() {
        //     global $db_conn;
        //     // Drop old table
        //     executePlainSQL("DROP TABLE demoTable");

        //     // Create new table
        //     echo "<br> creating new table <br>";
        //     executePlainSQL("CREATE TABLE demoTable (id int PRIMARY KEY, name char(30))");
        //     OCICommit($db_conn);
        // }

        function handleInsertRequest() {
            global $db_conn;

            $result = executePlainSQL("SELECT Count(*) FROM reservation");

            if (($row = oci_fetch_row($result)) != false) {
                 $ridtemp = $row[0];
            }

            //Getting the values from user and insert data into the table
            $guest = array (
                ":bind1" => $_POST['insName'],
                ":bind2" => $_POST['insid'],
                ":bind3" => $_POST['insEmail']
            );

           $pet = array(
                ":bind4" => $_POST['insPname'],
                ":bind5" => $_POST['insPtype'],
                ":bind6" => $_POST['insid']
           );

           $reservation = array(
                ":bind7" => $_POST['insCin'],
                ":bind8" => $_POST['insCout'],
                ":bind9" => $_POST['insid'],
                ":bind10" => $ridtemp
           );
           
            $selectroomtemp = array (
                ":bind11" => $_POST['insRnumber'],
                ":bind12" => $_POST['insid']
            );

            $allguest = array (
                $guest
            );

            $allpet= array (
                $pet
            );
            
            $allres= array (
                $reservation
            );
            
            $allsel = array (
                $selectroomtemp
            );

            executeBoundSQL("insert into guest values (:bind1, :bind2, :bind3)", $allguest);
            executeBoundSQL("insert into registerpet values (:bind4, :bind5, :bind6)", $allpet);
            executeBoundSQL("insert into reservation values (:bind7, :bind8, :bind9, :bind10)", $allres);
            executeBoundSQL("insert into selectroom values (:bind11, :bind12)", $allsel);
            executePlainSQL("UPDATE pet_free_room SET occupy = " 
. 1 . " WHERE room_number= " . $_POST['insRnumber'] . "");
            executePlainSQL("UPDATE pet_friendly_room SET occupy_pet = " 
. 1 . " WHERE room_number= " . $_POST['insRnumber'] . "");

            $rpp = executePlainSQL("SELECT Count(*) FROM reservation");

            echo"Your Reservation ID: $ridtemp <br>";

            OCICommit($db_conn);
        }

        // function handleCountRequest() {
        //     global $db_conn;
        //     echo "<br>Available Pet-Free Room:<br>";
        //     $result = executePlainSQL("SELECT * FROM pet_free_room Where occupy = 0");
 
        //     while (($row = oci_fetch_row($result)) != false) {
        //         echo "<br>$row[0]<br>";
        //     }

        //     echo "<br>Available Pet-Friendly Room:<br>";
        //     $result = executePlainSQL("SELECT * FROM pet_friendly_room Where occupy_pet = 0");
 
        //     while (($row = oci_fetch_row($result)) != false) {
        //         echo "<br>$row[0]<br>";
        //     }
        // }

        // HANDLE ALL POST ROUTES
	       // A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handlePOSTRequest() {
            if (connectToDB()) {
                if (array_key_exists('insertQueryRequest', $_POST)) {
                    handleInsertRequest();
                }

                disconnectFromDB();
            }
        }
        

		if (isset($_POST['insertSubmit'])) {
            handlePOSTRequest();
        } 
		?>
	</body>
</html>