function openNav() {
    document.getElementById("mySidenav").style.width = "20%";
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