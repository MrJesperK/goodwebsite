const monthYearEle = document.getElementById('monthYear');
const datesEle = document.getElementById('dates');
const prevBtn = document.getElementById('prevBtn');
const nextBtn = document.getElementById('nextBtn');
const clickableDate = document.getElementById('day'); 
const checkclick = document.getElementById('D');
const showbuttonclick = document.getElementById('button');

let currentDate = new Date();

let dateclick ='';
const updateCalendar = () => {
    const currentYear = currentDate.getFullYear();
    const currentMonth = currentDate.getMonth();
    const firstDay = new Date(currentYear, currentMonth, 0);
    const lastDay = new Date(currentYear, currentMonth + 1, 0);
    const totalDays = lastDay.getDate();
    const firstDayIndex = firstDay.getDay();
    const lastDayIndex = lastDay.getDay();

    const monthYearString = currentDate.toLocaleString('default', {month:'long', year: 'numeric'});
    monthYearEle.textContent = monthYearString
   
    let datesHTML = '';

    for(let i = firstDayIndex; i > 0; i--){
        const prevDate = new Date(currentYear, currentMonth, 0 - i + 1);
        datesHTML +=`<div class="date inactive">${prevDate.getDate()}</div>`
    }

    for(let i = 1; i <= totalDays; i++){
        const date = new Date(currentYear, currentMonth, i);
        const activeClass = date.toDateString() === new Date().toDateString() ? 'active': '';
        datesHTML += `<div class="date${activeClass}" index="${i}">${i}</div>`;
    }

    for (let i = 1; i <= 7 - lastDayIndex; i++){
        const nextDate = new Date(currentYear, currentMonth + 1, i);
        datesHTML += `<div class="date inactive">${nextDate.getDate()}</div>`;
    }
    datesEle.innerHTML = datesHTML;
}   



prevBtn.addEventListener('click', () => {
    currentDate.setMonth(currentDate.getMonth() - 1);
    updateCalendar();
})

nextBtn.addEventListener('click', () => {
    currentDate.setMonth(currentDate.getMonth() + 1);
    updateCalendar();
})

updateCalendar();

document.querySelectorAll('.date').forEach(function(day){
    day.addEventListener('click', function(){
        console.log(this.getAttribute('index'));
    })
})

let t = "";
const timeSelect = ["16:30", "14:00", "18:45", "12:25", "10:40", "16:30", "14:00", "18:45", "12:25", "10:40" ];
for(let x = 0; x != timeSelect.length; x++){
t += `<div><input type="radio" name="checkME" onclick="check(${x})" id='checkME${x}' <lable id="${x}">${timeSelect[x]}<br></div>`;

}
checkclick.innerHTML = t;

function check(a){
   if(document.getElementById('checkME'+a).checked === true){
    document.getElementById('btn').style.display = "block";
   }
}

   
   



document.querySelectorAll('.checkbox').forEach(function(time){
    time.addEventListener('click', function(){
        
    })
})
