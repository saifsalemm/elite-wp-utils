<?php
// Register the custom admin menu page
function revenue_admin_page()
{
    add_menu_page(
        'Revenue',
        'Revenue',
        'manage_options',
        'revenue-admin-page',
        'revenue_admin_page_content',
        'dashicons-money-alt',
        98
    );
}
add_action('admin_menu', 'revenue_admin_page');

function get_tutors()
{
    $args = array(
        'role' => 'author'
    );

    $tutors = get_users($args);

    foreach ($tutors as $tutor) {
        echo '<option value="' . $tutor->ID . '">' . $tutor->display_name . ' - ' . $tutor->user_email . '</option>';
    }
}

// Output the content of the custom admin page
function revenue_admin_page_content()
{
?>
    <div class="wrap">
        <style>
            .elite-spinner {
                display: inline-block;
                border: 2px solid rgba(0, 0, 0, 0.2);
                border-top-color: #333;
                border-radius: 50%;
                width: 20px;
                height: 20px;
                animation: spin 0.8s linear infinite;
            }

            @keyframes spin {
                to {
                    transform: rotate(360deg);
                }
            }

            .updates-msg {
                display: flex;
                width: 10%;
                min-height: 2rem;
                background-color: #63f26650;
                border: 1px solid #63f266;
                border-radius: 10px;
                color: black;
            }

            .tutor-error-msg {
                margin: auto;
            }

            input,
            select,
            .updates-msg {
                margin: 3px 3px 3px 0;
                width: 25%;
            }

            form {
                margin: 5px;
            }

            @media screen and (max-width: 600px) {

                input,
                select,
                .updates-msg {
                    margin: 3px 3px 3px 0;
                    width: 80%;
                }
            }
        </style>
        <!------------------------------------------------------------------------------------------------------------------------------>
        <h1>Get tutor platform fees</h1>

        <form id="get_tutor_fees_form">

            <label for="tutor">Select Tutor: </label><br>
            <select id="tutor_id" name="tutor_id" required>
                <?php echo get_tutors(); ?>
            </select><br>

            <label for="month_fees">Select Month: </label><br>
            <select id="month_fees" name="month_fees" required>
                <option value="1">January</option>
                <option value="2">February</option>
                <option value="3">March</option>
                <option value="4">April</option>
                <option value="5">May</option>
                <option value="6">June</option>
                <option value="7">July</option>
                <option value="8">August</option>
                <option value="9">September</option>
                <option value="10">October</option>
                <option value="11">November</option>
                <option value="12">December</option>
            </select><br>

            <input type="submit" name="submit_meta_update" value="Update"><br>
            <div id="updates-msg" class="updates-msg" style="display:none;">
                <p id="tutor-error-msg" class="tutor-error-msg"></p>
            </div>
            <span id="updates-spinner" class="elite-spinner" style="display:none;"></span>
        </form><br><br><br>

        <div id="tutor_fees_data_div" style="display:none;">
        </div>
        <!------------------------------------------------------------------------------------------------------------------------------>
        <script>
            jQuery('#get_tutor_fees_form').on('submit', function(event) {
                event.preventDefault();
                jQuery('#elite-spinner').show();
                var tutor_id = jQuery('#tutor_id').val();
                var month = jQuery('#month_fees').val();

                jQuery.ajax({
                    type: 'POST',
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    data: {
                        action: 'get_tutor_fees_form',
                        tutor_id,
                        month
                    },
                    success: function(response) {
                        jQuery('#elite-spinner').hide();
                        jQuery('#tutor_fees_data_div').html(response);
                    },
                    error: function(xhr, status, error) {
                        console.log('Error:', xhr.responseText);
                        jQuery('#elite-spinner').hide();
                        jQuery('#tutor-error-msg').html('Error happened');
                    }
                });
            });
        </script>
    </div>
<?php
}
