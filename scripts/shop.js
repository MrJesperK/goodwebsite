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

