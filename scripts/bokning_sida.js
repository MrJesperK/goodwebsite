document.addEventListener('DOMContentLoaded', () => {
    const monthYearEle = document.getElementById('monthYear');
    const datesEle = document.getElementById('dates');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const checkclick = document.getElementById('D');
    const bookBtn = document.getElementById('btn');

    let currentDate = new Date();
    let booktime = { date: "", time: "" };

    // Convert bookedDates to Date objects for comparison
    const bookedDatesSet = new Set(bookedDates.map(date => new Date(date).toDateString()));

    const updateCalendar = () => {
        const currentYear = currentDate.getFullYear();
        const currentMonth = currentDate.getMonth();
        const firstDay = new Date(currentYear, currentMonth, 1);
        const lastDay = new Date(currentYear, currentMonth + 1, 0);
        const totalDays = lastDay.getDate();
        const firstDayIndex = firstDay.getDay();
        const lastDayIndex = lastDay.getDay();
        const monthYearString = currentDate.toLocaleString('en-US', { month: 'long', year: 'numeric' });
        const today = new Date();

        monthYearEle.textContent = monthYearString;

        let datesHTML = '';

        // Render previous month's inactive days
        for (let i = firstDayIndex; i > 0; i--) {
            const prevDate = new Date(currentYear, currentMonth, 0 - i + 1);
            datesHTML += `<div class="date inactive">${prevDate.getDate()}</div>`;
        }

        // Render current month's days
        for (let i = 1; i <= totalDays; i++) {
            const date = new Date(currentYear, currentMonth, i);
            const isPast = date < today;
            const activeClass = date.toDateString() === today.toDateString() ? 'active' : '';
            const disabledClass = isPast ? 'past' ? 'disabled':'' :''; // Apply 'past' class to past dates
            const bookedClass = bookedDatesSet.has(date.toDateString()) ? 'booked' : ''; // Check if date is booked
            const availableClass = !bookedDatesSet.has(date.toDateString()) ? 'available' : ''; // Check if date is available
            datesHTML += `<div class="date ${activeClass} ${disabledClass} ${bookedClass} ${availableClass}" data-index="${i}">${i}</div>`;
        }

        // Render next month's inactive days
        for (let i = 1; i <= 7 - lastDayIndex; i++) {
            const nextDate = new Date(currentYear, currentMonth + 1, i);
            datesHTML += `<div class="date inactive">${nextDate.getDate()}</div>`;
        }

        datesEle.innerHTML = datesHTML;

        // Add event listener to each date
        document.querySelectorAll('.date').forEach(dateElement => {
            dateElement.addEventListener('click', function(e) {
                if (!e.target.classList.contains('inactive') && !e.target.classList.contains('disabled') && !e.target.classList.contains('booked')) {
                    // Remove 'selected' class from the previously selected date
                    document.querySelectorAll('.date.selected').forEach(d => d.classList.remove('selected'));
        
                    // Add 'selected' class to the clicked date
                    e.target.classList.add('selected');
                    
                    const dayIndex = e.target.getAttribute('data-index');
                    booktime.date = `${dayIndex} ${monthYearString}`;
                    console.log('Selected date:', booktime.date);
        
                    // Show the book button if a time has already been selected
                    updateButtonVisibility();
                }
            });
        });
        
    };

    const updateButtonVisibility = () => {
        if (booktime.time && booktime.date) {
            bookBtn.style.display = "block";
        } else {
            bookBtn.style.display = "none";
        }
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
            booktime.time = timeSelect[index];
            console.log('Selected time:', booktime.time);

            // Show the button if a date has been selected
            updateButtonVisibility();
        }
    }

    window.BtnClick = () => {
        const errorElement = document.getElementById('error-message');
        errorElement.textContent = ''; // Clear any existing error messages

        if (!booktime.date) {
            errorElement.textContent = 'Please select a date before booking.';
            return false; // Prevent form submission
        }
        if (!booktime.time) {
            errorElement.textContent = 'Please select a time before booking.';
            return false; // Prevent form submission
        }

        const [day, monthYear] = booktime.date.split(' ');
        const [month, year] = monthYear.split(' ');
        const selectedDate = new Date(`${month} ${day}, ${year}`);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        selectedDate.setHours(0, 0, 0, 0);

        if (selectedDate < today) {
            errorElement.textContent = 'You cannot book in the past. Please choose a future date.';
            return false; // Prevent form submission
        }

        document.getElementById("bookingdate").value = booktime.date;
        document.getElementById("bookingtime").value = booktime.time;
        console.log('Booking confirmed:', booktime);
        return true; // Allow form submission
    };
});
