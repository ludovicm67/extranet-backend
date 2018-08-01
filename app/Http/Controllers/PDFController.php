<?php

namespace App\Http\Controllers;

use Dompdf\Dompdf;
use Illuminate\Http\Request;

class PDFController extends Controller
{
  private $pdfName;

  public function __construct() {
    $name = '\n\a\v\e\t\t\e\-';
    $appName = strtolower(config('app.name'));

    for ($i = 0; isset($appName[$i]); $i++) {
      $char = $appName[$i];
      if ($char >= 'a' && $char <= 'z') {
        $name .= '\\' . $char;
      }
    }

    $this->pdfName = $name . '-m-Y';
  }

  public function compta(Request $request) {

    // get month and year params
    $year = intval($request->input('year', date('Y')));
    $month = intval($request->input('month', date('n')));
    if ($month < 1) {
      $month = 1;
    } else if ($month > 12) {
      $month = 12;
    }
    if ($year < 1900 || $year > 2100) {
      $year = intval(date('Y'));
    }
    $dt = new \DateTime("$year-$month");

    $content = 'compta';

    $dompdf = new Dompdf();
    $dompdf->loadHtml($content);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    $dompdf->stream($dt->format($this->pdfName) . '.pdf', [
      'compress' => 1,
      'Attachment' => 0
    ]);
  }
}
