
Ye projects koi issue na aaye isme page load na ho isme background main sb chale aesa krna hai ek ek steps ko

Plyvista ke name se laravel 11 main web routes ke sath main application web banani hai jisme salesperson ke leads, expenses, profile, kitni leads confirm huyi kitni nhi, quotation sb chij ki, or jitni bhi ek project ke liye salesperson ki chije hoti hai wo banani hai mujhe aap complete de kyunki mujhe real time update chahiye Jahan bhi ho jese Admin ne kuch change Kiya to sb jagah usi waqt ho jana chahiye, or sbhi ke graphs bhi chahiye taaki unhe dekh kr easily pta lg jaaye ki salesperson ne kya kya Kiya hai kb kb Kiya hai mujhe uski attandance ke sath live location bhi chahiye with calender jo ki showdetails-calender.blade.php ifle main hai jisme present, absent, leave, events all over sb kuch hoga date ke sath or date ka jo box hai sbhi ka alag alag color hoga

iska mujhe aap sbhi ko migration, model or api routes ka uske sath jo bhi blade file banegi sbka pura or proper code de.
mene sbki migrations bana rakhi hai jo na bni ho wo aap bana skte ho

jb regisration hoga to uska name email to aa hi jayega with role to hme uski baaki ki information bhi save krwani hai jese ki niche di gyi hai
    -
        'phone',
        'photo',
        'whatspp number',
        'pincode',
        'address',
        'location',
        'designation',
        'date_of_joining',
        'status'

======= SALESPERSON DASHBOARD =====

ye saari informations bhi store krwani hai jb wo registration ho jayega to usme locaiion, attendance status, leave_type, holiday_reason, total_sales, target_sales, conversion_rate, average_deal_size, performance_rating ye itni fileds hai jo wo baad main update hogi kyunki attandance ki to alag table bana rakhi hai migrations aap dekh skte hai, sales activity ki alag se banegi jisme salesperosn ke target_sales or total_sales ka bhi rahega, or salesperson ke login hote hi uski live location update ho jayegi usi din ki attandance kahan se ki hai,


or salesperson dashboard ka mene kanban ka design bana rakha hai usi ke hisab se krna hai uski js bhi dekh lena or design ko bhi thoda theek kr de
leads ke hisab se hi hoga wo sb kuch or unke jo bhi status honge wo upar dikhege wo dynmic honge leads status bhi or card ko swap kre ya rander kre to multiple time kr ske iska bhi dhayan rakhna or na ho rha hai to theek kr de or card main leads with details ke sath achhe se dikhaye bottom main sbhi ke niche message, call, jese icons bhi lagaye taaki jrurat pdne pr use kr ske achha attractive dikhe

Isme mujhe pusher ka use nhi Krna kyunki wo paid hai mujhe bilkul free application banani hai or kuch bhi change ho kre uska notification bhi aaye or isme Mujhe aesa Krna hai ki ye smoothly work kre
Isme Jo salesperson or Admin ka number save hoga wo ek WhatsApp number hoga wo automatically number ko dekhe or salesperson ko message daal de subh dhopher saam ko ki uske kitne target hai kitne baaki hai or kitne pure ho gye hai kitne lost ho gye hai sb kuch leads ke target
or kya kaam chal rha hai today main wo bhi
new leads ka message ka notificatons bhi jaaye jis salesperson ne kiya hai or admin ke number pr bhi jaaye



sabse phle salesperson ke attandance ko complete krna hai main focus yhi hai web ke liye website ko view pr bhi dikhayega bilkul real time data ajax se kr skte hai
iska dhyan rakhe jo aapko dashboard dikha rakha hai view / dashboard / salesperson / salesperson-dashboard.blade.php
and editpipline.blade.php se aesa krna hai or behatar achha krna hai proper crm system banana hai mujhe 

1. jb tk attandance na lagaye mtlb salesperson present na ho uss din to wo new lead or new expenses add na kr ske
2. per person ka duty time rahega 9.30AM se 7.30PM uske baaad hamesha late mark hoga jb 9.30AM ke baad aayega to
3.  check-in krne ke baad uski timeline ban kr dikh jaaye admin dashboard main salesperson ki jese google ki timeline dikhti hai per month ki datewise kb kahan gye the vese hi alag se page pr
4. late aane pr whatsapp number pr jo number save ho uspe autometically message chala jaaye aese krna hai isme ki aaj aap late ho
5. check-out krega tb tk uss din ki map ki timeline admin ke pass rahegi uske baad ki nhi
    jb check-in krega fir se start ho jayegi wo aese krna hai real time map tracking chahiye
6. Whatsapp sms jaaye attandance with status, lead task, sales task 
7. sales force main target diya tha salesperson ke siniors ne mothly, weekly, quarterly, Yealy wo bhi dikhe
8. jese hi month ki 1 tarik aaye plan lena hai ki iss month kya krege wo bhi salesperson daal ske apna plan
    - lead plans, sales plan ka target denge -> option dene honge target diya tha mothly, weekly, quarterly, Yealy
9. Meeting ka option -> reminder date pr
    - reminder date nikal jaaye to uska status pending main rahega ne delete kr skta hai


chalo ab frontend UI design krte hai mere pass kuch hai jese hi salesperson ke dashboard ke liye mene usme ek blade design de rakha hai usse dynmic krna hai kyunki meri web pr work krni chahiye usme sb kuch kre or mere pass uske dashboard ke liye ek image hai aap wo dekh kr design lga skte hai kuch vesa admin ke liye bhi add kr skte hai

sweetalert ka use kr skte hai salesperson ka layouts bhi create kr de taaki duplicate na ho code shi rhe

UI/UX design achha hona chahiye jisse dekh kr achha lge professional lge kanban, calendar with crm or calendar main date wise dikhe alag alag use bhi kr skte ho alag alag purpose ke liye

✅ Live Location Tracking (Salesperson ke login hote hi)
✅ Leads & Sales Tracking (Kitni leads confirm, reject, lost, etc.)
✅ Real-Time Updates (Admin ya Salesperson ka data turant update ho)
✅ Notification System (WhatsApp API ya Free SMS Gateway ke sath)
✅ Performance & Sales Analytics (Graphs aur Charts ke sath)
✅ Attendance & Calendar Integration

👨‍💻 अगले कदम:
✅ Attendance Dashboard बनाएं (जहां ग्राफ, स्टेट्स और महीने की रिपोर्ट दिखे)।
✅ Push Notifications जोड़ें (लेट आने पर सेल्सपर्सन को नोटिफिकेशन मिले)।
✅ GPS Location Accuracy सुधारें (फ्रंटेंड पर Google Maps API के साथ)। Sirf admin dashboard main per salesperson ka alag rahega

    google map api key :- AIzaSyBl5N0v6zO372f3-RU-mSKNAMyN1Cu0Rzk

    whatspp number :- 8607807612






    ======= important =======

    1. Attandance table main leave resion application bhi deni hai


<!-- google map script real data update show -->

     <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBl5N0v6zO372f3-RU-mSKNAMyN1Cu0Rzk&callback=initMap" async defer></script>

        <script>
            function initMap() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        var latLng = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude
                        };

                        var map = new google.maps.Map(document.getElementById('map'), {
                            center: latLng,
                            zoom: 15
                        });

                        new google.maps.Marker({
                            position: latLng,
                            map: map
                        });

                        document.getElementById('currentLocation').innerHTML = 
                            `Latitude: ${position.coords.latitude} <br> Longitude: ${position.coords.longitude}`;
                    });
                } else {
                    document.getElementById('currentLocation').textContent = 'Geolocation not supported';
                }
            }
        </script>


        <!-- Attendance Section -->
        <div class="attendance-container p-4 rounded shadow-sm">
            <h3 class="h5 text-center mb-4 text-dark fw-bold pb-4" style="border-bottom: 2px solid black;">Attendance</h3>

            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="info-card location-card shadow-sm text-center p-3">
                        <p class="small fw-semibold text-dark">Current Location</p>
                        <div id="map" style="height: 300px;"></div>
                    </div>
                </div>
            </div>
        </div>

<!-- google map script real data update close -->

     <!-- Calendar Navigation -->
        <div class="bg-white rounded shadow mt-4 text-center">
            <!-- Calendar Header -->
            <div class="p-4 border-bottom d-flex flex-column align-items-center">
                <h3 id="calendarTitle" class="h5 d-flex align-items-center fw-bold">
                    <i class="fas fa-calendar-alt text-primary me-2"></i> <!-- Calendar Icon -->
                    <span>March 2025</span> <!-- Month & Year Display -->
                </h3>
                <div class="btn-group mt-3" role="group">
                    <button id="prevMonth" class="btn btn-outline-dark custom-btn">
                        <i class="fas fa-chevron-left"></i> Prev
                    </button>
                    <button id="monthView" class="btn btn-outline-dark custom-btn">
                        <i class="fas fa-th-large"></i> Month
                    </button>
                    <button id="weekView" class="btn btn-outline-dark custom-btn">
                        <i class="fas fa-calendar-week"></i> Week
                    </button>
                    <button id="dayView" class="btn btn-outline-dark custom-btn">
                        <i class="fas fa-calendar-day"></i> Day
                    </button>
                    <button id="nextMonth" class="btn btn-outline-dark custom-btn">
                        Next <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
            <!-- Calendar Body -->
            <div class="month-calendar">
                <div class="month-grid" id="calendarGrid">
                    <!-- Days will be dynamically added here -->
                </div>
            </div>
        </div>