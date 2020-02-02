<h3>Configuration</h3>
<ul>
    <li><b><code>sys_search_panel</code></b> and <b><code>sys_search_panel_details</code></b> Tables are responsible for generate Dynamic Search Panel form where <code>`search_panel_slug`</code> is unique identity.</li>
    <li><b><code>sys_search_panel</code></b> Contain the basic form/input page configuration.
        <ul>
            <li><code>`sys_search_panel`</code> stands for search Panel Name.<br/></li>
            <li><code>`default_search_by`</code> stands for the comma separated value of <code>`sys_search_panel_details_id`</code> which means those fields are defaultly shown in implemented place.</li>
        </ul>
    </li>
    <li>
        Every row in <b><code>sys_search_panel_details</code></b> table is stands for the Entry/Edit inputs which are grouped by <code>`search_panel_slug`</code>. This are auto generated after the above command.
        <ul>
            <li>Unneccessary or Extra-required input can be removed/added manually here as row.</li>
            <li><code>`column_space`, </code> stands for the bootstrap grid-segment number (for input field size. eg. 3 | 5 | 12).</li>
            <li><code>`field_name`, </code> stands for the input name attribute <b>(must be same as target table column name)</b>.</li>
            <li><code>`input_name`</code> stands for Input name attribute.</li>
            <li><code>`input_id`</code> stands for Input ID attribute.</li>
            <li><code>`input_class`</code> stands for Input Class attribute.</li>
            <li><code>`label_name`</code> stands for Input Label.</li>
            <li><code>`label_class`</code> stands for Input Label Class.</li>
            <li><code>`placeholder`</code> stands for Input placeholder AND Checkbox/radio input Text.</li>
            <li><code>`input_type`</code> stands for which type of input is this (See the enum value).</li>
            <li><code>`default_value`</code> stands for the default value for the input.</li>
            <li><code>`single_compare`</code> stands for default 0 for (start, condition, end) || 1 for (start, condition) is applicable where <code>`input_type`</code> is <u>text_range</u> or <u>number_range</u></li>
            <li><code>`sorting`</code> stands for Input Sequence.</li>
            <li><code>`required`</code> stands for required or not.</li>
            <li><code>`dropdown_slug`</code> stands for which <b>dynamic dropdown</b> will be integrated here. If this field name act like a foreign key, automatically system will generate the dropdown function as well as dropdown_slug and place here.<br/></li>
            <li><code>`dropdown_options`</code> stands for COMMA seperated value which act like the dropdown value and options. In this case <code>`dropdown_slug`</code> need to be <b>NULL</b><br/></li>
            <li><code>`dropdown_view`</code> stands for this dropdown is dynamic regular dropdown or Grid system Dropdown.</li>
            <li>If <code>`dropdown_grid_name`</code> is <b>grid</b> place the DROPDOWN GRID SLUG NAME <b>(See Dropdown Documentation)</b>.</li>
        </ul>
    </li>
</ul>
<h3>Implementation</h3>
<pre class="line-numbers"><code class="language-php">// From Controller/Function
class CustomController extends Controller {
    public function RequestOperationFunction(Request $request){
        $posted_value = $request->all();
        //Process Request
        //Redirection OR RETURN as needed with $posted_value
    }
}
</code></pre>
<pre class="line-numbers"><code class="language-php">// For View Page
if(isset($posted_value))
    $searched_value = $posted_value
else
    $searched_value = []
&#123;!! __getCustomSearch('DROPDOWN-GRID-SLUG', $searched_value) !!&#125;
</code></pre>
<h3>Full Example</h3>
<pre class="line-numbers"><code class="language-html">&lt;!--------Use this inside a form--------->
&lt;form action=action_route>
    &lt;div class=row>
        &lt;!---------------------------------------------------->
        &#123;!! __getCustomSearch('DROPDOWN-GRID-SLUG', $POST_VALUE) !!&#125;
        &lt;!---------------------------------------------------->
        &lt;div class=col-md-3>
            &lt;div class=form-group>
                &lt;label class=form-label>Other Input&lt;/label>
                &lt;div class=input-group>
                    &lt;input type=text name=other_input class=form-control />
                &lt;/div>
            &lt;/div>
        &lt;/div>
    &lt;/div>
    &lt;button type=submit>SUBMIT&lt;/button>
&lt;/form>
</code></pre>
<div class="row"></div>