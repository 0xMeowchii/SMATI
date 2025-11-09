<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMATI Registration</title>
    <link rel="icon" type="image/png" href="images/logo5.png">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        :root {
            --navy-blue: #001f3f;
            --gold: #FFD700;
            --light-gold: #FFF8DC;
            --light-blue: #e6f2ff;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }

        .navbar {
            background-color: var(--navy-blue);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            color: var(--gold) !important;
            font-weight: 700;
            font-size: 1.5rem;
        }

        .btn-primary {
            background-color: var(--navy-blue);
            border-color: var(--navy-blue);
            padding: 10px 25px;
            font-weight: 500;
        }

        .btn-primary:hover {
            background-color: #003366;
            border-color: #003366;
        }

        .btn-outline-primary {
            color: var(--navy-blue);
            border-color: var(--navy-blue);
            padding: 10px 25px;
            font-weight: 500;
        }

        .btn-outline-primary:hover {
            background-color: var(--navy-blue);
            color: white;
        }

        .registration-container {
            max-width: 1000px;
            margin: 30px auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .registration-header {
            background: linear-gradient(135deg, var(--navy-blue) 0%, #003366 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .registration-header h1 {
            font-weight: 700;
            margin-bottom: 10px;
        }

        .registration-header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .registration-body {
            padding: 40px;
        }

        .form-section {
            margin-bottom: 30px;
            padding: 25px;
            border-radius: 10px;
            background-color: #f9f9f9;
            border-left: 4px solid var(--navy-blue);
        }

        .section-title {
            color: var(--navy-blue);
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .section-title i {
            margin-right: 10px;
            font-size: 1.2rem;
        }

        .form-label {
            font-weight: 500;
            color: #555;
            margin-bottom: 8px;
        }

        .form-control,
        .form-select {
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid #ddd;
            transition: all 0.3s;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--navy-blue);
            box-shadow: 0 0 0 0.2rem rgba(0, 31, 63, 0.25);
        }

        .progress-container {
            margin-bottom: 40px;
        }

        .progress {
            height: 8px;
            margin-bottom: 10px;
        }

        .step-indicator {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin-top: 20px;
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 2;
        }

        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: #666;
            margin-bottom: 8px;
            transition: all 0.3s;
        }

        .step.active .step-circle {
            background-color: var(--navy-blue);
            color: white;
        }

        .step.completed .step-circle {
            background-color: var(--gold);
            color: var(--navy-blue);
        }

        .step-label {
            font-size: 0.85rem;
            color: #666;
            text-align: center;
            font-weight: 500;
        }

        .step.active .step-label {
            color: var(--navy-blue);
            font-weight: 600;
        }

        .hidden {
            display: none;
        }

        .registration-number {
            background-color: var(--light-gold);
            padding: 15px;
            border-radius: 8px;
            font-weight: 600;
            color: var(--navy-blue);
            margin-bottom: 25px;
            text-align: center;
            border: 1px dashed var(--gold);
        }

        .summary-card {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            border-left: 4px solid var(--navy-blue);
        }

        .summary-item {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .summary-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .summary-label {
            font-weight: 600;
            color: #555;
            margin-bottom: 5px;
        }

        .summary-value {
            color: #333;
        }

        .gold-highlight {
            color: var(--gold);
            font-weight: 600;
        }

        .form-check-input:checked {
            background-color: var(--navy-blue);
            border-color: var(--navy-blue);
        }

        .form-check-label {
            color: #555;
        }

        .action-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }

        .program-option {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            margin-bottom: 15px;
            height: 100%;
        }

        .program-option:hover {
            border-color: var(--navy-blue);
            transform: translateY(-5px);
        }

        .program-option.selected {
            border-color: var(--navy-blue);
            background-color: var(--light-blue);
        }

        .program-icon {
            font-size: 2.5rem;
            color: var(--navy-blue);
            margin-bottom: 15px;
        }

        .program-title {
            font-weight: 600;
            color: var(--navy-blue);
            margin-bottom: 10px;
        }

        footer {
            background-color: var(--navy-blue);
            color: white;
            padding: 20px 0;
            margin-top: 50px;
            text-align: center;
        }

        @media (max-width: 768px) {
            .registration-body {
                padding: 20px;
            }

            .step-label {
                font-size: 0.75rem;
            }

            .action-buttons {
                flex-direction: column;
                gap: 10px;
            }

            .action-buttons button {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="/SMATI/">
                <i class="fas fa-graduation-cap me-2"></i>SMATI
            </a>
        </div>
    </nav>

    <!-- Registration Form -->
    <div class="container">
        <div class="registration-container">
            <div class="registration-header">
                <h1>Student Registration</h1>
                <p>Complete the form below to register for your desired program</p>
            </div>

            <div class="registration-body">
                <!-- Progress Indicator -->
                <div class="progress-container">
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width: 25%;" id="progress-bar"></div>
                    </div>

                    <div class="step-indicator">
                        <div class="step active" id="step1">
                            <div class="step-circle">1</div>
                            <div class="step-label">Personal Info</div>
                        </div>
                        <div class="step" id="step2">
                            <div class="step-circle">2</div>
                            <div class="step-label">Contact Details</div>
                        </div>
                        <div class="step" id="step3">
                            <div class="step-circle">3</div>
                            <div class="step-label">Program Selection</div>
                        </div>
                        <div class="step" id="step4">
                            <div class="step-circle">4</div>
                            <div class="step-label">Review & Submit</div>
                        </div>
                    </div>
                </div>

                <!-- Step 1: Personal Information -->
                <div class="form-section" id="step1-form">
                    <h3 class="section-title"><i class="fas fa-user"></i> Personal Information</h3>

                    <div class="registration-number">
                        Registration Number: <div id="registerNumberDisplay"></div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="firstName" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="firstName" required>
                            <div class="invalid-feedback">
                                Please provide a valid first name.
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="lastName" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="lastName" required>
                            <div class="invalid-feedback">
                                Please provide a valid last name.
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="birthDate" class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" id="birthDate" required>
                            <div class="invalid-feedback">
                                Please provide your date of birth.
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="gender" class="form-label">Gender</label>
                            <select class="form-select" id="gender" required>
                                <option value="" selected disabled>Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                            <div class="invalid-feedback">
                                Please select your gender.
                            </div>
                        </div>
                    </div>

                    <div class="action-buttons">
                        <button type="button" class="btn btn-outline-primary" disabled>Previous</button>
                        <button type="button" class="btn btn-primary" id="next-to-step2">Next</button>
                    </div>
                </div>

                <!-- Step 2: Contact Details -->
                <div class="form-section hidden" id="step2-form">
                    <h3 class="section-title"><i class="fas fa-address-book"></i> Contact Details</h3>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" required>
                        <div class="invalid-feedback">
                            Please provide a valid email address.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Contact Number</label>
                        <input type="tel" class="form-control" id="phone" required>
                        <div class="invalid-feedback">
                            Please provide a valid phone number.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" rows="3" required></textarea>
                        <div class="invalid-feedback">
                            Please provide your complete address.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="schoolVisitation" class="form-label">School Visitation</label>
                        <select class="form-select" id="schoolVisitation" required>
                            <option value="" selected disabled>Select Visitation Option</option>
                            <option value="yes">Yes, I would like to schedule a school visit</option>
                            <option value="no">No, I don't need a school visit</option>
                            <option value="already">I have already visited the school</option>
                        </select>
                        <div class="invalid-feedback">
                            Please select a visitation option.
                        </div>
                    </div>

                    <div class="action-buttons">
                        <button type="button" class="btn btn-outline-primary" id="prev-to-step1">Previous</button>
                        <button type="button" class="btn btn-primary" id="next-to-step3">Next</button>
                    </div>
                </div>

                <!-- Step 3: Program Selection -->
                <div class="form-section hidden" id="step3-form">
                    <h3 class="section-title"><i class="fas fa-book-open"></i> Program Selection</h3>

                    <div class="mb-4">
                        <label class="form-label">Select Program Category</label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="program-option" data-category="shs">
                                    <div class="program-icon">
                                        <i class="fas fa-user-graduate"></i>
                                    </div>
                                    <div class="program-title">Senior High School</div>
                                    <p>Complete your secondary education with specialized tracks</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="program-option" data-category="college">
                                    <div class="program-icon">
                                        <i class="fas fa-university"></i>
                                    </div>
                                    <div class="program-title">College</div>
                                    <p>Pursue higher education with our degree programs</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3" id="shsPrograms">
                        <label for="shsTrack" class="form-label">Select SHS Track</label>
                        <select class="form-select" id="shsTrack" required>
                            <option value="" selected disabled>Select Track</option>
                            <option value="academic">Academic Track</option>
                            <option value="tvl">TVL Track</option>
                            <option value="arts">Arts & Design</option>
                            <option value="sports">Sports Track</option>
                        </select>
                        <div class="invalid-feedback">
                            Please select a track.
                        </div>

                        <div id="academicStrands" class="mt-3 hidden">
                            <label for="academicStrand" class="form-label">Select Academic Strand</label>
                            <select class="form-select" id="academicStrand">
                                <option value="" selected disabled>Select Strand</option>
                                <option value="abm">ABM - Accountancy, Business, and Management</option>
                                <option value="stem">STEM - Science, Technology, Engineering, and Mathematics</option>
                                <option value="humss">HUMSS - Humanities and Social Sciences</option>
                                <option value="gas">GAS - General Academic Strand</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3 hidden" id="collegePrograms">
                        <label for="collegeProgram" class="form-label">Select College Program</label>
                        <select class="form-select" id="collegeProgram">
                            <option value="" selected disabled>Select Program</option>
                            <option value="cist">CIST - Computer Science and Information Technology</option>
                            <option value="hrt">HRT - Hotel and Restaurant Technology</option>
                        </select>
                        <div class="invalid-feedback">
                            Please select a program.
                        </div>
                    </div>

                    <div class="action-buttons">
                        <button type="button" class="btn btn-outline-primary" id="prev-to-step2">Previous</button>
                        <button type="button" class="btn btn-primary" id="next-to-step4">Next</button>
                    </div>
                </div>

                <!-- Step 4: Review and Submit -->
                <div class="form-section hidden" id="step4-form">
                    <h3 class="section-title"><i class="fas fa-clipboard-check"></i> Review Your Information</h3>

                    <div class="summary-card mb-4">
                        <h5 class="mb-3" style="color: var(--navy-blue);">Registration Summary</h5>
                        <div id="summary-content">
                            <!-- Summary will be populated here -->
                        </div>
                    </div>

                    <!-- reCAPTCHA -->
                    <div class="mb-4">
                        <div class="g-recaptcha" data-sitekey="6LdxyvQrAAAAAMCDZVWlknaTzOMK_q6CT6Wx4min"></div>
                        <div class="invalid-feedback">
                            Please complete the reCAPTCHA.
                        </div>
                    </div>

                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="agreeTerms" required>
                        <label class="form-check-label" for="agreeTerms">
                            I agree to the terms and conditions
                        </label>
                        <div class="invalid-feedback">
                            You must agree before submitting.
                        </div>
                    </div>

                    <div class="action-buttons">
                        <button type="button" class="btn btn-outline-primary" id="prev-to-step3">Previous</button>
                        <button type="button" class="btn btn-success" id="submit-registration">Submit Registration</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy; 2025 SMATI. All rights reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- reCAPTCHA -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <script>
        // Function to generate registration number from server
        function generateRegisterNumber() {
            fetch('generate-reg-number.php')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Generated register number:', data.registerNumber);
                    if (data.registerNumber) {
                        document.getElementById("registerNumberDisplay").textContent = data.registerNumber;
                    } else {
                        throw new Error('No register number received');
                    }
                })
                .catch(error => {
                    console.error('Error generating register number:', error);
                    // Fallback: generate client-side number
                    generateFallbackRegisterNumber();
                });
        }

        // Fallback function if server generation fails
        function generateFallbackRegisterNumber() {
            const year = new Date().getFullYear();
            const timestamp = Date.now().toString().slice(-6); // Use last 6 digits for better uniqueness
            const random = Math.floor(Math.random() * 1000); // Add random component
            const registerNumber = `SMATIReg${year}-${timestamp}${random.toString().padStart(3, '0')}`;

            document.getElementById("registerNumberDisplay").textContent = registerNumber;
        }

        // Handle form submission
        document.getElementById('submit-registration').addEventListener('click', function() {
            if (!validateStep4()) {
                return;
            }

            // Show loading state
            const submitBtn = document.getElementById('submit-registration');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';
            submitBtn.disabled = true;

            // Collect all form data
            const formData = {
                reg_number: document.getElementById('registerNumberDisplay').textContent,
                firstname: document.getElementById('firstName').value,
                lastname: document.getElementById('lastName').value,
                birthdate: document.getElementById('birthDate').value,
                gender: document.getElementById('gender').value,
                email: document.getElementById('email').value,
                phone: document.getElementById('phone').value,
                address: document.getElementById('address').value,
                school_visit: document.getElementById('schoolVisitation').value,
                program: document.querySelector('.program-option.selected').getAttribute('data-category'),
                program_details: getProgramDetails()
            };

            // Submit to server
            fetch('submit-registration.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Registration Successful!',
                            html: `
            <p>Your registration has been submitted successfully.</p>
            <p><strong>Registration Number:</strong> ${formData.reg_number}</p>
            <p>We will contact you soon regarding the next steps.</p>
        `,
                            confirmButtonColor: '#001f3f',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            // Redirect to home or clear form
                            window.location.href = 'register.php';
                        });
                    } else {
                        // Check if it's a duplicate registration error
                        if (data.message.includes('already registered')) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Already Registered',
                                html: `
                <p>${data.message}</p>
                <p class="small mt-2">If you believe this is an error, please contact the administration.</p>
            `,
                                confirmButtonColor: '#001f3f',
                                confirmButtonText: 'OK'
                            });
                        } else {
                            throw new Error(data.message);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Submission Failed',
                        text: 'There was an error submitting your registration. Please try again.',
                        confirmButtonColor: '#001f3f'
                    });
                })
                .finally(() => {
                    // Restore button state
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
        });

        // Helper function to get program details string
        function getProgramDetails() {
            const selectedCategory = document.querySelector('.program-option.selected');
            const programCategory = selectedCategory ? selectedCategory.getAttribute('data-category') : '';

            if (programCategory === 'shs') {
                const shsTrack = document.getElementById('shsTrack').value;
                let program = getTrackName(shsTrack);

                if (shsTrack === 'academic') {
                    const academicStrand = document.getElementById('academicStrand').value;
                    program += ' - ' + getStrandName(academicStrand);
                }

                return program;
            } else {
                const collegeProgram = document.getElementById('collegeProgram').value;
                return getProgramName(collegeProgram);
            }
        }

        // Form navigation
        document.addEventListener('DOMContentLoaded', function() {
            generateRegisterNumber();

            // Step navigation
            document.getElementById('next-to-step2').addEventListener('click', function() {
                if (validateStep1()) {
                    showStep(2);
                }
            });

            document.getElementById('next-to-step3').addEventListener('click', function() {
                if (validateStep2()) {
                    showStep(3);
                }
            });

            document.getElementById('next-to-step4').addEventListener('click', function() {
                if (validateStep3()) {
                    updateSummary();
                    showStep(4);
                }
            });

            document.getElementById('prev-to-step1').addEventListener('click', function() {
                showStep(1);
            });

            document.getElementById('prev-to-step2').addEventListener('click', function() {
                showStep(2);
            });

            document.getElementById('prev-to-step3').addEventListener('click', function() {
                showStep(3);
            });

            // Program category selection
            document.querySelectorAll('.program-option').forEach(option => {
                option.addEventListener('click', function() {
                    // Remove selected class from all options
                    document.querySelectorAll('.program-option').forEach(opt => {
                        opt.classList.remove('selected');
                    });

                    // Add selected class to clicked option
                    this.classList.add('selected');

                    const category = this.getAttribute('data-category');

                    if (category === 'shs') {
                        document.getElementById('shsPrograms').classList.remove('hidden');
                        document.getElementById('collegePrograms').classList.add('hidden');
                        document.getElementById('shsTrack').required = true;
                        document.getElementById('collegeProgram').required = false;
                    } else {
                        document.getElementById('shsPrograms').classList.add('hidden');
                        document.getElementById('collegePrograms').classList.remove('hidden');
                        document.getElementById('shsTrack').required = false;
                        document.getElementById('collegeProgram').required = true;
                    }
                });
            });

            // Academic track selection
            document.getElementById('shsTrack').addEventListener('change', function() {
                if (this.value === 'academic') {
                    document.getElementById('academicStrands').classList.remove('hidden');
                } else {
                    document.getElementById('academicStrands').classList.add('hidden');
                }
            });

            // Input validation
            document.getElementById('phone').addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });

            document.getElementById('email').addEventListener('blur', function() {
                validateEmail(this);
            });
        });

        // Show specific step
        function showStep(stepNumber) {
            // Hide all steps
            document.querySelectorAll('.form-section').forEach(section => {
                section.classList.add('hidden');
            });

            // Show selected step
            document.getElementById(`step${stepNumber}-form`).classList.remove('hidden');

            // Update step indicators
            document.querySelectorAll('.step').forEach(step => {
                step.classList.remove('active', 'completed');
            });

            for (let i = 1; i <= stepNumber; i++) {
                if (i === stepNumber) {
                    document.getElementById(`step${i}`).classList.add('active');
                } else {
                    document.getElementById(`step${i}`).classList.add('completed');
                }
            }

            // Update progress bar
            const progressPercentage = (stepNumber / 4) * 100;
            document.getElementById('progress-bar').style.width = `${progressPercentage}%`;
        }

        // Validation functions
        function validateStep1() {
            let isValid = true;
            const firstName = document.getElementById('firstName');
            const lastName = document.getElementById('lastName');
            const birthDate = document.getElementById('birthDate');
            const gender = document.getElementById('gender');

            if (!firstName.value.trim()) {
                firstName.classList.add('is-invalid');
                isValid = false;
            } else {
                firstName.classList.remove('is-invalid');
            }

            if (!lastName.value.trim()) {
                lastName.classList.add('is-invalid');
                isValid = false;
            } else {
                lastName.classList.remove('is-invalid');
            }

            if (!birthDate.value) {
                birthDate.classList.add('is-invalid');
                isValid = false;
            } else {
                birthDate.classList.remove('is-invalid');
            }

            if (!gender.value) {
                gender.classList.add('is-invalid');
                isValid = false;
            } else {
                gender.classList.remove('is-invalid');
            }

            if (!isValid) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Please fill in all required fields correctly.',
                    confirmButtonColor: '#001f3f'
                });
            }

            return isValid;
        }

        function validateStep2() {
            let isValid = true;
            const email = document.getElementById('email');
            const phone = document.getElementById('phone');
            const address = document.getElementById('address');
            const schoolVisitation = document.getElementById('schoolVisitation');

            if (!validateEmail(email)) {
                isValid = false;
            }

            if (!phone.value.trim() || phone.value.length < 10) {
                phone.classList.add('is-invalid');
                isValid = false;
            } else {
                phone.classList.remove('is-invalid');
            }

            if (!address.value.trim()) {
                address.classList.add('is-invalid');
                isValid = false;
            } else {
                address.classList.remove('is-invalid');
            }

            if (!schoolVisitation.value) {
                schoolVisitation.classList.add('is-invalid');
                isValid = false;
            } else {
                schoolVisitation.classList.remove('is-invalid');
            }

            if (!isValid) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Please fill in all required fields correctly.',
                    confirmButtonColor: '#001f3f'
                });
            }

            return isValid;
        }

        function validateStep3() {
            let isValid = true;

            // Check if a program category is selected
            const selectedCategory = document.querySelector('.program-option.selected');
            if (!selectedCategory) {
                Swal.fire({
                    icon: 'error',
                    title: 'Program Selection Required',
                    text: 'Please select a program category.',
                    confirmButtonColor: '#001f3f'
                });
                return false;
            }

            const programCategory = selectedCategory.getAttribute('data-category');

            if (programCategory === 'shs') {
                const shsTrack = document.getElementById('shsTrack');
                if (!shsTrack.value) {
                    shsTrack.classList.add('is-invalid');
                    isValid = false;
                } else {
                    shsTrack.classList.remove('is-invalid');

                    // If academic track, validate strand selection
                    if (shsTrack.value === 'academic') {
                        const academicStrand = document.getElementById('academicStrand');
                        if (!academicStrand.value) {
                            academicStrand.classList.add('is-invalid');
                            isValid = false;
                        } else {
                            academicStrand.classList.remove('is-invalid');
                        }
                    }
                }
            } else {
                const collegeProgram = document.getElementById('collegeProgram');
                if (!collegeProgram.value) {
                    collegeProgram.classList.add('is-invalid');
                    isValid = false;
                } else {
                    collegeProgram.classList.remove('is-invalid');
                }
            }

            if (!isValid) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Please select your program details.',
                    confirmButtonColor: '#001f3f'
                });
            }

            return isValid;
        }

        function validateStep4() {
            let isValid = true;

            // Check reCAPTCHA
            const recaptchaResponse = grecaptcha.getResponse();
            if (recaptchaResponse.length === 0) {
                document.querySelector('.g-recaptcha').classList.add('is-invalid');
                isValid = false;
            } else {
                document.querySelector('.g-recaptcha').classList.remove('is-invalid');
            }

            // Check terms agreement
            const agreeTerms = document.getElementById('agreeTerms');
            if (!agreeTerms.checked) {
                agreeTerms.classList.add('is-invalid');
                isValid = false;
            } else {
                agreeTerms.classList.remove('is-invalid');
            }

            if (!isValid) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Please complete the reCAPTCHA and agree to the terms.',
                    confirmButtonColor: '#001f3f'
                });
            }

            return isValid;
        }

        function validateEmail(input) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(input.value)) {
                input.classList.add('is-invalid');
                return false;
            } else {
                input.classList.remove('is-invalid');
                return true;
            }
        }

        function updateSummary() {
            const summaryContent = document.getElementById('summary-content');
            const firstName = document.getElementById('firstName').value;
            const lastName = document.getElementById('lastName').value;
            const birthDate = document.getElementById('birthDate').value;
            const gender = document.getElementById('gender').value;
            const email = document.getElementById('email').value;
            const phone = document.getElementById('phone').value;
            const address = document.getElementById('address').value;
            const schoolVisitation = document.getElementById('schoolVisitation').value;
            const selectedCategory = document.querySelector('.program-option.selected');
            const programCategory = selectedCategory ? selectedCategory.getAttribute('data-category') : '';

            let programDetails = '';
            if (programCategory === 'shs') {
                const shsTrack = document.getElementById('shsTrack').value;
                programDetails = `Senior High School - ${getTrackName(shsTrack)}`;

                if (shsTrack === 'academic') {
                    const academicStrand = document.getElementById('academicStrand').value;
                    programDetails += ` - ${getStrandName(academicStrand)}`;
                }
            } else {
                const collegeProgram = document.getElementById('collegeProgram').value;
                programDetails = `College - ${getProgramName(collegeProgram)}`;
            }

            // FIX: Use registerNumberDisplay instead of reg-number
            const registerNumber = document.getElementById('registerNumberDisplay').textContent;

            summaryContent.innerHTML = `
            <div class="summary-item">
                <div class="summary-label">Registration Number</div>
                <div class="summary-value gold-highlight">${registerNumber}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Name</div>
                <div class="summary-value">${firstName} ${lastName}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Date of Birth</div>
                <div class="summary-value">${birthDate}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Gender</div>
                <div class="summary-value">${gender.charAt(0).toUpperCase() + gender.slice(1)}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Email</div>
                <div class="summary-value">${email}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Phone</div>
                <div class="summary-value">${phone}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Address</div>
                <div class="summary-value">${address}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">School Visitation</div>
                <div class="summary-value">${getVisitationOption(schoolVisitation)}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Program</div>
                <div class="summary-value">${programDetails}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Registration Date</div>
                <div class="summary-value">${new Date().toLocaleString()}</div>
            </div>
    `;
        }

        // Helper functions for program names
        function getTrackName(track) {
            const tracks = {
                'academic': 'Academic Track',
                'tvl': 'TVL Track',
                'arts': 'Arts & Design',
                'sports': 'Sports Track'
            };
            return tracks[track] || track;
        }

        function getStrandName(strand) {
            const strands = {
                'abm': 'ABM - Accountancy, Business, and Management',
                'stem': 'STEM - Science, Technology, Engineering, and Mathematics',
                'humss': 'HUMSS - Humanities and Social Sciences',
                'gas': 'GAS - General Academic Strand'
            };
            return strands[strand] || strand;
        }

        function getProgramName(program) {
            const programs = {
                'cist': 'CIST - Computer Science and Information Technology',
                'hrt': 'HRT - Hotel and Restaurant Technology'
            };
            return programs[program] || program;
        }

        function getVisitationOption(option) {
            const options = {
                'yes': 'Yes, I would like to schedule a school visit',
                'no': 'No, I don\'t need a school visit',
                'already': 'I have already visited the school'
            };
            return options[option] || option;
        }

        function getProgramDetails() {
            const selectedCategory = document.querySelector('.program-option.selected');
            const programCategory = selectedCategory ? selectedCategory.getAttribute('data-category') : '';

            if (programCategory === 'shs') {
                const shsTrack = document.getElementById('shsTrack').value;
                let program = getTrackName(shsTrack);

                if (shsTrack === 'academic') {
                    const academicStrand = document.getElementById('academicStrand').value;
                    program += ` - ${getStrandName(academicStrand)}`;
                }

                return program;
            } else {
                const collegeProgram = document.getElementById('collegeProgram').value;
                return getProgramName(collegeProgram);
            }
        }
    </script>
</body>

</html>