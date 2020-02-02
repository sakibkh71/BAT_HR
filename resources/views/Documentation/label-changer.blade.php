<h3>Configuration</h3>
<ul>
	<li>First we need to input Module name with file name in <b><code>sys_modules</code></b> table.</li>
	<li>For each module separate file will be Create in <code>‘resources/lang/en’</code> folder.</li>
</ul>

<h3>Implementation of <code><b>__lang($key, $replace = [ ])</b></code></h3>

<ul><li><b><code>$key</code></b> value come from file.</li></ul>
<pre class="line-numbers"><code class="language-html">//old way
	&lt;h2>Purchase Requisition List&lt;/h2>

//New way using function
	&lt;h2>&#123;&#123; __lang('Purchase_Requisition_List')  &#125;&#125;&lt;/h2>
</code></pre>

<pre class="line-numbers"><code class="language-php">//Old way
	dropdown_grid(
		$slug = 'customer-dropdown-grid',
		$data = array( 'selected_value'=>'', 
		'addbuttonid'=>'selected-customer',  'attributes'=>array( 'class'=>'btn btn-success btn-xs', 'id'=>'customer_name',  'style'=>'padding:0 5px;'  ),
		'name'=>'<i class="fa fa-plus-circle"></i>Select Customer '
	) )

//New Way using function
	dropdown_grid(
	$slug = 'customer-dropdown-grid',
	…………………………...
	 'name'=>'&lt;i class="fa fa-plus-circle">&lt;/i>'.__lang(' Select_Customer').''
	) )

</code></pre>

<ul>
	<li>
		Here what we declare in <b><code>__lang(‘Select_Customer’)</code></b> function, It first find it in specific file for this value. If we are in Sales Module this function find value for the key <code> ‘Select_Customer’</code> In <code>resources/lang/en/Sales.php</code> file. Module wise file name already declare in <b><code>sys_modules</code></b> table . If it find value for <code>Select_Customer</code> it just print the value <code>“Sales Customer For Apsis”</code> .
But here  <b><code>__lang('Purchase_Requisition_List')</code></b> ... <code>'Purchase_Requisition_List'</code> Key  have no value SO the <code>__lang()</code> function <code>remove (‘_’)</code> and print <code>'Purchase Requisition List'</code>
</code>
	</li>
</ul>


<pre class="line-numbers"><code class="language-php">//PHP File Example: sales.php
	return [  
	     "Select_Customer" => 'Sales Customer For Apsis',
	     "welcome" => 'Welcome, :name',
	];

	//Passing variable in __lang($key, $replace = [ ]) function
	__lang('welcome',array('name'=>'Rashed'))

</code></pre>

<ul>
	<li>
	** Here <b><code>:NAME</code></b> is Case Sensitive .
		<ul>
			<li>
			:NAME Uppercase return uppercase (Ex: RASHED).
			</li>
			<li>
				 :name return camelcase (Ex: Rashed)
			</li>
		</ul>      
    </li>
</ul>

