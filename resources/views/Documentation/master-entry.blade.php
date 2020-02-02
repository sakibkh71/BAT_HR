<h3>Master Entry Generator</h3>
<p>Make a command to generate modules for a <b>table</b></p>
<pre>php artisan apsis:moduleMaker MODULE_NAME</pre>
<ul>
    <li><code>MODULE_NAME</code> IS the same name of <code>TABLE_NAME (without `s`)</code></li>
    <li>There will be generated a GRID with Entry and Edit Feature with the link <code>HOST_NAME / grid / MODULE_NAME</code></li>
</ul>
<h3>Master Grid Manager</h3>
<ul>
    <li><b><code>sys_master_grid</code></b> Table is containing the Grid information where <code>`sys_master_grid_name`</code> is unique identity.
        <ul>
            <li><code>`grid_title`</code> stands for Grid Heading.<br/></li>
            <li><code>`grid_sql`</code> stands for raw query which fetch the grid data. <b>SELECTED FIELDS</b> will be table column respectively.<br/></li>
            <li><code>`action_table`</code> stands for the main <b>table name</b> with respect of <code>`grid_sql`</code> data<br/></li>
            <li><code>`primary_key_field`</code> stands for the primary key with respect of <code>`grid_sql`</code> data<br/></li>
            <li><code>`primary_key_hide`</code> 1 = Hide <code>`primary_key_field`</code> from the grid view and vice versa.<br/></li>
            <li><code>`enable_form`</code> stands for the Entry/Edit button will appear(1) or not(0).<br/></li>
            <li><s><code>`include_search_panel`</code> stands for Dynamic Search Panel is tagged <b>(custom_search_slug)</b> or not tagged <b>(NULL)</b>.</s></li>
        </ul>
    </li>
    <li> <code>`sys_master_entry_name`</code> is the related Master Entry/Edit which will be automatically connected.<br/>
        <ul>
            <li>If <code>`sys_master_entry_name`</code> will be <b>NULL</b> for custom Entry/Edit and then <code>`master_entry_url`</code> contain the ROUTE NAME. <br/></li>
            <li>This Custom Entry function should have the PRIMARY_ID as a first parameter to Editing function <br/></li>
        </ul>
        <pre class="line-numbers"><code class="language-php">Route::get('custom-entry/{primary_id?}', 'CustomController@customFunction');</code></pre>
<pre class="line-numbers"><code class="language-php">class CustomController extends Controller {
public function customFunction($id){
    //Entry or Edit Page Data operation
    return view('custom-view');
}
public function RequestOperationFunction(Request $request){
    //Process Request
    //Redirection OR RETURN as needed
}
}
</code></pre>

    </li>
</ul>
<h3>Master Entry/Edit Manager</h3>
<ul>
    <li><b><code>sys_master_entry</code></b> and <b><code>sys_master_entry_details</code></b> Tables are responsible for Entry/Edit where <code>`sys_master_entry_name`</code> is unique identity.</li>
    <li><b><code>sys_master_entry</code></b> Contain the basic form/input page configuration.
        <ul>
            <li><code>`master_entry_title`</code> stands for Form Heading.<br/></li>
            <li><code>`form_action_mode`</code> stands for how the form submit (Default Ajax).<br/></li>
            <li><code>`form_save_mode`</code> form will save on submit (default) or on every input (instant).<br/></li>
            <li><code>`form_action`</code> will be filled up with the route name which stands for custom operation with the data.<br/></li>
            <li><code>`form_class`</code> is by default integrated with bootstrap validator by the class <code>.validator</code>.<br/></li>
        </ul>
    </li>
    <li>
        Every row in <b><code>sys_master_entry_details</code></b> table is stands for the Entry/Edit inputs which are grouped by <code>`sys_master_entry_name`</code>. This are auto generated after the above command.
        <ul>
            <li>Unneccessary or Extra-required input can be removed/added manually here as row.<br/></li>
            <li><code>`table_name`, </code> stands for this input data will go to which table...<br/></li>
            <li><code>`field_name`, </code> stands for the input name attribute <b>(must be same as target table column name)</b>.<br/></li>
            <li><code>`sorting`</code> stands for Input Sequence.<br/></li>
            <li><code>`label_name`</code> stands for Input Label.<br/></li>
            <li><code>`label_class`</code> stands for Input Label Class.<br/></li>
            <li><code>`placeholder`</code> stands for Input placeholder AND Checkbox/radio input Text.<br/></li>
            <li><code>`input_type`</code> stands for which type of input is this (See the enum value).<br/></li>
            <li><code>`sorting`</code> stands for Input Sequence.<br/></li>
            <li><code>`input_id`</code> stands for Input ID attribute.<br/></li>
            <li><code>`input_class`</code> stands for Input Class attribute.<br/></li>
            <li><code>`required`</code> stands for required or not.<br/></li>
            <li><code>`dropdown_slug`</code> stands for which <b>dynamic dropdown</b> will be integrated here. If this field name act like a foreign key, automatically system will generate the dropdown function as well as dropdown_slug and place here.<br/></li>
            <li><code>`dropdown_options`</code> stands for COMMA seperated value which act like the dropdown value and options. In this case <code>`dropdown_slug`</code> need to be <b>NULL</b><br/></li>
            <li><code>`dropdown_view`</code> stands for this dropdown is dynamic regular dropdown or Grid system Dropdown.<br/></li>
            <li>If <code>`dropdown_grid_name`</code> is <b>grid</b> place the DROPDOWN GRID SLUG NAME <b>(See Dropdown Documentation)</b></li>
        </ul>
    </li>
</ul>
<div class="row"></div>
