<?php
function fileUploadHtml($multiple = false, $reference = '', $reference_id = '')
{
    $mode = 'add';
    ?>
    <div class="table-responsive">
        <table class="table table-bordered table-condensed" id="attach_table">
            <thead>
            <tr>
                <th class="text-center">SL</th>
                <th class="text-center">Attachment Tittle</th>
                <th class="text-center">File Name</th>
                <th width="1" class="text-center">
                    <?php
                    if ($multiple) {
                        ?>
                        <button title="Add More" onclick="append_attachment_row()" type="button" class="btn btn-primary btn-sm" id="attach_add_more"><i class="fa fa-plus"></i> </button>
                        <?php
                    }
                    ?>
                </th>
            </tr>
            </thead>
            <tbody>
            <?php
            if (!empty($reference) && !empty($reference_id)) {
                $mode = 'edit';
                $rows = getAttachments($reference, $reference_id);
                if ($rows->count() > 0) {
                    $sl = 0;
                    foreach ($rows as $row) {
                        ?>
                        <tr>
                            <td class="text-center attach_sl"><?php echo ++$sl; ?></td>
                            <td class="text-left"><?php echo $row->document_name; ?></td>
                            <td class="text-center"><a title="View" class="btn btn-info btn-xs" href="<?php echo url($row->document_path); ?>"><i class="fa fa-eye"></i></a></td>
                            <td class="text-center"><button type="button" class="btn btn-danger btn-sm attach_remove_ajax" attachments_id="<?php echo $row->attachments_id; ?>"><i class="fa fa-remove"></i></button></td>
                        </tr>
                        <?php
                    }
                }
            }else{
                ?>
                <tr>

                    <td class="text-center attach_sl">1</td>

                    <td class="text-center"><input type="text" name="attach_title[]" class="form-control"></td>

                    <td class="text-center"><input type="file" name="attach_file[]" class="form-control-file"></td>

                    <td class="text-center">
                        <button title="Remove" type="button" class="btn btn-danger btn-sm attach_remove"><i class="fa fa-remove"></i></button>
                    </td>

                </tr>
            <?php }
            ?>
            </tbody>
        </table>
    </div>



    <script type="text/javascript">
        // jQuery(document).ready(function($){

        function  append_attachment_row() {
            var attachTr = '<tr>\n\
      <td class="text-center attach_sl">1</td>\n\
      <td class="text-center"><input type="text" name="attach_title[]" class="form-control"></td>\n\
      <td class="text-center"><input type="file" name="attach_file[]" class="form-control-file"></td>\n\
      <td class="text-center"><button title="Remove" type="button" class="btn btn-danger btn-sm attach_remove"><i class="fa fa-remove"></i></button></td>\n\
    </tr>';

            $("#attach_table tbody").append(attachTr);
            resetAttachSl();
        }




        $(".attach_remove_ajax").click(function(){
            var $this = $(this);
            var attachmentsId = $this.attr('attachments_id');
            //alert(attachmentsId);
            swalConfirm('Are you sure?').then(function(r){
                if(r.value){
                    $.ajax({
                        type: "POST",
                        async: false,
                        dataType: 'JSON',
                        url: "<?php echo url('delete-attachments-ajax'); ?>",
                        data: {"_token": "<?php echo csrf_token(); ?>", attachments_id: attachmentsId},
                        success: function (data) {
                            //console.log(data);
                            if(data.result){
                                $this.closest('tr').remove();
                                resetAttachSl();
                            }
                        }
                    });
                }
            });
        });

        // });

        $(document).on("click", ".attach_remove", function(){
            $(this).closest('tr').remove();
            resetAttachSl();
        });

        function resetAttachSl(){
            $(".attach_sl").each(function(idx, elem) {
                $(elem).text(idx+1);
            });
        }
    </script>
    <?php
}

function fileUploadSave($request, $reference, $reference_id, $upload_dir = '')
{
    $table_name = 'attachments';
    if (empty($upload_dir)) {
        $upload_dir = "uploads/fams";
    }

    $upload_path = base_path($upload_dir);
    makeDirectory($upload_path);

    if($request->hasfile('attach_file')) {
        $data['reference'] = $reference;
        $data['reference_id'] = $reference_id;
        foreach($request->file('attach_file') as $key => $file) {
            $file_name = $reference . rand() . '.' . $file->getClientOriginalExtension();
            $file->move($upload_path, $file_name);
            $file_path = "$upload_dir/$file_name";
            $data['document_name'] = $request->attach_title[$key];
            $data['document_path'] = $file_path;
            $data['created_by'] = Auth::id();
            $data['created_at'] = mySqlDateTime();
            DB::table($table_name)->insert($data);
            $attachments_id = DB::getPdo()->lastInsertId();
        }
    }
}

function fileUploadView($reference, $reference_id)
{
    ?>
    <div class="table-responsive">
        <table class="table table-bordered table-condensed" id="attach_table_view">
            <thead>
            <tr>
                <th class="text-center">SL</th>
                <th class="text-center">Attachment Tittle</th>
                <th class="text-center">Action</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $rows = getAttachments($reference, $reference_id);
            if ($rows->count() > 0) {
                foreach ($rows as $sl=>$row) {
                    ?>
                    <tr>
                        <td class="text-center"><?php echo $sl+1?></td>
                        <td class="text-left"><?php echo $row->document_name; ?></td>
                        <td class="text-center">
                            <a download title="Download" class="btn btn-primary btn-xs" href="<?php echo url($row->document_path); ?>"><i class="fa fa-download"></i></a>
                            <a title="View" target="_blank" class="btn btn-info btn-xs" href="<?php echo url($row->document_path); ?>"><i class="fa fa-eye"></i></a>
                        </td>
                    </tr>
                    <?php
                }
            } else {
                ?>
                <tr>
                    <td colspan="3">No attachments found!</td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </div>
    <?php
}

function getAttachments($reference, $reference_id)
{
    $rows = DB::table('attachments')
        ->where('reference', $reference)
        ->where('reference_id', $reference_id)
        ->get();
    return $rows;
}

?>