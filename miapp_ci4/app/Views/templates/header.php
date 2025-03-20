<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">

    <title>Newsfeed - Social Network</title>
    <link rel="shortcut icon" href="../../../public/images/favicon.png" type="image/x-icon">


    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="../../../public/css/style.css">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <!-- Icons FontAwesome 4.7.0 -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"  type="text/css" />



</head>
<body>
    <div class="navbar">
        <div class="navbar_menuicon" id="navicon">
            <i class="fa fa-navicon"></i>
        </div>
        <div class="navbar_logo">
            <img src="images/logo.png" alt="" />
        </div>
        <div class="navbar_page">
            <span>> NEWSFEED</span>
        </div>
        <div class="navbar_search">
            <form method="" action="/">
                <input type="text" placeholder="Search people.." />
                <button><i class="fa fa-search"></i></button>
            </form>
        </div>
        <div class="navbar_icons">
            <ul>
                <li id="friendsmodal"><i class="fa fa-user-o"></i><span id="notification">5</span></li>
                <li id="messagesmodal"><i class="fa fa-comments-o"></i><span id="notification">2</span></li>
                <a href="" style="color:white"><li><i class="fa fa-globe"></i></li></a>
            </ul>
        </div>
        <div class="navbar_user" id="profilemodal" style="cursor:pointer">
            <img src="../../../public/images/user.jpg" alt="" />
            <span id="navbar_user_top">Jonh Hamstrong<br><p>User</p></span><i class="fa fa-angle-down"></i>
        </div>
    </div>

    <div class="all">