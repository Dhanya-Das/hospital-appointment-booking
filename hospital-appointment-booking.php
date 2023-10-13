<?php
/*
Plugin Name: Appointments Booking
Description: A Custom plugin to manage hospital appointments.
Version: 1.0
Author: Dhanya Das
*/

define( 'HOSPITAL_APPOINTMENTS',     plugin_dir_url( __FILE__ )  );

add_action('admin_enqueue_scripts', 'scripts_for_HOSPITAL_APPOINTMENTS_js');
add_action('wp_enqueue_scripts', 'scripts_for_HOSPITAL_APPOINTMENTS_js');
function scripts_for_HOSPITAL_APPOINTMENTS_js() {
	wp_enqueue_script('HOSPITAL_APPOINTMENTSjs', HOSPITAL_APPOINTMENTS.'assets/js/bootstrap.js', array('jquery'), '1.1.0', true );
	wp_enqueue_script('hospital-appointment-booking_js', HOSPITAL_APPOINTMENTS.'assets/js/hospital-appointment-booking.js', array('jquery'), '1.1.7', true );
    wp_localize_script('jquery', 'ajax_object', array('ajaxurl' => admin_url('admin-ajax.php')));

}

add_action('wp_enqueue_scripts', 'scripts_for_HOSPITAL_APPOINTMENTS_css');
add_action('admin_enqueue_scripts', 'scripts_for_HOSPITAL_APPOINTMENTS_css');
function scripts_for_HOSPITAL_APPOINTMENTS_css() {
	wp_enqueue_style('HOSPITAL_APPOINTMENTScss', HOSPITAL_APPOINTMENTS.'assets/css/bootstrap.css');
    wp_enqueue_style('hospital-appointment-booking_css', HOSPITAL_APPOINTMENTS.'assets/css/hospital-appointment-booking.css');
}

function create_appointments_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'hospital_appointments';

    $sql = "CREATE TABLE $table_name (
        id INT NOT NULL AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        phone VARCHAR(20) NOT NULL,
        address TEXT NOT NULL,
        doctor VARCHAR(100) NOT NULL,
        appointment_day DATE NOT NULL,
        appointment_time TIME NOT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY unique_id (id)
    ) ENGINE=InnoDB AUTO_INCREMENT=1001;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

register_activation_hook(__FILE__, 'create_appointments_table');

// Define the appointment booking form shortcode
function appointment_booking_form_shortcode() {
    ob_start();
    // Include your appointment booking form HTML here
    include('appointment-form.php');
    return ob_get_clean();
}

add_shortcode('appointment_form', 'appointment_booking_form_shortcode');


function insert_appointment() {
    // Get form data from the AJAX request
    parse_str($_POST['form_data'], $form_data);

    // Insert data into the database
    $name = sanitize_text_field($form_data['name']);
    $email = sanitize_email($form_data['email']);
    $phone = sanitize_text_field($form_data['phone']);
    $address = sanitize_text_field($form_data['address']);
    $doctor = sanitize_text_field($form_data['doctor']);
    $appointment_day = sanitize_text_field($form_data['appointment-day']);
    $appointment_time = sanitize_text_field($form_data['appointment-time']);
        // Data is valid, insert it into the database
        global $wpdb;
        $table_name = $wpdb->prefix . 'hospital_appointments';
        $wpdb->insert(
            $table_name,
            array(
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'address' => $address,
                'doctor' => $doctor,
                'appointment_day' => $appointment_day,
                'appointment_time' => $appointment_time,
            )
        );

        if ($wpdb->insert_id) {
            $response['success'] = true;
            $response['message'] = 'Appointment booked successfully!';
        } else {
            $response['success'] = false;
            $response['message'] = 'Please fill in all the required fields.';
        }

        echo json_encode($response);
    
    wp_die();
}

add_action('wp_ajax_insert_appointment', 'insert_appointment');
add_action('wp_ajax_nopriv_insert_appointment', 'insert_appointment');
// Add a custom admin menu
function patient_appointment_menu() {
    add_menu_page(
        'Appointment Management',
        'Patient & Appointments',
        'read',
        'patient-appointment-management',
        'display_patient_appointment_page'
    );
}
add_action('admin_menu', 'patient_appointment_menu');

// Callback function to display the admin page
function display_patient_appointment_page() {
    ?>
    <div class="wrap">
        <div class="mb-3">
            <h2>Appointment Management</h2>
        </div>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <?php if (!current_user_can('administrator')) { ?>
                <tr>
                    <th>Patient ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Doctor's Name</th>
                    <th>Appointment Day</th>
                    <th>Appointment Time</th>
                    <th>Update</th>
                    <th>Delete</th>
                </tr>
                <?php } else { ?>
                    <tr>
                    <th>Patient ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Doctor's Name</th>
                    <th>Appointment Day</th>
                    <th>Appointment Time</th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <!-- Query and display patient and appointment data here -->
                <?php
                global $wpdb;
                $table_name = $wpdb->prefix . 'hospital_appointments';
                $results = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
                $current_user = wp_get_current_user();
                $user_email = $current_user->user_email;
                if (!empty($results)) {
                    foreach ($results as $row) {
                        echo '<tr>';
                        if (!current_user_can('administrator') && $user_email == $row['email']) { 
                            echo '<td>#' . esc_html($row['id']) . '</td>';
                            echo '<td>' . esc_html($row['name']) . '</td>';
                            echo '<td>' . esc_html($row['email']) . '</td>';
                            echo '<td>' . esc_html($row['phone']) . '</td>';
                            echo '<td>' . esc_html($row['address']) . '</td>';
                            echo '<td>' . esc_html($row['doctor']) . '</td>';
                            echo '<td>' . esc_html(date('d F Y', strtotime($row['appointment_day']))) . '</td>';
                            echo '<td>' . esc_html(date('h:i A', strtotime($row['appointment_time']))) . '</td>';
                        } elseif (current_user_can('administrator')) {
                            echo '<td>#' . esc_html($row['id']) . '</td>';
                            echo '<td>' . esc_html($row['name']) . '</td>';
                            echo '<td>' . esc_html($row['email']) . '</td>';
                            echo '<td>' . esc_html($row['phone']) . '</td>';
                            echo '<td>' . esc_html($row['address']) . '</td>';
                            echo '<td>' . esc_html($row['doctor']) . '</td>';
                            echo '<td>' . esc_html(date('d F Y', strtotime($row['appointment_day']))) . '</td>';
                            echo '<td>' . esc_html(date('h:i A', strtotime($row['appointment_time']))) . '</td>';
                        }
                        if (!current_user_can('administrator') && $user_email == $row['email']) { 

                            // Add an "Edit" button to trigger the edit form
                            echo '<td><button class="edit-record btn btn-info btn-sm" data-id="' . esc_attr($row['id']) . '">Edit</button></td>'; 
                            echo '<td><button class="delete-record btn btn-danger btn-sm" data-id="' . esc_attr($row['id']) . '">Delete</button></td>';


                            echo '</tr>';
                            
                            // Add an edit form for each row (hidden initially)
                            echo '<tr class="edit-form" data-id="' . esc_attr($row['id']) . '" style="display:none;">';
                            echo '<td colspan="8">';
                            echo '<form class="edit-appointment-form">';
                            echo '<div> <input type="hidden" name="id" value="' . esc_attr($row['id']) . '"></div>';
                            echo '<div> <input type="text" name="name" value="' . esc_attr($row['name']) . '"></div>';
                            echo '<div> <input type="email" name="email" value="' . esc_attr($row['email']) . '"></div>';
                            echo '<div> <input type="tel" name="phone" value="' . esc_attr($row['phone']) . '"></div>';
                            echo '<div> <textarea name="address">' . esc_attr($row['address']) . '</textarea></div>';
                            $selectedDoctor = $row['doctor']; 
                            $availableDoctors = array("Dr. Smith", "Dr. Johnson", "Dr. Brown");

                            echo '<div><select class="form-control" name="doctor" required>
                                <option value="">Select Doctor</option>';

                            // Loop through available doctors and generate the options
                            foreach ($availableDoctors as $doctor) {
                                // Check if the current doctor matches the selected doctor
                                $selected = ($doctor === $selectedDoctor) ? 'selected' : '';

                                echo '<option value="' . $doctor . '" ' . $selected . '>' . $doctor . '</option>';
                            }

                            echo '</select></div>';
                            echo '<div> <input type="date" name="appointment_day" value="' . esc_attr($row['appointment_day']) . '"></div>';
                            echo '<div> <input type="time" name="appointment_time" value="' . esc_attr($row['appointment_time']) . '"></div>';

                            echo '<button class="update-button btn btn-primary btn-sm" data-id="' . esc_attr($row['id']) . '">Update</button>';
                            echo '</form>';
                        } 
                        echo '</td>';
                        echo '</tr>';

                        
                    }
                }else {
                    echo '<tr><td colspan="8">No records found.</td></tr>';
                }
                ?>
            </tbody>
        </table>

    </div>
    <?php
}

// Update date
function update_appointment() {
    if (isset($_POST['action']) && $_POST['action'] == 'update_appointment') {
        global $wpdb;
        $table_name = $wpdb->prefix . 'hospital_appointments';
        parse_str($_POST['form'], $form_data);

        $id = intval($form_data['id']);
        $name = sanitize_text_field($form_data['name']);
        $email = sanitize_email($form_data['email']);
        $phone = sanitize_text_field($form_data['phone']);
        $address = sanitize_text_field($form_data['address']);
        $doctor = sanitize_text_field($form_data['doctor']);
        $appointment_day = sanitize_text_field($form_data['appointment_day']);
        $appointment_time = sanitize_text_field($form_data['appointment_time']);

        $update_data = $wpdb->update(
            $table_name,
            array(
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'address' => $address,
                'doctor' => $doctor,
                'appointment_day' => $appointment_day,
                'appointment_time' => $appointment_time,
            ),
            array('id' => $id)
        );
    }

    if ($update_data) {
        $response['success'] = true;
        $response['message'] = 'Data Updated successfully!';
    } else {
        $response['success'] = false;
        $response['message'] = 'Failed to Update';
        $response['appointment_day'] = $appointment_day ;
        $response['appointment_time'] = $appointment_time ;
    }


    echo json_encode($response); 
    wp_die();
}

add_action('wp_ajax_update_appointment', 'update_appointment');
add_action('wp_ajax_nopriv_update_appointment', 'update_appointment');


// Delete data
function delete_appointment() {
    if (isset($_POST['record_id'])) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'hospital_appointments';
        $record_id = intval($_POST['record_id']);
        
        // Delete the record from the database
        $delete_data = $wpdb->delete($table_name, array('id' => $record_id));
        
        // You can provide a response to indicate success or failure
        if ($delete_data) {
            $response['success'] = true;
            $response['message'] = 'Record deleted successfully!';
        } else {
            $response['success'] = false;
            $response['message'] = 'Failed to delete';
        }
       
    }
    echo json_encode($response);
    wp_die();
}

add_action('wp_ajax_delete_appointment', 'delete_appointment');
add_action('wp_ajax_nopriv_delete_appointment', 'delete_appointment');



?>