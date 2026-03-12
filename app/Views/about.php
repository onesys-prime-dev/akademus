<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Akademus</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        *,*::before,*::after { box sizing: border-box;  margin: 0; padding: 0; }
        body { font-family: 'Poppins', Helvetica, sans-serif;; background: #0040a6; overflow-x: hidden;}
        a { text-decoration: none; color: inherit; }
        img { display: block; max-width: 100%;}

        /* Navbar */ 

        nav .navbar {
            width: 100%; background; #fff;
            border-bottom: 1.5px solid #eef0f3;
            position: sticky; top: 0; z-index: 100;
        }

        .navbar-inner {
            display: flex; align-items: center;
            justify-content: space-between;
            height: 105px; padding: 0 50px;
        }

    
        .navbar-brand {
            display:flex;
            align-items: center;
            gap: 16px
        }

        .navbar-brand img {
            width:82px;
            height: 82px;
            border-radius: 20px;
            object-fit: cover;
        }
        .navbar-brand span {
            font-weight: 500;
            font-size : 55px;
            color: #0040a6;
            white-space: nowrap;
        }
        