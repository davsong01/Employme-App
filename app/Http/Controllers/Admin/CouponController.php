<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Group;
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
        if (!checkRoleHas(['Admin', 'Facilitator', 'Grader'])) {
            return route('home');
        }

        $coupons = Coupon::with(['coupon_users' => function ($query) {
            return $query->where('status', 1)->get();
        }])->orderBy('created_at', 'desc')->get();

        return view('dashboard.admin.coupons.index', compact('i', 'coupons'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(checkRoleHas(['Admin'])) {
            $programs = Program::mainActivePrograms()->get();
            $groups =  Group::isActive()->get();
        } else  if(checkRoleHas(['Facilitator'])) {
            $programs = DB::table('facilitator_trainings')->where(['user_id' => resolveAuthUser()->id, 'status' => 1])
                ->join('programs', 'programs.id', '=', 'facilitator_trainings.program_id')
                ->select('programs.id', 'programs.p_name', 'programs.p_amount', 'facilitator_trainings.created_at')
                ->orderBy('facilitator_trainings.created_at')
                ->get();
        } else {
            return abort(404);
        }

        return view('dashboard.admin.coupons.create', compact('programs', 'groups'));
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
        if (checkRoleHas(['Admin'])) {
            $programs = Program::mainActivePrograms()->get();
            $groups =  Group::isActive()->get();
        } else  if (checkRoleHas(['Facilitator'])) {
            $programs = DB::table('facilitator_trainings')->where(['user_id' => resolveAuthUser()->id, 'status' => 1])
                ->join('programs', 'programs.id', '=', 'facilitator_trainings.program_id')
                ->select('programs.id', 'programs.p_name', 'programs.p_amount', 'facilitator_trainings.created_at')
                ->orderBy('facilitator_trainings.created_at')
                ->get();
        } else {
            return abort(404);
        }

        return view('dashboard.admin.coupons.create', compact('programs', 'groups','coupon'));
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
            "training_type" => 'required|in:program,group',
            "program_ids"   => 'nullable|array',
            "group_ids"     => 'nullable|array',
            "code"          => 'required|string|max:255',
            "type"          => 'required|in:fixed,percentage',
            "amount"        => 'required|numeric|min:0',
        ]);

        $data['program_ids'] = $data['program_ids'] ?? [];
        $data['group_ids']   = $data['group_ids'] ?? [];

        if ($data['training_type'] === 'program') {
            $items = in_array('all', $data['program_ids'])
                ? Program::with('coupon')->ActivePrograms()->get()
                : Program::with('coupon')->ActivePrograms()
                ->whereIn('id', $data['program_ids'])->get();

            foreach ($items as $program) {
                $this->saveCouponForItem($program, $data, 'program_id', $coupon);
            }
        }

        if ($data['training_type'] === 'group') {
            $items = in_array('all', $data['group_ids'])
                ? Group::with('coupon')->get()
                : Group::with('coupon')->whereIn('id', $data['group_ids'])->get();

            foreach ($items as $group) {
                $this->saveCouponForItem($group, $data, 'group_id', $coupon);
            }
        }

        return redirect(route('coupon.index'))
            ->with('message', 'Coupon updated successfully');
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
            "training_type" => 'required|in:program,group',
            "program_ids"   => 'nullable|array',
            "group_ids"     => 'nullable|array',
            "code"          => 'required|string|max:255',
            "type"          => 'required|in:fixed,percentage',
            "amount"        => 'required|numeric|min:0',
        ]);

        $data['program_ids'] = $data['program_ids'] ?? [];
        $data['group_ids']   = $data['group_ids'] ?? [];

        if ($data['training_type'] === 'program') {
            // Handle Programs
            if (in_array('all', $data['program_ids'])) {
                $items = Program::with('coupon')->ActivePrograms()->get();
            } else {
                $items = Program::with('coupon')
                    ->ActivePrograms()
                    ->whereIn('id', $data['program_ids'])
                    ->get();
            }

            foreach ($items as $program) {
                $this->saveCouponForItem($program, $data, 'program_id');
            }
        }

        if ($data['training_type'] === 'group') {
            if (in_array('all', $data['group_ids'])) {
                $items = Group::with('coupon')->get();
            } else {
                $items = Group::with('coupon')
                    ->whereIn('id', $data['group_ids'])
                    ->get();
            }

            foreach ($items as $group) {
                $this->saveCouponForItem($group, $data, 'group_id');
            }
        }

        return redirect(route('coupon.index'))
            ->with('message', 'Coupon created successfully');
    }

    /**
     * Shared logic for creating a coupon for either Program or Group
     */
    protected function saveCouponForItem($item, $data, $foreignKey, $coupon = null)
    {
        // If creating, prevent duplicate coupon with same code
        if (!$coupon) {
            $exists = $item->coupon->where('code', $data['code'])->first();
            if ($exists) {
                return;
            }
        }

        // Validation: coupon amount must not exceed training/package amount
        if ($data['amount'] > $item->p_amount) {
            back()->with('error', 'Coupon amount cannot be more than ' . $item->p_amount . ', please enter valid values')->throwResponse();
        }

        // Facilitator rules
        if (checkRoleHas(['Facilitator'])) {
            $maxAmt = isset($item->facilitator_percent) ? $item->facilitator_percent : 0;

            if ($maxAmt > 0) {
                $maxAmt = ($item->facilitator_percent / 100) * $item->p_amount;
            }

            if ($maxAmt == 0) {
                back()->with('error', 'You are not eligible to create/update a coupon for the selected training/package at the moment')->throwResponse();
            }

            if ($data['amount'] > $maxAmt) {
                back()->with('error', 'You cannot add coupon of more than ' . $maxAmt . ' for the selected training/package')->throwResponse();
            }

            $data['facilitator_id'] = resolveAuthUser()->id;
        } else {
            $data['facilitator_id'] = 0;
        }

        // Reset both program_id and group_id to ensure exclusivity
        $couponData = [
            "type"           => $data['type'],
            "code"           => $data['code'],
            "amount"         => $data['amount'],
            "facilitator_id" => $data['facilitator_id'],
            "program_id"     => null,
            "group_id"       => null,
            $foreignKey      => $item->id,
        ];

        if ($coupon) {
            // Update existing coupon
            $coupon->update($couponData);
        } else {
            // Create new coupon
            Coupon::create($couponData);
        }
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

    public function fetchCoupon(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'source' => 'required|in:program,group',
        ]);

        if ($request->source === 'program') {
            $coupons = Coupon::where('program_id', $request->id)->get(['id', 'code', 'type', 'amount']);
        } else {
            $coupons = Coupon::where('group_id', $request->id)->get(['id', 'code', 'type', 'amount']);
        }

        return response()->json([
            'coupons' => $coupons
        ]);
    }
}
