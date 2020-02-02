<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
//for excel library
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use exportHelper;

class QueryToExcel extends Controller
{

    public function preExcel(Request $request){

        $sql=$request->sql;
        $header_array=[
            [
                'text'=>'Name',
                'row'=>2
            ],
            [
                'text'=>'email',
                'row'=>2
            ],
            [
                'text'=>'Salary',
                'col'=>3,
                'sub'=>[
                    [
                        'text'=>'Basic'
                    ],
                    [
                        'text'=>'Home'
                    ],
                    [
                        'text'=>'Others'
                    ]
                ]
            ],
            [
                'text'=>'random',
                'col'=>3,
                'row'=>2,
                'sub'=>[
                    [
                        'text'=>'random1'
                    ],
                    [
                        'text'=>'random2',
                        'col'=>2
                    ]
                ]
            ],
            [
                'text'=>'what',
            ],
            [
                'text'=>'ever',
                'col'=>2,
            ],
            [
                'text'=>'Know',
                'row'=>2
            ]
        ];
        //$fileName=$this->generateReport($sql,$header_array);
      $fileName=exportExcel($sql,$header_array);
       // $fileName=apsysHelper->exportExcel($sql);
        return response()->json(['status' => 'success', 'file' => $fileName]);

    }

    private $header_fill=0;
    //public function generateReport($query_result,$header_array='',$pre_header_array='',$fileName=''){
      public function generateReport($allArr){
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        /*
         * style
         * */

        $style_bold = array(
            'font' => array('bold' => true
            )
        );
        $font_bold = array(
            'font' => array(
                'size' => 10,
                'bold' => true

            )

        );
        $font_size = array(
            'font' => array(
                'size' => 9
            )
        );
$merged_row=0;

//        if($sql_data == '') {
//            $query_result = DB::select($sql);
//        }
//        else{
//            $query_result = DB::select($sql,$sql_data);
//        }

       if(isset($allArr['header_color']) && $allArr['header_color']==0){
           $this->header_fill=0;
       }else{
           $this->header_fill=1;
       }

        if(isset($allArr['pre_header']) && $allArr['pre_header'] !=''){
            $col=1; $row =1;


            foreach ($allArr['pre_header'] as $header){
                if(isset($header['font-size'])){
                    $pre_header_font_size=array(
                      'font'=>array(
                       'size'=>$header['font-size'],
                       'bold'=>true
                      )
                    );
                    $gen_arr=$this->generateHeader($spreadsheet,$header,$col,$row,$pre_header_font_size);
                    $col=$gen_arr['col'];
                    $merged_row=$gen_arr['merged'];
                }else{
                    $gen_arr=$this->generateHeader($spreadsheet,$header,$col,$row);
                    $col=$gen_arr['col'];
                    $merged_row=$gen_arr['merged'];
                }


            }
        }

        if(!isset($allArr['header_array']) || $allArr['header_array']=='') {


            $heading_array = array();

            foreach ($allArr['data_array'] as $k) {
                foreach ($k as $key => $value) {
                    $heading_array[] = $key;

                }
                break;
            }


            $col = 1;
            if(!isset($allArr['pre_header']) || $allArr['pre_header']=='') {
                $row = $sheet->getHighestRow();
            }else {
                $row = $sheet->getHighestRow() + $merged_row;
            }


            foreach ($heading_array as $ha) {
                $sheet->setCellValueByColumnAndRow($col, $row, $ha);
                    $sheet->getStyleByColumnAndRow($col, $row)->applyFromArray($font_bold);
                if($this->header_fill==1 ) {
                    $sheet->getStyleByColumnAndRow($col, $row)
                        ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('cacaca');
                }
                $col++;
            }


            $row = $sheet->getHighestRow()+1;
            foreach ($allArr['data_array'] as $qa) {
                $col = 1;
                $qa=(array) $qa;
                foreach ($heading_array as $ha) {

                    $sheet->setCellValueByColumnAndRow($col, $row, $qa[$ha]);
                    $col++;
                }
                $row++;
            }

        }
        elseif(isset($allArr['header_array']) && $allArr['header_array'] !=''){
            $col=1;
            if(!isset($allArr['pre_header']) || $allArr['pre_header'] =='') {
                $row = $sheet->getHighestRow();
            }else {
                $row = $sheet->getHighestRow() + $merged_row+2;
            }


            foreach ($allArr['header_array'] as $header){
                $gen_arr=$this->generateHeader($spreadsheet,$header,$col,$row);
                $col=$gen_arr['col'];
                $merged_row=$gen_arr['merged'];

            }
            //return $merged_row;
            $sheet_highest_row = $sheet->getHighestRow();
            $row=$sheet_highest_row+1;

            $heading_array = array();

            foreach ($allArr['data_array'] as $k) {
                foreach ($k as $key => $value) {
                    $heading_array[] = $key;

                }
                break;
            }

            foreach ($allArr['data_array'] as $qa) {
                $col = 1;
                $qa=(array) $qa;
                foreach ($heading_array as $ha) {

                    $sheet->setCellValueByColumnAndRow($col, $row, $qa[$ha]);
                    $col++;
                }
                $row++;
            }

        }

        $minwidth=15;
        $sheet->getDefaultColumnDimension()
            ->setWidth($minwidth);

        $sheet_col_string_high = $sheet->getHighestColumn();
        $sheet_row_high = $sheet->getHighestRow();
        $sheet->getStyle("A1:{$sheet_col_string_high}{$sheet_row_high}")
            ->getAlignment()
            ->setHorizontal('center')
            ->setVertical('center')
            ->setWrapText(true);

        $sheet->getStyle("A1:{$sheet_col_string_high}{$sheet_row_high}")
            ->applyFromArray($font_size);

        if($allArr['file_name'] == ''){
            $fileName = 'Random_file.xlsx';
        }else{
            $fileName=$allArr['file_name'];
        }

        exportHelper::excelHeader($fileName, $spreadsheet);
        return $fileName;

    }
    public function generateHeader(Spreadsheet $spreadsheet,$arr,$col,$row,$font=''){
                /*====Style border=====*/
                $font_bold = array(
                    'font' => array(
                        'size' => 10,
                        'bold' => true

                    )

                );
                $merged_row=0;

                $sheet=$spreadsheet->getActiveSheet();
                if(isset($arr['text'])) {
                    $sheet->setCellValueByColumnAndRow($col, $row, $arr['text']);
                    if($font=='') {
                        $sheet->getStyleByColumnAndRow($col, $row)->applyFromArray($font_bold);
                        if($this->header_fill==1) {
                            $sheet->getStyleByColumnAndRow($col, $row)
                                ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('cacaca');
                        }
                    }else{
                        $sheet->getStyleByColumnAndRow($col, $row)->applyFromArray($font);
                        if($this->header_fill==1) {
                            $sheet->getStyleByColumnAndRow($col, $row)
                                ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('cacaca');
                        }
                    }

                }

                if(isset($arr['col']) && !isset($arr['row'])){
                    $sheet->mergeCellsByColumnAndRow($col,$row,$col+$arr['col']-1,$row);
                    $merged_row=0;
                }
                elseif (isset($arr['row']) && !isset($arr['col'])){
                    $sheet->mergeCellsByColumnAndRow($col,$row,$col,$row+$arr['row']-1);
                    $merged_row=$arr['row'];

                }
                elseif (isset($arr['row']) && isset($arr['col'])){
                    $sheet->mergeCellsByColumnAndRow($col,$row,$col+$arr['col']-1,$row+$arr['row']-1);
                    $merged_row=$arr['row'];
                }

                if(isset($arr['sub'])){
                    foreach ($arr['sub'] as $subArray) {
                        if(isset($arr['row'])) {
                            $gen_arr = $this->generateHeader($spreadsheet, $subArray, $col, $row + $arr['row']);
                            $col=$gen_arr['col'];
                            $merged_row=$gen_arr['merged'];
                        }else{
                            $gen_arr = $this->generateHeader($spreadsheet, $subArray, $col, $row + 1);
                            $col=$gen_arr['col'];
                            $merged_row=$gen_arr['merged'];
                        }
                    }
                }


                if(isset($arr['col']) && !isset($arr['sub'])){
                    return [
                        'col'=>$col+$arr['col'],
                        'merged'=>$merged_row
                    ];

                }
                elseif(isset($arr['col']) && isset($arr['sub'])){
                    return [
                        'col'=>$col,
                        'merged'=>$merged_row
                    ];
                }
                else{
                    return [
                        'col'=>$col+1,
                        'merged'=>$merged_row
                    ];
                }
    }
    public function testArray(){
        $array=[
            [
                'text'=>'Name',
                'row'=>2
            ],
            [
                'text'=>'email',
                'row'=>2
            ],
            [
                'text'=>'Salary',
                'col'=>3,
                'sub'=>[
                    [
                        'text'=>'Basic'
                    ],
                    [
                        'text'=>'Home'
                    ],
                    [
                        'text'=>'Others'
                    ]
                ]
            ]
        ];
    }
}
