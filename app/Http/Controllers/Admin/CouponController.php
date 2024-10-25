<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Coupon;
use App\Models\Program;
use App\Models\CouponUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $i = 1;
        if (!empty(array_intersect(adminRoles(), Auth::user()->role()))) {

            $coupons = Coupon::with(['coupon_users' => function ($query) {
                return $query->where('status', 1)->get();
            }])->orderBy('created_at', 'desc')->get();

            return view('dashboard.admin.coupons.index', compact('i', 'coupons'));
        } else  if (!empty(array_intersect(facilitatorRoles(), Auth::user()->role()))) {
            $coupons = Coupon::where('facilitator_id', Auth::user()->id)->orderBy('created_at', 'desc')->get();
            return view('dashboard.admin.coupons.index', compact('i', 'coupons'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!empty(array_intersect(adminRoles(), Auth::user()->role()))) {
            $programs =  Program::whereStatus(1)
                ->where('p_end', '>=', date('Y-m-d'))
                ->where('close_registration', 0)
                ->orderBy('created_at', 'DESC')
                // $programs = Program::mainActivePrograms()
                ->get();
        } else  if (!empty(array_intersect(facilitatorRoles(), Auth::user()->role()))) {
            $programs = DB::table('facilitator_trainings')->where(['user_id' => auth::user()->id, 'status' => 1])
                ->join('programs', 'programs.id', '=', 'facilitator_trainings.program_id')
                ->select('programs.id', 'programs.p_name', 'programs.p_amount', 'facilitator_trainings.created_at')
                ->orderBy('facilitator_trainings.created_at')
                ->get();
        } else {
            return abort(404);
        }

        return view('dashboard.admin.coupons.create', compact('programs'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $this->validate($request, [
            "program_id" => 'required',
            "code" => 'required',
            "amount" => 'required',
        ]);

        // run checks
        if (in_array('all', $data['program_id'])) {
            $programs = Program::with('coupon')->ActivePrograms()->get();
        } else {
            $programs = Program::with('coupon')->ActivePrograms()->whereIn('id', $data['program_id'])
                ->get();
        }

        // $program = Program::find($data['program_id']);
        foreach ($programs as $program) {
            $exists = $program->coupon->where('code', $data['code'])->first();

            if (!$exists) {
                if ($request->amount > $program->p_amount) {
                    return back()->with('error', 'Coupon amount cannot be more than training amount, please enter valid values');
                }

                if (!empty(array_intersect(facilitatorRoles(), Auth::user()->role()))) {
                    // Check that this facilitator can create the coupon
                    $maxAmt = isset($program->facilitator_percent) ? $program->facilitator_percent : 0;

                    if ($maxAmt > 0) {
                        $maxAmt = ($program->facilitator_percent / 100) * $program->p_amount;
                    }

                    if ($maxAmt == 0) {
                        return back()->with('error', 'You are not eligible to create a coupon for the selected training at the moment');
                    }

                    if ($request->amount > $maxAmt) {
                        return back()->with('error', 'You cannot add coupon of more than ' . $maxAmt . ' for the selected training');
                    }

                    $data['facilitator_id'] = Auth::user()->id;
                } else {
                    $data['facilitator_id'] = 0;
                }

                $in = [
                    "code" => $data['code'],
                    "amount" => $data['amount'],
                    "facilitator_id" => $data['facilitator_id'],
                    "program_id" => $program->id
                ];

                Coupon::create($in);
            }
        }

        return redirect(route('coupon.index'))->with('message', 'Coupon created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Coupon  $coupon
     * @return \Illuminate\Http\Response
     */
    public function show(Coupon $coupon)
    {
        $usages = CouponUser::with('program')->where('coupon_id', $coupon->id)->get();
        $i = 1;

        return view('dashboard.admin.coupons.usage', compact('i', 'usages', 'coupon'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Coupon  $coupon
     * @return \Illuminate\Http\Response
     */
    public function edit(Coupon $coupon)
    {
        if (!empty(array_intersect(adminRoles(), Auth::user()->role()))) {

            $programs = Program::select('id', 'p_name', 'p_amount')->where('id', '<>', 1)->where('status', 1)->orderBy('created_at', 'DESC')->get();
        } else  if (!empty(array_intersect(facilitatorRoles(), Auth::user()->role()))) {
            $programs = DB::table('facilitator_trainings')->where(['user_id' => auth::user()->id, 'status' => 1])
                ->join('programs', 'programs.id', '=', 'facilitator_trainings.program_id')
                ->select('programs.id', 'programs.p_name', 'programs.p_amount', 'facilitator_trainings.created_at')
                ->orderBy('facilitator_trainings.created_at')
                ->get();
        } else {
            return abort(404);
        }
        return view('dashboard.admin.coupons.edit', compact('coupon', 'programs'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Coupon  $coupon
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Coupon $coupon)
    {

        $data = $this->validate($request, [
            "program_id" => 'required',
            "code" => 'required',
            "amount" => 'required',
        ]);

        // run checks
        $program = Program::find($data['program_id']);

        if ($request->amount > $program->p_amount) {
            return back()->with('error', 'Coupon amount cannot be more than training amount, please enter valid values');
        }

        if (!empty(array_intersect(facilitatorRoles(), Auth::user()->role()))) {
            // Check that this facilitator can create the coupon
            $maxAmt = isset($program->facilitator_percent) ? $program->facilitator_percent : 0;

            if ($maxAmt > 0) {
                $maxAmt = ($program->facilitator_percent / 100) * $program->p_amount;
            }

            if ($maxAmt == 0) {
                return back()->with('error', 'You are not eligible to create a coupon for the selected training at the moment');
            }

            if ($request->amount > $maxAmt) {
                return back()->with('error', 'You cannot add copon of more than ' . $maxAmt . ' for the selected training');
            }

            $data['facilitator_id'] = Auth::user()->id;
        } else {
            $data['facilitator_id'] = 0;
        }


        $coupon->update($data);

        return redirect(route('coupon.index'))->with('message', 'Coupon updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Coupon  $coupon
     * @return \Illuminate\Http\Response
     */
    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return back()->with('message', 'Delete successful');
    }

    public function getCreatedBy($id)
    {
        $coupon = Coupon::find($id);

        if ($coupon->facilitator_id == 0) {
            $createdBy = 'Administrator';
        } else {
            $createdBy = User::where('id', $coupon->facilitator_id)->value('name');
        }
        return $createdBy;
    }
}
