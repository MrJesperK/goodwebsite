document.addEventListener('DOMContentLoaded', () => {
    const monthYearEle = document.getElementById('monthYear');
    const datesEle = document.getElementById('dates');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const checkclick = document.getElementById('D');
    const bookBtn = document.getElementById('btn');

    let currentDate = new Date();
    let booktime = { date: "", time: "" };

    const updateCalendar = () => {
        const currentYear = currentDate.getFullYear();
        const currentMonth = currentDate.getMonth();
        const firstDay = new Date(currentYear, currentMonth, 1);
        const lastDay = new Date(currentYear, currentMonth + 1, 0);
        const totalDays = lastDay.getDate();
        const firstDayIndex = firstDay.getDay();
        const lastDayIndex = lastDay.getDay();
        const monthYearString = currentDate.toLocaleString('default', { month: 'long', year: 'numeric' });
        const today = new Date(); // Define today here

        monthYearEle.textContent = monthYearString;

        let datesHTML = '';

        // Render previous month's inactive days
        for (let i = firstDayIndex; i > 0; i--) {
            const prevDate = new Date(currentYear, currentMonth, 0 - i + 1);
            datesHTML += `<div class="date inactive">${prevDate.getDate()}</div>`;
        }

        for (let i = 1; i <= totalDays; i++) {
            const date = new Date(currentYear, currentMonth, i);
            const isPast = date < today; // Check if date is in the past
            const activeClass = date.toDateString() === today.toDateString() ? 'active' : '';
            const disabledClass = isPast ? 'disabled' : ''; // Disable past dates
            datesHTML += `<div class="date${activeClass} ${disabledClass}" data-index="${i}">${i}</div>`;
        }

        // Render next month's inactive days
        for (let i = 1; i <= 7 - lastDayIndex; i++) {
            const nextDate = new Date(currentYear, currentMonth + 1, i);
            datesHTML += `<div class="date inactive">${nextDate.getDate()}</div>`;
        }

        datesEle.innerHTML = datesHTML;

        // Event delegation to handle clicks on the dynamically created dates
        datesEle.addEventListener('click', function(e) {
            if (e.target.classList.contains('date') && !e.target.classList.contains('inactive') && !e.target.classList.contains('disabled')) {
                document.querySelectorAll('.date').forEach(d => d.classList.remove('selected'));
                e.target.classList.add('selected');
                const dayIndex = e.target.getAttribute('data-index');
                booktime.date = `${dayIndex} ${monthYearString}`;
                console.log('Selected date:', booktime.date);

                // Hide book button and reset time when date changes
                bookBtn.style.display = "none";
                booktime.time = "";
            }
        });
    };

    prevBtn.addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() - 1);
        updateCalendar();
    });

    nextBtn.addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        updateCalendar();
    });

    updateCalendar();

    // Time selection setup
    const timeSelect = ["16:30", "14:00", "18:45", "12:25", "10:40"];
    const renderTimeOptions = () => {
        let timeHTML = '';
        timeSelect.forEach((time, index) => {
            timeHTML += `
                <div>
                    <input type="radio" name="checkME" id="checkME${index}" onclick="check(${index})">
                    <label for="checkME${index}">${time}</label>
                </div>`;
        });
        checkclick.innerHTML = timeHTML;
    };

    renderTimeOptions();

    window.check = (index) => {
        if (document.getElementById('checkME' + index).checked) {
            bookBtn.style.display = "block";
            booktime.time = timeSelect[index];
            console.log('Selected time:', booktime.time);
        }
    }

    window.BtnClick = () => {
        const selectedDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), parseInt(booktime.date.split(" ")[0]));
        const today = new Date();

        if (selectedDate < today) {
            alert('You cannot book in the past. Please choose a future date.');
            return false; // Prevent form submission
        }

        document.getElementById("bookingdate").value = booktime.date;
        document.getElementById("bookingtime").value = booktime.time;
        console.log('Booking confirmed:', booktime);
        return true;
    };
});