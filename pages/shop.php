<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SHOP</title>
    <link rel="stylesheet" href="Cart.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../styles/shop.css">
    <link rel="stylesheet" href="../styles/footer.css">
    <link rel="stylesheet" href="../styles/sidomeny.css">


</head>
<body class="d-flex flex-column min-vh-100">
    
<ul class="nav nav-underline bg-body-tertiary border-bottom justify-content-center">
        <div id="main">
          <span class="hamburg" onclick="openNav()">&#9776;</span>
        </div>
  
        <div id="mySidenav" class="sidenav">
          <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
          <a href="#">About</a>
          <a href="#">Services</a>
          <a href="#">Clients</a>
          <a href="#">Contact</a>
        </div>
      <li class="nav-item">
        <a class="nav-link" style="color:black; font-size: 20px;" aria-current="page" href="index.php">Hem</a>
      </li>
      <li class="nav-item">
        <a class="nav-link active" style="color: black; font-size: 20px;" href="shop.php">Webbshop</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" style="color: black; font-size: 20px;" href="ban_sida.php">Karta</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" style="color:black; font-size: 20px;"  href="bokning_sida.php">Bokning</a>
      </li>     
      <li class="nav-item">
        <a class="nav-link" style="color:black; font-size: 20px;"  href="login.php">Logga in</a>
      </li> 
</ul>
      <div class="container">

            <div id="main">
                <span class="hamburg" onclick="openNav()">&#9776;</span>
              </div>

              <div id="mySidenav" class="sidenav">
                <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
                <a href="#">About</a>
                <a href="#">Services</a>
                <a href="#">Clients</a>
                <a href="#">Contact</a>
              </div>
    
      </div>

      <div class="p-5 shop_img w-100 position-absolute"></div>
      <div class="w-75 p-3 border border-black rounded m-auto mb-5 bg-white filter_box" >

        <h4 class="text-center text-decoration-underline m-auto">filtrera</h4>

        <div class="row flex-column gap-3" id="filters_always_shown">
            <form>
                <input type="text" name="search" id="search" class="w-25" placeholder="Sök">
                <button type="submit" class="btn btn-success">Sök</button>
            </form>

            <div>
                <form>
                    <span class="multi-range w-25">
                        <span id="range_lowest">lägsta pris: 0kr</span>
                        <input type="range" min="0" max="50" value="0" id="lower" onchange="test()">

                    </span>
                </form>
            </div>
        </div>
       
        <hr>

        <div id="filters_hideable">
            <span>[hideable filters here]</span>
        </div>

        <div class="w-25 m-auto row justify-content-center">
            <div class="col text-center">
            <input type="checkbox" checked class="col" id="filter_button" onclick="filtersHideShow()"></input>
            <label for="filter_button" id="filter_label" class="col btn btn-secondary w-100">Göm filter &uarr;</label>
            </div>
          </div>
      </div>

      <h1 class="text-center text-decoration-underline">Shop</h1>

      <div class=" w-100 row justify-content-center gap-5 item-list-padding">
      <button type="button" class="card p-0 col-3 ItemCard" style="width: 18rem;" data-bs-toggle="modal" data-bs-target="#item_1_modal">
        <img src="../images/example.jpg" class="card-img-top" alt="...">
        <div class="card-body m-auto">
          <h5 class="card-title">Kastmyrens signatur frisbee</h5>
          <p class="card-text">Nå toppen!</p>
          <div class="btn btn-primary w-100">visa</div>
        </div>
      </button>

      

    </div>

  
  <!-- Modal -->
  <div class="modal fade " id="item_1_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Vara #1</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body row m-0 p-0">
            <div class="w-50">
             <img src="../images/example.jpg" alt="item" class="col" style="max-width: 100%; height: auto; transform: scale(0.7);"/>
            </div>

         <div class="col border-start">
            <h2 class="text-decoration-underline text-center">kastmyrens signatur frisbee</h2>
            <p class="fs-4 fw-semibold">           
                 Det här är en frisbee som kommer att ta dig till toppen av diskgolf spelande. Denna har använts av alla mästare inom diskgolfen, och nu är det <strong>DIN</strong> tur att nå den toppen!
            </p>
            <hr>
            <div class="col overflow-y-scroll overflow-x-hidden reviews">
                <h3 class="text-decoration-underline text-center">Recensioner</h3>

                <form class="w-100 row justify-content-center">
                    <input type="text" name="review" id="review" class="rounded w-50 me-2" placeholder="Skriv en recension">
                    <button type="submit" class="btn btn-primary w-25">Skicka!</button>
                </form>

                <hr>

                <div class="card mb-3 shadow-sm">
                    <div class="card-header">
                      Användare
                    </div>
                    <div class="card-body">
                      <h5 class="card-title">Riktigt bra</h5>
                      <p class="card-text">Gjorde det som den ska</p>
                    </div>
                  </div>

                  <div class="card mb-3 shadow-sm">
                    <div class="card-header">
                      Användare 2
                    </div>
                    <div class="card-body">
                      <h5 class="card-title">Riktigt bra</h5>
                      <p class="card-text">Gjorde det som den ska</p>
                    </div>
                  </div>

                  <div class="card mb-3 shadow-sm">
                    <div class="card-header">
                      Användare 3
                    </div>
                    <div class="card-body">
                      <h5 class="card-title">Riktigt bra</h5>
                      <p class="card-text">Gjorde det som den ska</p>
                    </div>
                  </div>
            </div>
         </div>
         
        </div>
        <div class="modal-footer">
          <h3 class="me-4">
           <strong>
            349 SEK
           </strong>
           </h3>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Stäng</button>
          <button type="button" class="btn btn-primary">Lägg till i kundvagn</button>
        </div>
      </div>
    </div>
  </div>
  
  <script src="../scripts/shop.js"></script>
  <script src="../scripts/sidomeny.js"></script>
</body>
    
<footer class="footer mt-auto">
        <div class="containerfooter">
            <div class="footer-section">
                <h4>Contact Us</h4>
                <p>Email: Kastmyrens@diskare.com</p>
                <p>Telefon: +123-456-7890</p>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; 2024 Kastmyrens Diskare. All rights reserved.
        </div>
    </footer>

</html>

