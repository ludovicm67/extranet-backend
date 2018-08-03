<?php

namespace App\Http\Controllers;

use App\Contract;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use ludovicm67\SuperDate\Date;

/**
 * NOTE:
 * - doesn't support concurrent leaves; so they will be counted twice (or more)
 */

class PDFController extends Controller
{
  private $pdfName;
  private $startHour = 9;
  private $endHour = 18;

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

  private function getNbDays($year = null, $month = null,
    $contractStart = null, $contractEnd = null, $includeHours = false
  ) {
    if (is_null($year)) {
      $year = intval(date('Y'));
    }
    if (is_null($month)) {
      $month = intval(date('n'));
    }

    $start = new \DateTime($year . '-' . $month . '-01');
    $end = new \DateTime($start->format('Y-m-t') . ' 23:59:59');

    if (!is_null($contractStart)) {
      if (is_string($contractStart)) {
        $startDate = new \DateTime($contractStart);
      }
      if (is_object($contractStart)) {
        $startDate = $contractStart;
      }
      if ($startDate > $start) {
        $start = $startDate;
      }
    }

    if (!is_null($contractEnd) && !empty($contractEnd)) {
      if (is_string($contractEnd)) {
        $endDate = new \DateTime($contractEnd);
      }
      if (is_object($contractEnd)) {
        $endDate = $contractEnd;
      }
      if ($endDate < $end) {
        $end = $endDate;
      }
    }

    $d = new Date($start->format('Y-m-d'));
    $period = $d->allDaysTo($end->format('Y-m-d'));

    $workingDays = array_filter($period, function ($d) {
      return $d->isWeekDay() && !$d->isHoliday();
    });

    $nbDays = count($workingDays);
    if ($includeHours && $start->format('H') > $this->startHour) {
      $nbDays -= .5;
    }
    if ($includeHours && $end->format('H') < $this->endHour) {
      $nbDays -= .5;
    }

    return $nbDays;
  }

  // thing = congé, maladie, CDD, ...
  private function writeDates($thing,
    $month, $year, $from, $to = null,
    $onlyWhenNeeded = false, $includeHours = false
  ) {
    $content = $thing;

    // month period
    $start = new \DateTime($year . '-' . $month . '-01');
    $end = new \DateTime($start->format('Y-m-t') . ' 23:59:59');

    $startDate = new \DateTime($from);
    $startPeriod = '';
    $endPeriod = '';
    if ($includeHours && $startDate->format('H') > $this->startHour) {
      $startPeriod = ' après-midi';
    }
    $needBefore = (!$onlyWhenNeeded || $startDate > $start);
    if (!is_null($to)) {
      $endDate = new \DateTime($to);
      if ($includeHours && $endDate->format('H') < $this->endHour) {
        $endPeriod = ' midi';
      }
      $needAfter = (!$onlyWhenNeeded || $endDate < $end);
    } else {
      $needAfter = false;
    }

    if ($needBefore && $needAfter) {
      // start
      if ($startDate->format('Y') != $year) {
        $s = $startDate->format('d/m/Y');
      } else if ($startDate->format('n') != $month) {
        $s = $startDate->format('d/m');
      } else {
        $s = $startDate->format('j');
      }

      // end
      if ($endDate->format('Y') != $year) {
        $e = $endDate->format('d/m/Y');
      } else if ($endDate->format('n') != $month) {
        $e = $endDate->format('d/m');
      } else {
        $e = $endDate->format('j');
      }

      if ($s == $e && $startPeriod == $endPeriod) {
        $content .= ' le ' . $s . $startPeriod;
      } else if ($s == $e && empty($startPeriod) && !empty($endPeriod)) {
        $txtAfter = $endPeriod;
        if (trim($txtAfter) == 'midi') {
          $txtAfter = ' matin';
        }
        $content .= ' le ' . $s . $txtAfter;
      } else if ($s == $e && !empty($startPeriod) && empty($endPeriod)) {
        $content .= ' le ' . $s . $startPeriod;
      } else {
        $content .= ' du ' . $s . $startPeriod . ' au ' . $e . $endPeriod;
      }
    } else if ($needBefore && !$needAfter) {
      // start
      if ($startDate->format('Y') != $year) {
        $s = $startDate->format('d/m/Y');
      } else if ($startDate->format('n') != $month) {
        $s = $startDate->format('d/m');
      } else {
        $s = $startDate->format('j');
      }

      $content .= ' depuis le ' . $s . $startPeriod;
    } else if (!$needBefore && $needAfter) {
      // end
      if ($endDate->format('Y') != $year) {
        $e = $endDate->format('d/m/Y');
      } else if ($endDate->format('n') != $month) {
        $e = $endDate->format('d/m');
      } else {
        $e = $endDate->format('j');
      }

      $content .= " jusqu'au " . $e . $endPeriod;
    }

    return $content;
  }

  private function cleanLines($year, $month, $getLines) {
    $lines = [];

    foreach ($getLines as $line) {
      $line = json_decode(json_encode($line));
      if (empty($line->user)) continue;

      $contract = $this->writeDates($line->type, $month, $year, $line->start_at, $line->end_at, true, false);
      $details = '';

      $overtime = 0;
      $conges = 0;
      $maladie = 0;
      $autre = 0;
      $transports = 0;
      $expenses = 0;

      $alreadyDefined = false;

      // if an other contract was already defined for this user
      if (isset($lines[$line->user_id])) {
        $alreadyDefined = true;
        $contract = $lines[$line->user_id]->contract . ', ' . $contract;
        $details = $lines[$line->user_id]->details;
        $overtime = $lines[$line->user_id]->overtime;
        $conges = $lines[$line->user_id]->conges;
        $maladie = $lines[$line->user_id]->maladie;
        $autre = $lines[$line->user_id]->autre;
        $transports = $lines[$line->user_id]->transports;
        $expenses = $lines[$line->user_id]->expenses;
      }

      // if overtime, leaves and expenses are not already defined, define them!
      if (!$alreadyDefined) {
        // overtime: only one per user, that's why we only use index 0
        if (!empty($line->user->overtime)) {
          $overtime = $line->user->overtime[0]->volume;
        }

        if (!empty($line->user) && !empty($line->user->leave)) {
          $arr = array_reduce($line->user->leave, function ($a, $o) use ($year, $month) {
            $nbDays = $this->getNbDays($year, $month, $o->start, $o->end, true);
            switch (mb_strtolower($o->reason)) {
              case 'congé':
                $a['conges'] += $nbDays;
                return $a;
              case 'maladie':
                $a['maladie'] += $nbDays;
                return $a;
              case 'autre':
                $a['autre'] += $nbDays;
                return $a;
              default:
                return $a;
            }
          }, [
            'conges' => 0,
            'maladie' => 0,
            'autre' => 0,
          ]);

          $conges = $arr['conges'];
          $maladie = $arr['maladie'];
          $autre = $arr['autre'];
        }

        if (!empty($line->user) && !empty($line->user->expenses)) {
          $arr = array_reduce($line->user->expenses, function ($a, $o) {
            if (mb_strtolower($o->type) == 'transports') {
              $a['transports'] += $o->amount;
              return $a;
            } else {
              $a['expenses'] += $o->amount;
              return $a;
            }
          }, [
            'transports' => 0,
            'expenses' => 0,
          ]);

          $transports = $arr['transports'];
          $expenses = $arr['expenses'];
        }
      }

      // hello, dear intern! :-)
      if (mb_strtolower($line->type) == 'stage') {
        $nbDays = $this->getNbDays($year, $month, $line->start_at, $line->end_at, false);
        $details .= $nbDays . ' jours de présence (stage).';
      }

      // clean values
      if ($overtime < 0) $overtime = 0;

      // insert the line
      $lines[$line->user_id] = (object) [
        'user_id' => $line->user_id,
        'name' => $line->user->firstname . ' ' . $line->user->lastname,
        'contract' => $contract,
        'overtime' => $overtime,
        'conges' => $conges,
        'maladie' => $maladie,
        'autre' => $autre,
        'transports' => $transports,
        'expenses' => $expenses,
        'details' => $details,
      ];
    }

    return array_values($lines);
  }

  private function getLines($year, $month) {
    // month period
    $start = new \DateTime($year . '-' . $month . '-01');
    $end = new \DateTime($start->format('Y-m-t') . ' 23:59:59');

    $contracts = Contract::with([
      'user',
      'user.overtime' => function($query) use ($year, $month) {
        $query->where('month', $month)->where('year', $year);
      },
      'user.expenses' => function($query) use ($year, $month) {
        $query
          ->where('month', $month)
          ->where('year', $year)
          ->where('accepted', 1);
      },
      'user.leave' => function($query) use ($start, $end) {
        $query
          ->orderBy('start')
          ->where('start', '<=', $end->format('Y-m-d H:i:s'))
          ->where('end', '>=', $start->format('Y-m-d H:i:s'))
          ->where('accepted', 1);
      }
    ])->orderBy('start_at')
      ->where('start_at', '<=', $end->format('Y-m-d H:i:s'))
      ->where(function($query) use ($start) {
        $query
          ->where('end_at', '>=', $start->format('Y-m-d H:i:s'))
          ->orWhereNull('end_at');
      })->get();

    return $this->cleanLines($year, $month, $contracts);
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

    $lines = $this->getLines($year, $month);

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
