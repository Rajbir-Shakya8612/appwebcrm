
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
se aesa krna hai or behatar achha krna hai proper crm system banana hai mujhe 

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



========== new task ======
isme mene phle se kuch method migrations model or logics controller main bana rakhe hai with routes phle check kr le unhe jisme kmi ho unhe theek kre blade file main ho to usse baaki jahan bhi ho wahan kmi theek kr de 


1. locations trackking table main data kese jayega phle to ye shi kre kyunki attandance lgne ke sath jaani chahiye post usi ki to locations tracks krege admin
iske commands console bhi bana rakha hai check kr le shi se work kre ye bhi

2. check in or check out ki request kaafi der main jaati hai wo real time work krni chahiye ki user ne click kiya or attrandace lg jaye sath ke sath hi check out kre to wo bhi sath ke sath hi ho jaaye
sweet alert main to check in or check out krne pr error dikhata hai jo ki niche mene diya hai lekin request chali bhi jaati hai success bhi ho jaati hai data bhi database main proper save ho jata hia isse bhi check kre aesa kyu hota hai

Error!
Failed to check out

3. sath main new lead add or edit ka bhi dekh lena salesperson ki side main ki dashboard se new lead add kr ske edit bhi or sath main wo calender main bhi dikhe jb click kre to popup main wo leads ki details dikhe aese krna hai usme
4, salesperson ko uski total leads ka or attandance ka graph dikhe dashboard pr dono 
5. task bhi add kr ske aap uski migrations model controller main method check kr le with routes or dashboard se task ko whi informations ko add krwaye jo model migrations main hai
6. Whatsapp sms jaaye attandance with status, lead task, sales task 
7. sales force main target diya tha salesperson ke siniors ne mothly, weekly, quarterly, Yealy wo bhi dikhe
8. jese hi month ki 1 tarik aaye plan lena hai ki iss month kya krege wo bhi salesperson daal ske apna plan
    - lead plans, sales plan ka target denge -> option dene honge target diya tha mothly, weekly, quarterly, Yealy
9. Meeting ka option -> reminder date pr
    - reminder date nikal jaaye to uska status pending main rahega ne delete kr skta hai


plan aese dekha salesperson

id	1
user_id	1
start_date	2025-03-28 23:01:56
end_date	2025-04-27 23:01:56
month	3
year	2025
type	monthly
lead_target	100
sales_target	50000.50
description	March Sales Plan
status	active
achievements	"{\"leads\":20,\"sales\":10000}"
notes	Initial plan for March
created_at	2025-03-28 23:01:56
updated_at	2025-03-28 23:01:56

aese data save hoga wo


card move js code old

            // Initialize Dragula
            const containers = document.querySelectorAll('[id^="status-"]');
            dragula(containers, {
                moves: function(el) {
                    return el.classList.contains('cursor-move');
                },
                accepts: function(el, target) {
                    return target.id !== el.parentNode.id;
                },
                direction: 'horizontal',
                revertOnSpill: true
            }).on('drop', function(el, target) {
                const leadId = el.dataset.leadId;
                const newStatusId = target.id.replace('status-', '');

                // Update lead status via AJAX
                $.ajax({
                    url: `/leads/${leadId}/status`,
                    method: 'PUT',
                    data: {
                        status_id: newStatusId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Lead status updated successfully',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Failed to update lead status'
                        });
                    }
                });
            });

card move js code old close


    ========= admin ke liye important =========

1. salesperson side se locations tracking ko hta kr uske routes ke sath admin side krne hai testing ke baad
2. salesperson side jo bhi testing ke liye rakh rakha hai admin ka baad main hide krna hai
3. salesperson dashboard ka code dashboard.js main hai shi se wo usme krna hai salesperson-dashboard pe jo js hai wo shi krni hai












current task

mere kehne ka mtlb ye hai ki jese hi lead update or store hoti hai to kanban ke card main bhi real time update ho jaaye wo kese hoga usse krna hai

    <!-- Kanban Board -->
        <div class="bg-white rounded shadow mt-4">
            <div class="p-4 border-bottom">
                <h3 class="h6">Lead Pipeline</h3>

                <div class="d-flex">
                    <button onclick="openLeadModal()" class="btn btn-primary me-2">
                        + Add New Lead
                    </button>
                    <button onclick="openModal()" class="btn btn-success">
                        + Add New Task
                    </button>
                </div>
            </div>
            <div class="p-4">
                <div class="d-flex overflow-auto">
                    @foreach ($leadStatuses as $status)
                        <div class="flex-shrink-0 me-3" style="width: 300px;">
                            <div class="bg-light rounded p-3">
                                <h4 class="small text-muted mb-3">{{ $status->name }}</h4>
                                <div class="mb-3" id="status-{{ $status->id }}">
                                    @foreach ($status->leads as $lead)
                                        <div class="bg-white rounded shadow p-3 mb-2 cursor-move"
                                            data-lead-id="{{ $lead->id }}">
                                            <div class="d-flex justify-content-between mb-2">
                                                <h5 class="font-weight-bold text-dark">{{ $lead->name }}</h5>
                                                <span
                                                    class="small text-muted">{{ $lead->created_at->format('M d, Y') }}</span>
                                            </div>
                                            <p class="small text-muted mb-3">{{ Str::limit($lead->description, 100) }}</p>
                                            <div class="d-flex justify-content-between">
                                                <div class="d-flex align-items-center">
                                                    <a href="tel:{{ $lead->phone }}" class="text-primary me-2">
                                                        <i class="fas fa-phone"></i>
                                                    </a>
                                                    <a href="mailto:{{ $lead->email }}" class="text-success me-2">
                                                        <i class="fas fa-envelope"></i>
                                                    </a>
                                                    <a href="https://wa.me/{{ $lead->phone }}" target="_blank"
                                                        class="text-success">
                                                        <i class="fab fa-whatsapp"></i>
                                                    </a>
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <button onclick="openLeadModal({{ $lead->id }})"
                                                        class="text-primary me-2">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button onclick="deleteLead({{ $lead->id }})"
                                                        class="text-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>


isme isse kre or cart ke top pr jahan pr status dikh rha hai  <h4 class="small text-muted mb-3">{{ $status->name }}</h4>  iske bilkul samne right side main uss card main kitni lead hai total wo bhi dikhe aese krna hai


            // lead open management
            function openLeadModal(leadId = null) {
                const modal = document.getElementById('leadModal');
                const form = document.getElementById('addLeadForm');
                const modalTitle = document.getElementById('leadModalLabel');
                const leadIdField = document.getElementById('lead_id');

                if (leadId) {
                    // Edit mode
                    modalTitle.textContent = 'Edit Lead';
                    fetch(`/salesperson/leads/${leadId}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok: ' + response.statusText);
                            }
                            return response.json();
                        })
                        .then(lead => {
                            form.name.value = lead.name;
                            form.phone.value = lead.phone;
                            form.email.value = lead.email;
                            form.company.value = lead.company;
                            form.description.value = lead.additional_info;
                            form.source.value = lead.source;
                            form.expected_amount.value = lead.expected_amount;
                            form.notes.value = lead.notes;
                            form.status.value = lead.status_id; // Status update

                            form.dataset.leadId = leadId;
                            leadIdField.value = leadId;
                        })
                        .catch(error => {
                            console.error('Error fetching lead:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Failed to load lead data. Please try again.'
                            });
                        });
                } else {
                    // Create mode
                    modalTitle.textContent = 'Add New Lead';
                    form.reset(); // Reset the form for new lead
                    delete form.dataset.leadId; // Ensure the lead ID is removed
                }

                new bootstrap.Modal(modal).show();
            }



            function submitLeadForm(event) {
                event.preventDefault();
                const form = event.target;
                const leadId = form.dataset.leadId;
                const url = leadId ? `/salesperson/leads/${leadId}` : '/salesperson/leads';
                const method = leadId ? 'PUT' : 'POST';

                const formData = new FormData(form);
                const data = Object.fromEntries(formData.entries());

                // Add CSRF token
                data._token = '{{ csrf_token() }}';

                fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: result.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            location.reload();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: result.message || 'Failed to save lead'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Failed to save lead. Please try again.'
                        });
                    });

            }

            function deleteLead(leadId) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        axios.delete(`/salesperson/leads/${leadId}`, {
                            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                        }).then(response => {
                            if (response.data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: response.data.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                document.getElementById(`lead-row-${leadId}`).remove(); // Table se row hatao
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: response.data.message || 'Failed to delete lead'
                                });
                            }
                        }).catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Failed to delete lead. Please try again.'
                            });
                        });
                    }
                });
            }



                // Lead Form Submission
            document.getElementById('addLeadForm').addEventListener('submit', function(event) {
                event.preventDefault();

                let form = this;
                let leadId = document.getElementById('lead_id').value;
                let url = leadId ? `/salesperson/leads/${leadId}` : '/salesperson/leads';
                let method = leadId ? 'put' : 'post';

                let formData = new FormData(form);
                let data = Object.fromEntries(formData.entries());

                axios({
                    method: method,
                    url: url,
                    data: data,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                }).then(response => {
                    if (response.data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.data.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        $('#leadModal').modal('hide'); // Modal close karo
                        updateLeadTable(response.data.lead); // Table update karo
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.data.message || 'Failed to save lead'
                        });
                    }
                }).catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Failed to save lead. Please try again.'
                    });
                });
            });


ab kre isse shi se

   /**
     * Store a newly created lead.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'company' => 'required|string|max:255',
            'description' => 'required|string',
            'source' => 'required|string|max:255',
            'expected_value' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        $lead = Lead::create([
            'user_id' => Auth::id(),
            'status_id' => LeadStatus::where('slug', 'new')->first()->id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'company' => $request->company,
            'notes' => $request->description,
            'source' => $request->source,
            'expected_amount' => $request->expected_value,
            'notes' => $request->notes
        ]);
        
        $user = Auth::user();
        $today = now()->toDateString();

        // Check if the user has marked attendance today
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->exists();

        if (!$todayAttendance) {
            return response()->json([
                'success' => false,
                'message' => 'आप आज की अटेंडेंस लगाए बिना नई लीड नहीं जोड़ सकते।'
            ], 403);
        }
        // Create initial activity
        $lead->createActivity(
            'Lead Created',
            'Lead was added to the system',
            Auth::user()
        );

        // Send WhatsApp notification to admin
        $admin = User::whereHas('role', function($query) {
            $query->where('name', 'admin');
        })->first();

        if ($admin) {
            $this->whatsappService->sendNewLeadNotification($admin, $lead);
        }

        // Check if the request is an AJAX request
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Lead created successfully',
                'lead' => $lead
            ]);
        }

        // Fallback for non-AJAX requests
        return redirect()->back()->with('success', 'Lead created successfully');
    }

    /**
     * Display the specified lead.
     */
    public function show(Lead $lead)
    {
        $this->authorize('view', $lead);
        return response()->json($lead);
    }

    /**
     * Update the specified lead.
     */
    public function update(Request $request, Lead $lead)
    {
        $this->authorize('update', $lead);

        $validatedData = $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'company' => 'required|string|max:255',
            'description' => 'required|string',
            'source' => 'required|string|max:255',
            'expected_value' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ]);


        $user = Auth::user();
        $today = now()->toDateString();

        // Check if the user has marked attendance today
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->exists();

        if (!$todayAttendance) {
            return response()->json([
                'success' => false,
                'message' => 'आप आज की अटेंडेंस लगाए बिना नई लीड नहीं अपडेट कर सकते।'
            ], 403);
        }

        if ($lead->id != $request->lead_id) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid lead ID.'
            ], 403);
        }

        $lead->update($validatedData);

        // Create activity for the update
        $lead->createActivity(
            'Lead Updated',
            'Lead information was updated',
            Auth::user()
        );

        // If status changed to converted, send notification
        if ($lead->wasChanged('status') && $lead->status === 'converted') {
            $this->whatsappService->sendLeadConversionNotification($lead->user, $lead);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lead updated successfully',
            'lead' => $lead
        ]);
    }


 // Leads
    Route::post('/leads', [LeadController::class, 'store'])->name('salesperson.leads.store');
    Route::get('/leads/{lead}', [LeadController::class, 'show'])->name('salesperson.leads.show');
    Route::put('/leads/{lead}', [LeadController::class, 'update'])->name('salesperson.leads.update');
    Route::delete('/leads/{lead}', [LeadController::class, 'destroy'])->name('salesperson.leads.destroy');
    Route::put('/leads/{lead}/status', [LeadController::class, 'updateStatus'])->name('salesperson.leads.status');
    Route::get('/leads/status/{status}', [LeadController::class, 'getLeadsByStatus'])->name('salesperson.leads.by-status');
    Route::get('/leads/stats', [LeadController::class, 'getLeadStats'])->name('salesperson.leads.stats');

iss wale ko kre bina websoket ke 
bina websocket or pusher ke kre isse taaki shi se chal ske kyunki sbmit hokar response to aata hi hai to wahan data dikh jaaye sath ke sath hi aese bhi to ho skta hai isse or tarike se kre



kya aap kanban ka design thoda improve krke professional kr skte ho jisse wo or behtarin ban jaaye with colorfull bootstrap5 + css +js se or card ko swap kre to uska status bhi update ho ske shi se