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