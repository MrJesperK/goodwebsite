const myModal = document.getElementById('myModal')
const myInput = document.getElementById('myInput')

myModal.addEventListener('shown.bs.modal', () => {
  myInput.focus()
})




function filtersHideShow(){
    let filters = document.getElementById('filters_hideable');
let filter_button = document.getElementById('filter_button');
let label = document.getElementById('filter_label');
    if (filter_button.checked){
        label.innerHTML = "Göm filter &uarr;";
        console.log("open")
        filters.style.display = "block";
    } else if (!filter_button.checked){
        label.innerHTML = "Visa fler filter &darr;";
        console.log("closed");
        filters.style.display = "none";
        label.style.display = "block";
    }
}



function test(){
    let range_label = document.getElementById("range_lowest");
let range_input = document.getElementById("lower");
    range_label.innerText = "Lägsta pris: " + range_input.value + "kr";
}

function searching(event) {
    event.preventDefault();
    console.log("NOT PENIS");
    var data = new FormData(document.getElementById("searchForm"));
    console.log("Form data:", data);
  
    var xhr = new XMLHttpRequest();
    xhr.onload = function () {
      console.log("xhr.onload function called");
      if (xhr.status === 200) {
        console.log(xhr.responseText);
        document.body.innerHTML = xhr.responseText;
      } else {
        console.error("Request failed. Status: " + xhr.status);
      }
    };
  
    var url = window.location.href;
    xhr.open("POST", url);
    console.log("Sending request to:", url);
    xhr.send(data);
  
    return false;
  }

  function clearFormInputs(form) {
    form.reset();
  }
  
  function disableSubmitButton(form) {
    const submitButton = form.querySelector('button[type="submit"]');
    submitButton.disabled = true;
  }
  
  function enableSubmitButton(form) {
    const submitButton = form.querySelector('button[type="submit"]');
    submitButton.disabled = false;
  }

  function cartStuff(event, item){
    event.preventDefault();
    let cartForm = document.getElementById('cartForm_'+item);
    let itemToAdd = document.getElementById('item_'+item);
    let cartList = document.getElementById('cart');

    const request = {
      id: item,
    };

    const xhr = new XMLHttpRequest();
    const url = "../db/addToCart.php";

    xhr.open("POST", url);
    xhr.response = 'text';

    xhr.onreadystatechange = function() {
      if (xhr.readyState === XMLHttpRequest.DONE){
        if (xhr.status === 200){
          console.log(xhr.response);
          cartList.insertAdjacentHTML("afterbegin", xhr.response);
        } else {
          console.log(xhr.status, xhr.statusText, xhr.responseText);
        }

        clearFormInputs(cartForm);
        enableSubmitButton(cartForm);
      }
    };

    try {
      xhr.send(JSON.stringify(request));
    } catch(e) {
      console.log(e);
    }

    return false;
  }

  function review(event, item_id){
    event.preventDefault();
    let reviewForm = document.getElementById('revForm_'+item_id);
    let revList = document.getElementById('reviews');
    let revText = document.getElementById('revText').value;

    const req = {
      id: item_id,
      text: revText
    };

    const xhr = new XMLHttpRequest();
    const url = "../db/reviews.php";

    xhr.open("POST", url);
    xhr.response = 'text';

    xhr.onreadystatechange = function() {
      if (xhr.readyState === XMLHttpRequest.DONE){
        if (xhr.status === 200){
          console.log(xhr.response);
          cartList.insertAdjacentHTML("afterbegin", xhr.response);
        } else {
          console.log(xhr.status, xhr.statusText, xhr.responseText);
        }

        clearFormInputs(reviewForm);
        enableSubmitButton(reviewForm);
      }
    };

    try {
      xhr.send(JSON.stringify(req));
    } catch(e) {
      console.log(e);
    }

    return false;
  }