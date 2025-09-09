<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Demo Finalizada</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      background: linear-gradient(to right, #e0eafc, #cfdef3);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
    }

    .card {
      background: #ffffff;
      border-radius: 15px;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
      text-align: center;
      padding: 40px;
      max-width: 600px;
      animation: fadeIn 1s ease-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .card img {
      width: 250px;
      height: auto;
      margin-bottom: 25px;
      border-radius: 10px;
    }

    h1 {
      color: #e74c3c;
      font-size: 28px;
      margin-bottom: 15px;
    }

    p {
      font-size: 18px;
      color: #333;
      margin-bottom: 30px;
    }

    button {
      background-color: #3498db;
      color: white;
      border: none;
      padding: 12px 25px;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    button:hover {
      background-color: #2980b9;
      transform: scale(1.05);
    }
    
    .whatsapp-button {
      background-color: #25D366;
      color: white;
      border: none;
      padding: 12px 25px;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
      transition: all 0.3s ease;
      text-decoration: none;
      display: inline-block;
    }

    .whatsapp-button:hover {
      background-color: #1ebd5a;
      transform: scale(1.05);
    }
  </style>
</head>
<body>

  <div class="card">
    <img src="https://i.pinimg.com/originals/9d/1f/82/9d1f82cc324e498dd5127a6ed0296dac.gif" alt="Programador trabajando" />
    <h1>La demo ha terminado</h1>
    <p>Por favor, contáctese con el desarrollador para más información.</p>
    <button onclick="contactar()">Contactar al Desarrollador</button>
    <a class="whatsapp-button" href="https://wa.me/593983957796?text=Hola%2C%20estoy%20interesado%20en%20más%20información%20sobre%20la%20demo." target="_blank">
      Contactar por WhatsApp
    </a>
  </div>

  <script>
    function contactar() {
      window.location.href = "mailto:manuelmacias698@gmail.com?subject=Interés%20en%20la%20demo&body=Hola,%20quisiera%20obtener%20más%20información%20sobre%20la%20demo.";
    }
  </script>

</body>
</html>

