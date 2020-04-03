<!DOCTYPE html>
<html>
<head>
    <title><%=(locals.title)?title: ''%></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font-family: Arial;
            margin: 0;
        }
        .header {
            padding: 150px;
            text-align: center;
            background-size: cover;
            background-image: url("resort.jpg");
            background-repeat: no-repeat;
            color: ivory;
        }
        .header h1 {
            font-size: 40px;
            font-family: "Bookman Old Style";
        }
        .header h2{
            font-family: "Bookman Old Style";
        }
        .navbar {
            overflow: hidden;
            background-color: midnightblue;
        }
        .navbar a {
            float: left;
            display: block;
            color: white;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
        }
        .navbar a.right {
            float: right;
        }
        .navbar a:hover {
            background-color: aliceblue;
            color: black;
        }
        .row {
            display: -ms-flexbox; /* IE10 */
            display: flex;
            -ms-flex-wrap: wrap; /* IE10 */
            flex-wrap: wrap;
        }
        #bar{
            -ms-flex: 100%; /* IE10 */
            flex: 30%;
            background-color: crimson;
            padding: 15px;
        }
        .main {
            -ms-flex: 100%; /* IE10 */
            flex: 70%;
            background-color: white;
            padding: 20px;
        }
        #bar h2{
            font-family: "Century Gothic";
            font-weight:bold;
            color: white;
        }
        #bar p{
            font-family: "Century Gothic";
            color: white;
        }
        .fakeimg {
            background-color: #aaa;
            width: 100%;
            padding: 20px;
        }
        .footer {
            padding: 20px;
            text-align: center;
            background: #ddd;
        }
        @media screen and (max-width: 400px) {
            .navbar a {
                float: none;
                width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="header">
    <h1>WELCOME TO CROWN HOTEL</h1>
    <h2>please enjoy your stay</h2>
</div>

<div class="navbar">
    <a href= "final1.php">MAKE RESERVATIONS</a>
    <a href="requestM.php">MAINTENANCE</a>
    <a href="requestP.php">PET SERVICE</a>
    <a href="Manager.php" class="right">MANAGER</a>
    <a href="Worker.php" class="right">WORKER</a>
</div>


<div id="bar">
    <h2>Recent Update</h2>
    <p>Due to the unprecedented implications COVID-19 has had
        on the hospitality industry, Crown Hotels & Resorts has temporarily
        suspended operations at multiple properties.</p>
</div>
<div class="main">
    <h2>ABOUT US</h2>
    <p>A Crown Towers hotel is unparalleled in every way. From its highly personalised service and extraordinary design, it is where travellers go to experience the very pinnacle of hotel luxury.</p>

    <p>Renowned as one of the finest hotels in the world, Crown Towers Melbourne is the ultimate in luxury located on the bank of the Yarra River. The luxurious surrounds create an atmosphere of unmatched grandeur complemented by exemplary service. Crown Towers Melbourne is home to exceptional fine dining restaurants, the tranquil Crown Spa, internationally-acclaimed shopping and iconic bars as well as impressive pool and gymnasium facilities. </p>
 
<div class="footer">
    <h2>CPSC304 Project</h2>
</div>
