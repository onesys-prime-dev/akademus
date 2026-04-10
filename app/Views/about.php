<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Akademus</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-inner">
            <a href="#" class="navbar-brand">
                <img src="<?= base_url('assets/images/logo.png') ?>" alt="Akademus Logo">
                <span>Akademus University</span>
            </a>
            <ul class="navbar-menu">
                <li><a href="<?= base_url('/') ?>">Home</a></li>
                <li><a href="<?= base_url('/about') ?>">About Us</a></li>
                <li><a href="<?= base_url('/admissions') ?>">Admissions</a></li>
                <li><a href="<?= base_url('/contact') ?>">Contact</a></li>
            </ul>
        </div>
    </nav>
    <section class="about-section">
        <div class="container">
            <h1>About Akademus University</h1>
            <p>Akademus University is a prestigious institution dedicated to providing quality education and fostering innovation. Established in 1990, we have been committed to nurturing the minds of future leaders and professionals.</p>
            <p>Our mission is to empower students with knowledge, skills, and values that will enable them to succeed in their careers and make a positive impact on society. We offer a wide range of undergraduate and postgraduate programs across various disciplines, including science, technology, arts, and humanities.</p>
            <p>At Akademus University, we pride ourselves on our diverse and inclusive community, state-of-the-art facilities, and a faculty composed of experienced educators and industry experts. We are dedicated to creating an environment that encourages creativity, critical thinking, and lifelong learning.</p>
        </div>
    </section>
</body>

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
        