function openNav() {
    document.getElementById("mySidenav").style.width = "20%";
    if (window.innerWidth <= 768){
      document.getElementById("mySidenav").style.width = "50%";
    }
    closeCart();
  }
  
  function closeNav() {
    document.getElementById("mySidenav").style.width = "0";
    document.body.style.backgroundColor = "white";
  }


window.addEventListener('resize', function() {
    if (window.innerWidth > 768) {
        closeNav();
    }
});

function openCart() {
  document.getElementById("myCart").style.width = "20%";
  if (window.innerWidth <= 768){
    document.getElementById("myCart").style.width = "50%";
  }
  closeNav();
}

function closeCart() {
  document.getElementById("myCart").style.width = "0";
  document.body.style.backgroundColor = "white";
}


window.addEventListener('resize', function() {
  if (window.innerWidth > 768) {
      closeCart();
  }
});