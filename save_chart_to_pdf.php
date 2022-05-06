<?php

if (isset($_GET['id'])) {
    $id = $_GET['id'];
}
if (isset($_GET['for'])) {
    $for = $_GET['for'];
}
if (isset($_GET['admin'])) {
    $admin = $_GET['admin'];
}
if (isset($_GET['tid'])) {
    $tid = $_GET['tid'];
}

if (isset($_POST['htmlContent']) && $_POST['htmlContent'] != '')
{
    
    require_once __DIR__ . '/vendor/autoload.php';

    $defaultConfig = (new Mpdf\Config\ConfigVariables())->getDefaults();
    $fontDirs = $defaultConfig['fontDir'];

    $defaultFontConfig = (new Mpdf\Config\FontVariables())->getDefaults();
    $fontData = $defaultFontConfig['fontdata'];

    $mpdf = new \Mpdf\Mpdf([
        'fontDir' => array_merge($fontDirs, [
            __DIR__ . '/tmp',
        ]),
        'fontdata' => $fontData + [
            'sarabun' => [
                'R' => 'THSarabunNew.ttf',
                'I' => 'THSarabunNew_Italic.ttf',
                'B' => 'THSarabunNew_Bold.ttf',
                'BI'=> 'THSarabunNew_BoldItalic.ttf'
            ]
        ],
        'default_font' => 'sarabun',
        'pagenumPrefix' => 'Page ',
        'nbpgPrefix' => ' / '
    ]);

    $html = $_POST['htmlContent'];
    $mpdf -> setFooter('{PAGENO}{nbpg}');
    $mpdf -> WriteHTML($html);   

    if (isset($_GET['for'])) {
        $mpdf -> Output("TeacherReport.pdf");
        header ("location: assessment_instructor_teacher.php?id=".$id);
    } elseif (isset($_GET['admin'])) {
        $mpdf -> Output("AdminScoreReport.pdf");
        header ("location: assessment_subject_admin.php?id=".$id);
    } elseif (isset($_GET['tid'])) {
        $mpdf -> Output("AdminTeacherReport.pdf");
        header ("location: assessment_instructor_individual.php?id=".$id."&tid=".$tid);
    } else {
        $mpdf -> Output("ScoreReport.pdf");
        header ("location: assessment_subject_teacher.php?id=".$id);
    }

}

?>