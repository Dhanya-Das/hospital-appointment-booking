<div class="card shadow">
    <div class="card-body">
        <form id="appointment-booking-form" method="post">
                               
            <div class="mb-3  form-group">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" name="name" placeholder="Your Name" required>
            </div>
            <div class="mb-3  form-group">
                <label for="emailid" class="form-label">Email address</label>
                <input type="email" class="form-control" id="emailid" name="email" placeholder="Your Email" aria-describedby="emailHelp">
            </div>

            <div class="mb-3  form-group">
                <label for="phone">Phone:</label>
                <input type="tel" class="form-control" name="phone" placeholder="Your Phone" required>
            </div>
            <div class="mb-3  form-group">
                <label for="address">Address:</label>
                <textarea class="form-control" name="address" placeholder="Your Address" required></textarea>
            </div>

            <div class="mb-3  form-group">
                <label for="doctor">Doctor's Name:</label>
                <select class="form-control" name="doctor" required>
                    <option value="">Select Doctor</option>
                    <option value="Dr. Smith">Dr. Smith</option>
                    <option value="Dr. Johnson">Dr. Johnson</option>
                    <option value="Dr. Brown">Dr. Brown</option>
                </select>
            </div>

            <div class="mb-3  form-group">
                <label for="appointment-day">Appointment Day:</label>
                <input type="date" class="form-control" name="appointment-day" required>
            </div>

            <div class="mb-3  form-group">
                <label for="appointment-time">Appointment Time:</label>
                <input type="time" class="form-control" name="appointment-time" required>
            </div>
       
            <button type="button" class="btn btn-primary submit-appointment" id="submit-appointment">Book Appointment</button>
       
        </form>
    </div>
</div>
<div id="submission-status"></div>