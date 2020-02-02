<div class="table table-responsive">
    <table class="table table-striped table-bordered table-hover dataTable">
        <thead>
        <tr>
            <th width="1">SL</th>
            <th>Document Name</th>
            <th class="text-center">Action</th>
        </tr>
        </thead>
        <tbody id="supporting-files">
        @php
            $existing_attachment = getAttachmentInfo($reference,$reference_id);
        @endphp
        @if(!empty($existing_attachment))
            @php($i = 1)
            @foreach($existing_attachment as $i=>$document)
                <tr>
                    <td>{{($i+1)}}</td>
                    <td>
                        <a download style="text-decoration: none;" title="Download" href="{{asset('public/'.$document->document_path)}}">  {!! $document->document_name !!}</a>
                    </td>

                    <td align="center">
                      <?php
                      $file_type = isset(array_reverse(explode('.',$document->document_path))[0])?array_reverse(explode('.',$document->document_path))[0]:'';
                      $showable_type = array('jpg', 'jpeg', 'gif','pdf','png');?>
                      @if(in_array($file_type,$showable_type))
                      <a target="_blnk" style="text-decoration: none;" title="view" href="{{asset('public/'.$document->document_path)}}"><button class="btn btn-primary btn-xs"><i class="fa fa-eye"></i> </button></a>
                      <a download="{{asset('public/'.$document->document_path)}}" style="text-decoration: none;" title="Download" href="{{asset('public/'.$document->document_path)}}"><button class="btn btn-primary btn-xs"><i class="fa fa-download"></i> </button></a>
                      @else
<!--                        <a target="_blnk" style="text-decoration: none;" title="view" href="{{asset('public/'.$document->document_path)}}"><button class="btn btn-primary btn-xs"><i class="fa fa-eye"></i> </button></a>-->
                        <a download="{{asset('public/'.$document->document_path)}}" style="text-decoration: none;" title="View" href="{{asset('public/'.$document->document_path)}}"><button class="btn btn-primary btn-xs"><i class="fa fa-eye"></i> </button></a>
                        <a download="{{asset('public/'.$document->document_path)}}" style="text-decoration: none;" title="Download" href="{{asset('public/'.$document->document_path)}}"><button class="btn btn-primary btn-xs"><i class="fa fa-download"></i> </button></a>
                      @endif
                        
                    </td>
                </tr>
            @endforeach
            @php($i++)
        @else
            <td class="text-center" colspan="3">No Data Found!!!</td>
        @endif
        </tbody>
    </table>
</div>