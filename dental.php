<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>DentalCare Clinic</title>
  <link rel="stylesheet" href="dent_style.css" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
</head>

<body>
  <header>
    <div class="container">
      <div class="logo">
        <img src="images/logo.png" alt="Clinic Logo" />
        <h2 class="clinic-name">Bolasco-Servanez Dental Clinic</h2>
      </div>
      <nav class="nav-links">
        <a href="#services">Services</a>
        <a href="#about">About</a>
        <a href="#contact">Contact</a>

      </nav>
    </div>
  </header>

  <section class="hero">
    <h1>Your Smile Is Our Priority</h1>
    <p>
      Experience exceptional dental care with our team of experienced<br />
      professionals in a comfortable environment.
    </p>
    <div class="buttons">
    <a href="book.php" class="primary">Book Appointment</a>
    </div>
  </section>

  <section class="services-section" id="services">
    <h2 class="section-title">Our Services</h2>
    <div class="service-cards">
      <div class="service-card">
        <i class="fa-regular fa-calendar service-icon"></i>
        <h3 class="service-title">General Dentistry</h3>
        <p class="service-description">
          Comprehensive care for your dental health including<br />
          cleanings, fillings, and preventive treatments.
        </p>
      </div>

      <div class="service-card">
        <i class="fa-regular fa-user service-icon"></i>
        <h3 class="service-title">Cosmetic Dentistry</h3>
        <p class="service-description">
          Enhance your smile with our cosmetic services<br />
          including whitening, veneers, and smile makeovers.
        </p>
      </div>

      <div class="service-card">
        <i class="fa-regular fa-calendar service-icon"></i>
        <h3 class="service-title">Orthodontics</h3>
        <p class="service-description">
          Straighten your teeth with our modern orthodontic<br />
          treatments including invisible aligners.
        </p>
      </div>
    </div>
  </section>

  <section class="about-section" id="about">
    <div class="about-container">
      <div class="about-text">
        <h2>About Our Clinic</h2>
        <p>
          Bolasco-Servanez Dental Clinic has been providing exceptional dental services for over 15 years. Our team of experienced dental professionals is committed to ensuring your comfort and satisfaction.
        </p>
        <p>
          We use the latest technology and techniques to provide the highest quality care in a comfortable and welcoming environment.
        </p>
      </div>
      <div class="about-image">
        <img src="images/bolasco.jpg" alt="Clinic Interior" />
      </div>
    </div>
  </section>

  <section class="contact-section" id="contact">
    <div class="contact-container">
      <h2 class="contact-title">Contact Us</h2>
      <div class="contact-cards">
        <div class="contact-card">
          <h3>Get In Touch</h3>
          <div class="contact-info">
            <p><i class="fa-solid fa-phone"></i> (123) 456-7890</p>
            <p><i class="fa-solid fa-envelope"></i> info@dentalcare.com</p>
            <p><i class="fa-solid fa-location-dot"></i> 123 Dental Street, City, State 12345</p>
          </div>
        </div>

        <div class="contact-card">
          <h3>Office Hours</h3>
          <div class="office-hours">
            <div class="day">Monday - Friday</div>
            <div class="time">8:00 AM - 6:00 PM</div>
            <div class="day">Saturday</div>
            <div class="time">9:00 AM - 3:00 PM</div>
            <div class="day">Sunday</div>
            <div class="time">Closed</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <footer class="footer">
    <div class="footer-container">
      <div class="logo">
        <img src="images/logo.png" alt="Clinic Logo" />
        <h2 class="clinic-name">Bolasco-Servanez Dental Clinic</h2>
      </div>
      <p>© 2025 DentalCare Clinic. All rights reserved.</p>
    </div>
  </footer>

  <script>
    // Placeholder for interactions
    document.querySelector(".login-btn").addEventListener("click", () => {
      alert("Redirecting to login page...");
    });
  </script>
</body>
</html>
