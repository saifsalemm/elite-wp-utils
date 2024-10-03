<?php

add_action('rest_api_init', function () {
    register_rest_route('elite/v2', '/lesson', array(
        'methods' => 'GET',
        'callback' => 'get_lesson_and_student_data_refactored',
        'permission_callback' => 'is_authenticated',
    ));
});

function get_lesson_and_student_data_refactored(WP_REST_Request $request)
{
    $params = $request->get_params();
    $user_id = $params['user']->uid;
    $user_email = $params['user']->user_email;
    $is_admin = $user_id == '1';

    $slug = $_GET['lesson'];

    $product = get_page_by_path($slug, OBJECT, 'product');
    $product_id = $product->ID;
    $product_name = $product->post_title;
    $lesson_meta = get_post_meta($product_id);

    $is_author = $product->post_author == $user_id;
    $is_purchased = wc_customer_bought_product($user_id, $user_email, $product_id);

    if (!$is_purchased && !$is_author && !$is_admin) {
        $prerequisite_type = get_post_meta($product_id, 'prerequisite_type', true);

        if ($prerequisite_type === 'quiz') {
            $prerequisite_id = get_post_meta(get_post_meta($product_id, 'prerequisite', true), 'quiz_id', true);
            $raw_data = get_grade_by_user_and_quiz_id($prerequisite_id, $user_id);
        } else {
            $prerequisite_id = get_post_meta(get_post_meta($product_id, 'prerequisite', true), 'hw_id', true);
            $homework_results = get_user_meta($user_id, 'homework_results', false);
            $raw_data = null;
            foreach ($homework_results as $res) {
                $result = explode('-', $res);
                if ($result[0] === $prerequisite_id) {
                    $raw_data = get_post_meta($result[1], 'raw_data', true);
                }
            }
        }

        $allowed_payment_methods = [
            "code" => $lesson_meta['payment_method_code'][0] === "yes",
            "wallet" => $lesson_meta['payment_method_wallet'][0] === "yes",
            "fawry" => $lesson_meta['payment_method_fawry'][0] === "yes",
            "vodafone_cash" => $lesson_meta['payment_method_vodafone_cash'][0] === "yes",
        ];

        $prerequisite = !$prerequisite_id || !$lesson_meta['prerequisite'][0] ? false : intval($lesson_meta['prerequisite'][0]);

        $lesson = array(
            "lesson_id" => $product_id,
            "title" => $product_name,
            "date" => get_the_date('Y-m-d', $product_id),
            "price" => intval($lesson_meta['_regular_price'][0]),
            "allowed_payment_methods" => $allowed_payment_methods,
            "last_purchase_date" => $lesson_meta['last_purchase_date'][0],
            "pre" => $prerequisite,
            "hw_raw_data" => $raw_data,
        );

        return $lesson;
    }
}

//         $lesson_meta = get_post_meta($product_id);
//         $questions_data = array();
//         $quiz_data = get_post_meta($lesson_meta['quiz_id'][0]);
//         $questions_splitted = explode('-', $quiz_data['questions_ids'][0]);

//         foreach ($questions_splitted as $question_id) {
//             $question_meta = get_post_meta($question_id);
//             $question_data = array(
//                 "question_id" => $question_id,
//                 "title" => get_the_title($question_id),
//                 "content" => get_the_content(null, false, $question_id),
//                 "img" => $question_meta['image_url'][0],
//                 "weight" => $question_meta['weight'][0],
//                 "answers" => explode('-', $question_meta['answers'][0]),
//                 "correct_answer" => $question_meta['correct_answer'][0],
//                 "hint" => $question_meta['question_hint'][0]
//             );

//             array_push($questions_data, $question_data);
//         }

//         $hwqs_data = array();
//         $hw_data = get_post_meta($lesson_meta['hw_id'][0]);
//         $hwqs_splitted = explode('-', $hw_data['questions_ids'][0]);

//         foreach ($hwqs_splitted as $question_id) {
//             $question_meta = get_post_meta($question_id);
//             $question_data = array(
//                 "question_id" => $question_id,
//                 "title" => get_the_title($question_id),
//                 "content" => get_the_content(null, false, $question_id),
//                 "img" => $question_meta['image_url'][0],
//                 "weight" => $question_meta['weight'][0],
//                 "answers" => explode('-', $question_meta['answers'][0]),
//                 "correct_answer" => $question_meta['correct_answer'][0],
//                 "hint" => $question_meta['question_hint'][0]
//             );

//             array_push($hwqs_data, $question_data);
//         }

//         $lesson_date = get_the_date('Y-m-d', $product_id);
//         $quiz_title = get_the_title($lesson_meta['quiz_id'][0]);
//         $last_trial_date = get_post_meta($lesson_meta['quiz_id'][0], 'last_trial_date', true);

//         $lesson = array(
//             "lesson_id" => $product_id,
//             "title" => $product_name,
//             "date" => $lesson_date,
//             "price" => NULL,
//             "video_exist" => $lesson_meta['video_on_off'][0] === "yes" ? true : false,
//             "videos_data" => $lesson_meta['videos_urls_titles'][0],
//             "video_host" => $lesson_meta['video_host'][0],
//             "quiz_id" => $lesson_meta['quiz_id'][0],
//             "quiz_title" => $quiz_title,
//             "hide_correct_answers" => $quiz_data['hide_correct_answers'][0],
//             "must_answer_all" => $quiz_data['must_answer_all'][0],
//             "quiz_duration" => $quiz_data['time_minutes'][0],
//             "quiz_questions" => $questions_data,
//             "quiz_randomize" => $quiz_data['reorder_questions'][0],
//             "quiz_trials" => $lesson_meta['quiz_trials'][0] ?? 1,
//             "quiz_required" => $lesson_meta['quiz_required'][0] == "yes",
//             "hw_questions" => $hwqs_data,
//             "hw_id" => $lesson_meta['hw_id'][0],
//             "lesson_files" => $lesson_meta['lesson_files'],
//             "views_notification_mark" => $lesson_meta['views_notification_mark'][0],
//             "last_trial_date" => $last_trial_date,
//         );


//         $lesson["expiry_date"] = $lesson_meta['allowed_time'][0] == 0 ? -1 : intval(get_user_meta($user_id, $product_id . '_expiry_date', true));
//         $lesson["remaining_views"] = $lesson_meta['allowed_views'][0] == 0 ? -1 : intval(get_user_meta($user_id, $product_id . '_remaining_views', true));
//         $lesson["past_quiz_trials"] = fetch_grades_by_quiz_and_student($lesson_meta['quiz_id'][0], $user_id);
//         $lesson["xvast_protection"] = $is_admin ? false : $lesson_meta['xvast_protection'][0];
//         $lesson["is_author"] = $product->post_author == $user_id || $is_admin ? true : false;
//         $lesson["is_offline_purchase"] = in_array($product_id, get_user_meta($user_id, 'galal_offline_ids', false));

//         return $lesson;
//     } else {
//         $lesson_meta = get_post_meta($product_id);
//         $prerequisite_type = get_post_meta($product_id, 'prerequisite_type', true);

//         if ($prerequisite_type === 'quiz') {
//             $prerequisite_id = get_post_meta(get_post_meta($product_id, 'prerequisite', true), 'quiz_id', true);
//             $raw_data = get_grade_by_author_and_quiz_id($prerequisite_id);
//         } else {
//             $prerequisite_id = get_post_meta(get_post_meta($product_id, 'prerequisite', true), 'hw_id', true);
//             $homework_results = get_user_meta($user_id, 'homework_results', false);
//             $raw_data = null;
//             foreach ($homework_results as $res) {
//                 $result = explode('-', $res);
//                 if ($result[0] === $prerequisite_id) {
//                     $raw_data = get_post_meta($result[1], 'raw_data', true);
//                 }
//             }
//         }

//         $lesson = array(
//             "lesson_id" => $product_id,
//             "title" => $product_name,
//             "date" => get_the_date('Y-m-d', $product_id),
//             "price" => intval($lesson_meta['_regular_price'][0]),
//             "code" => $lesson_meta['payment_method_code'][0],
//             "wallet" => $lesson_meta['payment_method_wallet'][0],
//             "fawry" => $lesson_meta['payment_method_fawry'][0],
//             "vodafone_cash" => $lesson_meta['payment_method_vodafone_cash'][0],
//             "last_purchase_date" => $lesson_meta['last_purchase_date'][0],
//             "pre" => $lesson_meta['prerequisite'][0] == '' || !$prerequisite_id || !$lesson_meta['prerequisite'][0] ? false : intval($lesson_meta['prerequisite'][0]),
//             "hw_raw_data" => $raw_data,
//         );

//         return $lesson;
//     }

//     return '';
// }




function get_grade_by_user_and_quiz_id($quiz_id, $user_id)
{
    // Query for a post with the specified author and quiz_id
    $args = array(
        'post_type' => 'grades',
        'posts_per_page' => 1,
        'meta_query'     => array(
            array(
                'key'   => 'quiz_id',
                'value' => $quiz_id,
            ),
            array(
                'key'   => 'student_id',
                'value' => $user_id,
            ),
        ),
    );

    $posts = get_posts($args);

    // Check if a post is found
    if (empty($posts)) {
        return false;
    }

    // Get the post data
    return $posts;
}

// function fetch_grades_by_quiz_and_student($quiz_id, $student_id)
// {

//     // $user_data = get_transient('user_quiz_results_' . $student_id . '_' . $quiz_id);

//     // if ($user_data) return $user_data;

//     $args = array(
//         'post_type' => 'grades',
//         'post_status' => 'publish',
//         'meta_query' => array(
//             'relation' => 'AND',
//             array(
//                 'key' => 'quiz_id',
//                 'value' => $quiz_id,
//                 'compare' => '=',
//             ),
//             array(
//                 'key' => 'student_id',
//                 'value' => $student_id,
//                 'compare' => '=',
//             ),
//         ),
//     );

//     $query = new WP_Query($args);

//     if ($query->have_posts()) {
//         $grades_data = array();
//         while ($query->have_posts()) {
//             $query->the_post();
//             $post_id = get_the_ID();
//             $grade = get_post_meta($post_id);
//             $grades_data[] = $grade;
//         }
//         wp_reset_postdata();

//         // Calculate response size in kilobytes
//         $response_size_bytes = strlen(json_encode($grades_data));
//         $response_size_kb = round($response_size_bytes / 1024, 2);

//         $log_data = [
//             get_current_user_id(),
//             'fetch_grades_by_quiz_and_student',
//             date("Y-m-d H:i"),
//             $response_size_kb, // Include response size in kilobytes in the log
//             time()
//         ];

//         // set_transient('user_quiz_results_' . $student_id . '_' . $quiz_id, $grades_data, 3600);

//         return $grades_data;
//     } else {
//         return null;
//     }
// }



// ////////////////////////////////////////////////////////////////////////

// add_action('rest_api_init', function () {
//     register_rest_route('elite/v1', '/get-lesson', array(
//         'methods' => 'GET',
//         'callback' => 'get_lesson',
//         // 'permission_callback' => 'is_authenticated',
//     ));
// });

// function get_lesson()
// {
//     // return get_lesson_data_with_quiz_and_homework($_GET['lesson']);
//     // return $_GET['lesson'];
//     return get_lesson_data($_GET['lesson']);
// }


// function get_lesson_data($lesson_id)
// {
//     global $wpdb;

//     $query = $wpdb->prepare("
//         SELECT wp_posts.ID, wp_posts.post_author, wp_posts.post_content, wp_posts.post_title, wp_posts.post_name, wp_posts.post_type, wp_postmeta.meta_key, wp_postmeta.meta_value
// FROM `wp_posts`
// LEFT JOIN wp_postmeta ON wp_posts.ID = wp_postmeta.post_id
// WHERE wp_posts.ID = %d
//     ", $lesson_id);

//     // Execute the query and return the results
//     return $wpdb->get_results($query);
// }


// function get_lesson_data_with_quiz_and_homework($lesson_id)
// {
//     global $wpdb;

//     $sql = "SELECT 
//       l.ID AS lesson_id,
//       l.post_title AS lesson_title,
//       l.post_content AS lesson_content,
//       lm.meta_key AS lesson_meta_key,
//       lm.meta_value AS lesson_meta_value,
//       /* Quiz Data */
//       q.ID AS quiz_id,
//       q.post_title AS quiz_title,
//       q.post_content AS quiz_content,
//       qm.meta_key AS quiz_meta_key,
//       qm.meta_value AS quiz_meta_value,
//       /* Homework Data */
//       h.ID AS hw_id,
//       h.post_title AS hw_title,
//       h.post_content AS hw_content,
//       hm.meta_key AS hw_meta_key,
//       hm.meta_value AS hw_meta_value,
//       /* Questions data (replace with your actual logic) */
//       -- Replace with your logic to get questions for quiz and homework
//     FROM {$wpdb->posts} AS l
//     LEFT JOIN {$wpdb->postmeta} AS lm ON l.ID = lm.post_id
//     LEFT JOIN {$wpdb->posts} AS q ON lm.meta_value = q.ID AND lm.meta_key = 'quiz_id'
//     LEFT JOIN {$wpdb->postmeta} AS qm ON q.ID = qm.post_id
//     LEFT JOIN {$wpdb->posts} AS h ON lm.meta_value = h.ID AND lm.meta_key = 'hw_id'
//     LEFT JOIN {$wpdb->postmeta} AS hm ON h.ID = hm.post_id
//     -- LEFT JOIN your_questions_table AS qn ON q.ID = qn.quiz_id OR h.ID = qn.homework_id (Modify based on your question table)
//     WHERE l.ID = %d AND l.post_type = 'lesson'
//     ORDER BY lesson_id ASC
//     LIMIT 1;";

//     $prepared_sql = $wpdb->prepare($sql, $lesson_id);
//     $result = $wpdb->get_row($prepared_sql);

//     // Process questions data (replace with your logic)
//     // if ($result) {
//     //   // Modify this section to get questions based on quiz_id and hw_id
//     //   // $result->questions = ... (your logic to get questions)
//     // }

//     return $result;
// }
