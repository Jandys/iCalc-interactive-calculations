<?php



function icalcPluginConfiguration()
{
    echo '<div class="notice notice-success is-dismissible"><p>Hello World!</p></div>';
}

function icalcConfigurableVariablesMenu($options)
{
    add_options_page(
        'My Configurable Variables Plugin',
        'Configurable Variables',
        'manage_options',
        'my-configurable-variables-plugin',
        'my_configurable_variables_options_page'
    );

    function my_configurable_variables_options_page()
    {
        $option1_value = get_option('my_configurable_variables_option1');
        $option2_value = get_option('my_configurable_variables_option2');

        return `<script>
    jQuery(document).ready(function() {
        jQuery('.collapsible').click(function() {
            jQuery(this).next().toggle();
        });
    });
</script>

<div class="collapsible">Option 1</div>
<div class="content">
    <input type="text" name="my_configurable_variables_option1" value="<?php echo esc_attr($option1_value); ?>">
</div>

<div class="collapsible">Option 2</div>
<div class="content">
    <input type="text" name="my_configurable_variables_option2" value="<?php echo esc_attr($option2_value); ?>">
</div>
`;

    }
    function my_configurable_variables_save_options() {
        update_option('my_configurable_variables_option1', $_POST['my_configurable_variables_option1']);
        update_option('my_configurable_variables_option2', $_POST['my_configurable_variables_option2']);
    }

}