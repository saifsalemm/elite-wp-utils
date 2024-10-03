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

function my_theme_enqueue_react_assets()
{
    // Enqueue the built JavaScript file
    wp_enqueue_script('my-react-app', get_template_directory_uri() . '/new-utils/earnings-dashboard/earnings-dashboard-react/dist/index.js', array(), '1.0.0', true);

    // Enqueue the built CSS file
    wp_enqueue_style('my-react-app-style', get_template_directory_uri() . '/new-utils/earnings-dashboard/earnings-dashboard-react/dist/index.css', array(), '1.0.0');
}
add_action('admin_enqueue_scripts', 'my_theme_enqueue_react_assets');

// Output the content of the custom admin page
function revenue_admin_page_content()
{
?>
    <div id="revenue-root"></div>
<?php
}
