<h3>Configuration</h3>
<ul>
    <li><b><code>sys_dropdowns</code></b> Table is containing the dropdown information where <code>`dropdown_slug`</code> is unique identity.</li>
    <li><code>`dropdown_mode`</code> stands for <b>dropdown - </b>for regular dropdown combobox (default) && <b>dropdown_grid - </b> for dropdown grid view</li>
    <li>If <code>`dropdown_mode`</code> is <b>dropdown_grid</b>, then <code>`sys_search_panel_slug`</code> have the <b>sys_search_panel_slug</b> value otherwise it can be NULL. <i>see search panel doc</i></li>
    <li><code>`sqltext`</code> stands for the mysql raw query which have 2 columns (as value & option)</li>
    <li><code>`value_field`</code> stands for as <b>value</b> column. Must be <code>TABLE-NAME<b>.</b>FIELD-NAME</code></li>
    <li><code>`option_field`</code> stands for as <b>option</b> column (may use aggregate selection). Must be <code>TABLE-NAME<b>.</b>FIELD-NAME</code></li>
    <li><code>`multiple`</code> 1 = multiple && 2 = single</li>
    <li><code>`dropdown_name`</code> stands for dropdown input name (for multiple dropdown system automatically rename it appending '[]' after it.)</li>
</ul>
<h3>Implementation</h3>
<pre class="line-numbers"><code class="language-php">$data_param = array(
    'selected_value' => // string or array,
    'name' => // string will override the default DB value,
    'attributes' => Array(
        'class' => // String will override default DB value,
        'id' => // String will override default DB value,
        'data-others' => // As required,
        //-----etc---
        ),
    'multiple' => // 0 | 1,
    'sql_query' => // String will override default DB value
    );
echo __combo('dropdown_slug_name', $data_param);
</code></pre>
<div class="row"></div>
