<?php

namespace App\Http\Controllers;

use Storage;
use Validator;
use App\Leave;
use Illuminate\Http\Request;
use ludovicm67\SuperDate\Date;
use Eluceo\iCal\Component\Calendar;
use Eluceo\iCal\Component\Event;

class LeaveController extends Controller
{
    private function calcNbDays($start, $end) {
      if (empty($start) || empty($end)) {
        return 0;
      }
      $days = (new Date($start))->allDaysTo($end);
      $workingDays = array_filter($days, function ($d) {
        return !$d->isHoliday() && !$d->isWeekend();
      });
      if (empty($workingDays)) {
        return 0;
      }
      return count($workingDays);
    }

    private function deleteFile($file) {
      if (empty($file)) {
        return;
      }

      Storage::delete('/public/' . $file);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      return response()->json([
        'success' => true,
        'data' => Leave::with('user')->get(),
      ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $this->needPermission('leave', 'add');
      $validator = Validator::make($request->all(), [
        'reason' => 'required|string',
        'file' => 'nullable|file',
        'start' => 'required|string',
        'start_time' => 'required|integer|min:0|max:23',
        'end' => 'required|string',
        'end_time' => 'required|integer|min:0|max:23',
      ]);

      if ($validator->fails()) {
        return response()->json([
          'success' => false,
          'errors' => $validator->errors()->all(),
        ], 400);
      }

      $file = $request->file('file');
      if (!empty($file)) {
        $file = str_replace('public/', '', $file->store('public/leave/' . date('Y') . '/' . date('n')));
      }

      $startDate = date(
        'Y-m-d H:i:s',
        strtotime($request->start . ' ' . $request->start_time . ':00:00')
      );
      $endDate = date(
        'Y-m-d H:i:s',
        strtotime($request->end . ' ' . $request->end_time . ':00:00')
      );

      if ($endDate < $startDate) {
        return response()->json([
          'success' => false,
          'messsage' => 'end date cannot be before start date',
        ], 400);
      }

      $days = $this->calcNbDays($request->start, $request->end);
      if ($request->start_time > 9) {
        $days -= .5;
      }
      if ($request->end_time < 18) {
        $days -= .5;
      }
      if ($days < 0) {
        $days = 0;
      }

      Leave::create([
        'user_id' => auth()->user()->id,
        'accepted' => 0,
        'file' => $file,
        'details' => $request->details,
        'start' => $startDate,
        'start_time' => $request->start_time,
        'end' => $endDate,
        'end_time' => $request->end_time,
        'reason' => $request->reason,
        'days' => $days,
      ]);

      return response()->json([
        'success' => true,
      ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function show(Leave $leave)
    {
      return response()->json([
        'success' => true,
        'data' => $leave->fresh(['user']),
      ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Leave $leave)
    {
      $validator = Validator::make($request->all(), [
        'reason' => 'required|string',
        'file' => 'nullable|file',
        'start' => 'required|string',
        'start_time' => 'required|integer|min:0|max:23',
        'end' => 'required|string',
        'end_time' => 'required|integer|min:0|max:23',
      ]);

      if ($validator->fails()) {
        return response()->json([
          'success' => false,
          'errors' => $validator->errors()->all(),
        ], 400);
      }

      if ($request->delete_file == 1) {
        $this->deleteFile($leave->file);
        $leave->file = null;
      }

      $file = $request->file('file');
      if (!empty($file)) {
        $this->deleteFile($leave->file);
        $file = str_replace('public/', '', $file->store('public/leave/' . date('Y') . '/' . date('n')));
      } else {
        $file = $leave->file;
      }

      $startDate = date(
        'Y-m-d H:i:s',
        strtotime($request->start . ' ' . $request->start_time . ':00:00')
      );
      $endDate = date(
        'Y-m-d H:i:s',
        strtotime($request->end . ' ' . $request->end_time . ':00:00')
      );

      if ($endDate < $startDate) {
        return response()->json([
          'success' => false,
          'messsage' => 'end date cannot be before start date',
        ], 400);
      }

      $days = $this->calcNbDays($startDate, $endDate);
      if ($request->start_time > 9) {
        $days -= .5;
      }
      if ($request->end_time < 18) {
        $days -= .5;
      }
      if ($days < 0) {
        $days = 0;
      }

      $leave->update([
        'file' => $file,
        'details' => $request->details,
        'start' => $startDate,
        'start_time' => $request->start_time,
        'end' => $endDate,
        'end_time' => $request->end_time,
        'reason' => $request->reason,
        'days' => $days,
      ]);

      return response()->json([
        'success' => true,
      ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function destroy(Leave $leave)
    {
      $this->needPermission('leave', 'delete');
      $this->deleteFile($leave->file);
      $leave->delete();

      return response()->json([
        'success' => true,
      ]);
    }

    public function accept(Leave $leave) {
      $this->needPermission('request_management', 'edit');

      $leave->update([
        'accepted' => 1,
      ]);

      return response()->json([
        'success' => true,
      ]);
    }

    public function reject(Leave $leave) {
      $this->needPermission('request_management', 'edit');

      $leave->update([
        'accepted' => -1,
      ]);

      return response()->json([
        'success' => true,
      ]);
    }

    public function ics() {
      $leaves = Leave::with([
        'user',
      ])->where('accepted', '>=', 0)->get();

      $calName = config('app.name');
      $vCalendar = new Calendar($calName);
      $vCalendar->setName($calName);
      $vCalendar->setPublishedTTL('PT1H');

      foreach ($leaves as $leave) {
        $flags = [];
        if ($leave->accepted != 1) {
          $flags[] = '?';
        }
        $flags[] = mb_strtoupper($leave->reason);
        if (empty($flags)) {
          $flags = '';
        } else {
          $flags = '[' . implode('][', $flags) . '] ';
        }

        $vEvent = (new Event())
          ->setDtStart(new \DateTime($leave->start))
          ->setDtEnd(new \DateTime($leave->end))
          ->setSummary($flags . $leave->user->firstname . ' ' . $leave->user->lastname);
        $vCalendar->addComponent($vEvent);
      }

      return response($vCalendar->render())->withHeaders([
        'Content-Type' => 'text/calendar; charset=utf-8',
        'Content-Disposition' => 'attachment; filename="cal.ics"',
      ]);
    }
}
