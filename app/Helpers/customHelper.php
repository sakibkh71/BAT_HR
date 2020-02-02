<?php

function MenualApprovalProcessView($fam_maint_requests_id = '')
{
  ?>
<div class="table-responsive">
  <table class="table table-bordered table-condensed" id="attach_table_view">
    <thead>
      <tr>
        <th class="text-center">SL</th>
        <th class="text-center">Employee</th> 
      </tr>
    </thead>
    <tbody>
      <?php
     // echo 'hi';exit();
      $rows =  getMenualAppovalProcess($fam_maint_requests_id);
     // print_r($rows);exit();
      // echo 'hi';exit();
      if ($rows->count() > 0) {
        $sl = 0;
        foreach ($rows as $row) {
          ?>
      <tr>
        <td class="text-center"><?php echo ++$sl; ?></td>
        <td class="text-left"><?php echo $row->username; ?></td>
        
      </tr>
      <?php
        }
      } else {
        ?>
      <tr>
        <td colspan="3">No employee found!</td>
      </tr>
      <?php
      }
      ?>
    </tbody>
  </table>
</div>
<?php
}

function  MenualApprovalProcess($multiple = false, $fam_maint_requests_id = ''){
  $mode = 'add';
    
  ?>
  <div class="table-responsive">
    <table class="table table-bordered  table-condensed" id="aproval_table">
      <thead>
        <tr>
          <th class="text-center">SL</th>
          <th class="text-center">Employee</th>         
          <!--<th class="text-center">Action</th>-->
          <th class="text-center">
            <?php
            if ($multiple) {
              ?>
            &nbsp;<button type="button" title="Add More" class="btn btn-primary btn-sm" id="approval_add_more"><i class="fa fa-plus"></i></button>
            <?php
            } ?>
          </th>
        </tr>
      </thead>
    <tbody>
      <?php
     // echo $fam_maint_requests_id;
      if (!empty($fam_maint_requests_id)) {
        //echo 'hieeee';
        $mode = 'edit';
        $rows = getMenualAppovalProcess($fam_maint_requests_id);
        //dd($rows);
      //  echo "<pre>" ;print_r($rows);exit();
        if ($rows->count() > 0) {
          $sl = 0;
          
          foreach ($rows as $row) {
            
        $sys_users_arr = array(
        'selected_value' => $row->id ?? old('id'),
        'attributes' => array(
        'class' => 'form-control',
        'id' => 'id'
        ),
        'name' => 'emp[]'
        );
         ?>
        <tr>
          <td class="text-center approval_sl"><?php echo ++$sl; ?></td>
          <td class="text-left"><?php echo   __combo("sys_users", $sys_users_arr) ?></td>
          <td class="text-center"><button type="button" title="Remove" class="btn btn-danger btn-sm approval_remove" ><i class="fa fa-remove"></i></button></td>
        </tr>
        <?php
          }
        }
      }
      ?>
    </tbody>
    </table>
  </div>
  <?php
if ($multiple) {
  ?>
<!--&nbsp;<button type="button" class="btn btn-primary" id="approval_add_more">Add More</button>-->
<?php
} ?>
 <?php
    $sys_users_arr = array(
    'selected_value' => '',
    'attributes' => array(
    'class' => 'form-control',
    'id' => 'id'
    ),
    'name' => 'emp[]'
    );
  ?>
          
<script type="text/javascript">
  jQuery(document).ready(function($){
    var tr = '<tr>'+
      "<td class='text-center approval_sl'>1</td>"+
      '<td class="text-center"> <?= __combo("sys_users", $sys_users_arr) ?> </td>'+
      '<td class="text-center"><button type="button" title="Remove" class="btn btn-danger  btn-sm approval_remove"><i class="fa fa-remove"></i></button></td>'+
    '</tr>';
 
    <?php
    if ($mode == 'add') {
      ?>
      $("#aproval_table tbody").append(tr);
    <?php
    }
    ?>
     resetApprovalSl();
    $("#approval_add_more").click(function(){
      $("#aproval_table tbody").append(tr);
      resetApprovalSl();
    });  
  });  
  $(document).on("click", ".approval_remove", function(){
    $(this).closest('tr').remove();
    resetAttachSl();
  });
  function resetApprovalSl(){
    $(".approval_sl").each(function(idx, elem) {
      $(elem).text(idx+1);
    });
  }
</script>
<?php }
?>
<?php
function getMenualAppovalProcess($fam_maint_requests_id = '')
{
      $rows = DB::table('fam_maint_requests') 
          ->select('delegation_manual_user')
          ->where('fam_maint_requests_id', $fam_maint_requests_id)
          ->first();
      $approvalperson_ids =  json_decode($rows->delegation_manual_user); 
      //$abc['abc'] = $approvalperson_ids;
      $new_daligation_array = array();
      $i = 0;
      //debug($approvalperson_ids, 1);
      if (is_array($approvalperson_ids) && !empty($approvalperson_ids)) {
      foreach($approvalperson_ids as $approvalperson_ids  ) 
      {
          $new_daligation_array[$i]=$approvalperson_ids;
          $i++;
      }
      }
    $approvalperson_names = DB::table('sys_users')
                          ->select('username',
                                  'id')
                          ->whereIn('id', $new_daligation_array)
                          ->get();
   $appovalperson_names['appovalperson_names'] = $approvalperson_names;
   return $approvalperson_names ;
}



