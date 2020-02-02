<?php
/**
 * Created by PhpStorm.
 * User: APSIS-M
 * Date: 5/28/2018
 * Time: 9:48 AM
 */

namespace App\Helpers;


class PdfHelper
{
    public static function exportPdf($view,$data)
    {

        $company_info =  request()->session()->get('COMPANY_INFO');
        $company_name = !empty($company_info["company_name"])?$company_info["company_name"]:getOptionValue("company_name");
        $company_address = !empty($company_info["address"])?$company_info["address"]:'';

         $config = [
            'mode' => 'BN',
            "autoScriptToLang" => true,
            "autoLangToFont" => true,
        ];
        $mpdf = new \Mpdf\Mpdf($config);
        $mpdf->SetTitle($data['report_title']);

        //$mpdf->useOddEven = 1;
        $mpdf->defHeaderByName(
            'myHeader', array (
                'L' => array (

                    'content'=>'<img class="img-responsive" style="visibility: hidden; width:10px; margin-bottom: 5px" src="'.getOptionValue("company_logo2").'" alt=""/>'
                 ),
                'R' => array (
                    'content'=>'<p style="width:200px">Print Date : '.date("d-M-Y").'</p>',
                    'font-size'=>8
                ),
                'C' => array (
                    'content' => '<h2 style="font-size: 16px; line-height: 20px; display:block;">'.$company_name.'</h2><br>
                                  <h5 style="font-size: 12px; display:block;">'.$company_address . '</h5><br>',
                    'font-style' => 'B',
                    'font-family' => 'serif',
                ),
                'line' => 1,
            )
        );

        $mpdf->DefFooterByName(
            'myFooter', array (
                'L' => array (
                    'content'=>'Developed by: <a target="_blank" href="http://apsissolutions.com">apsissolutions.com</a>',
                    'font-size'=>7,
                    'font-family' => 'serif',
                ),
                'R' => array (
                    'content'=>'<i>Page {PAGENO} of {nb}</i>',
                    'font-size'=>8
                ),
                'C' => array (
                    'content' => '<h5 style="text-align: center; font-size: 12px">© '.date('Y').' '.$company_name.'</h5>',
                    'font-style' => 'B',
                    'font-family' => 'serif',
                ),
                'line' => 1,
            )
        );

        $mpdf->AddPage(
            (isset($data['orientation'])?$data['orientation']:'L'),
            'NEXT-ODD',
            '',
            '',
            '',
            20,
            10,
            (isset($data['top_margin'])?$data['top_margin']:30),
            20,
            5,
            10,
            'myHeader',
            'myHeader',
            'myFooter',
            'myFooter2',
            1,
            1,
            1,
            1,
            0,
            (isset($data['paper_size'])?$data['paper_size']:'A4')
        );

//        $mpdf->SetHTMLFooter('<h5 style="text-align: center">© '.date('Y').' SR Chemical Limited</h5>');

        ini_set("pcre.backtrack_limit", "1000000000");
        ini_set('memory_limit','256M');
        $html = view($view,$data);
//        $stylesheet = 'public/css/pdf.css';
        $stylesheet = file_get_contents('public/css/pdf.css');
        $stylesheet = file_get_contents('public/css/bootstrap.min.css');
        $mpdf->WriteHTML($stylesheet,1);
        $mpdf->WriteHTML('<h4 style="text-align: center; text-decoration: underline; margin-bottom: 10px">'.(isset($data['report_title'])?$data['report_title']:'Title').'</h4>');
        $mpdf->WriteHTML($html);
        $filename = (isset($data['filename'])?$data['filename']:'pdf'). date("Y-m-d-H-i-s").'.pdf';
        if(isset($data['download']) && $data['download']==true){
            $mpdf->Output($filename, 'D'); //for download
        } else{
            $mpdf->Output($filename, "I"); // open in browser
        }
    }

}
