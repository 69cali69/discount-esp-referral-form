<?php
if (!class_exists('Dotenv\Dotenv')) {
    // Correct the path to point to the vendor directory
    require_once __DIR__ . '/../vendor/autoload.php'; // Adjust the path as necessary

    // Initialize and load dotenv
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../'); // Adjust the path as necessary
    $dotenv->load();
}

$userInfoFields = [
    [
        'name' => 'first_name',
        'placeholder' => 'First Name',
        'type' => 'text',
        'id' => 'first_name',
        'value' => '',
        'maxlength' => '50'
    ],
    [
        'name' => 'last_name',
        'placeholder' => 'Last Name',
        'type' => 'text',
        'id' => 'last_name',
        'value' => '',
        'maxlength' => '50'
    ],
    [
        'name' => 'email',
        'placeholder' => 'Email',
        'type' => 'email',
        'id' => 'email',
        'value' => '',
        'maxlength' => '100'
    ],
    [
        'name' => 'phone',
        'placeholder' => 'Phone',
        'type' => 'text',
        'id' => 'phone',
        'value' => '',
        'maxlength' => '10'
    ],
    [
        'name' => 'referral_code',
        'placeholder' => 'Referral Code',
        'type' => 'text',
        'id' => 'referral_code',
        'value' => '',
        'maxlength' => '20'
    ]
];

if (isset($_POST['referral_form_submission']) && wp_verify_nonce($_POST['referral_form_nonce'], 'referral_form')) {
    
    $data = [];
    foreach ($_POST as $key => $value) {
        $data[$key] = sanitize_text_field($value);
    }

    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        echo '<div class="alert alert-error">Invalid email address</div>';
        return;
    }

    if(strlen($data['phone']) !== 10) {
        echo '<div class="alert alert-error">Invalid phone number</div>';
        return;
    }

    if(!is_numeric($data['phone'])) {
        echo '<div class="alert alert-error">Invalid phone number</div>';
    }

    if(!is_numeric($data['mileage'])) {
        echo '<div class="alert alert-error">Invalid mileage</div>';
    }

    $api_key = $_ENV['REFERRAL_ROCK_BASIC_AUTH_KEY'];

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.referralrock.com/api/referrals?shouldSendEmail=true',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => '{
            "programId": ' . $_ENV['REFERRAL_ROCK_PROGRAM_ID'] . ',
            "firstName": "' . $data['first_name'] . '",
            "lastName": "' . $data['last_name'] . '",
            "email": "' . $data['email'] . '",
            "referralCode": "'.$data['referral_code'].'",
            "phoneNumber": "' . $data['phone'] . '",
            "customOption1Name": "Is Your your vehicle currently under factory warranty?",
            "customOption1Value": "' . $data['factory_warranty'] . '",
            "customOption2Name": "What is your approximate current mileage?",
            "customOption2Value": "' . $data['mileage'] . '",
            "customText1Name": "What is your Vehicle Year?",
            "customText1Value": "' . $data['vehicle_year'] . '",
            "customText2Name": "What is your Vehicle Make?",
            "customText2Value": "' . $data['vehicle_make'] . '",
            "customText3Name": "What is your Vehicle Model?",
            "customText3Value": "' . $data['vehicle_model'] . '",
            "status": "pending"
        }',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: Basic ' . $api_key . ''
        )
    ));

    $response = curl_exec($curl);

    curl_close($curl);

    $response = json_decode($response, true);

    $redirect_url = wp_get_referer();

    if ($redirect_url) {
        wp_safe_redirect($redirect_url);
        exit;
    } else {
        wp_redirect(home_url());
    }
}
?>

<div id="form-wrapper">
    <form action="" method="POST">
        <?= wp_nonce_field('referral_form', 'referral_form_nonce') ?>
        <div class="card bg-white rounded-none shadow-lg px-4 lg:px-8 py-5">
            <div class="card-body space-y-1">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-[.75rem]">
                    <?php
                    foreach ($userInfoFields as $field) {
                        echo '<div class="form-control">';
                        echo '<input maxlength="'.$field['maxlength'].'" type="' . $field['type'] . '" name="' . $field['name'] . '" id="' . $field['id'] . '" placeholder="' . $field['placeholder'] . '" value="' . (isset($_POST[$field['name']]) ? esc_attr($_POST[$field['name']]) : '') . '" class="input input-bordered w-full placeholder:text-sm shadow-sm" />';
                        echo '</div>';
                    }
                    ?>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-[.75rem]">
                    <div class="form-control">
                        <select name="vehicle_year" id="vehicle_year" class="select select-bordered">
                            <option value="" selected disabled>Vehicle Year</option>
                            <?php
                            for ($i = 2024; $i >= 1980; $i--) {
                                echo '<option value="' . $i . '">' . $i . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-control">
                        <select name="vehicle_make" id="vehicle_make" class="select select-bordered">
                            <option value="" selected disabled>Vehicle Make</option>
                        </select>
                    </div>
                    <div class="form-control">
                        <select name="vehicle_model" id="vehicle_model" class="select select-bordered">
                            <option value="" selected disabled>Vehicle Model</option>
                        </select>
                    </div>
                    <div class="form-control">
                        <input type="text" class="input input-bordered w-full placeholder:text-sm shadow-sm" name="mileage" id="mileage" placeholder="Mileage">
                    </div>
                    <div class="form-control">
                        <select name="factory_warranty" id="factory_warranty" class="select select-bordered">
                            <option value="" selected disabled>Active Factory Warranty?</option>
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-center">
                    <button type="submit" name="referral_form_submission" class="btn bg-desp-green text-[#425766] w-64 hover:bg-desp-green hover:text-white text-xl capitalize outline-none border-0" style="letter-spacing: 1px;">Get Your Quote</button>
                </div>

                <div style="padding-top: 2.5rem">
                    <p style="font-size: xx-small;">Disclaimer: By submitting this form, I am giving Discount Extended Service Plans consent to contact me by email and/or telephone which may include artificial or pre-recorded calls and/or text messages, delivered via automated technology at the telephone number(s) provided above even if I am on a corporate, state or national Do Not Call Registry. I understand that consent is not a condition of purchase. For SMS messaging, text STOP to stop. Msg and data rates may apply. Max 10 messages per month. The Discount Extended Service Plans Privacy Policy governs our Data Collection Policy.</p>
                </div>
            </div>
        </div>
    </form>
</div>