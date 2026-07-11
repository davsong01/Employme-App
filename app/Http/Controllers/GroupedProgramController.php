<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Program;
use App\Models\Currency;
use App\Models\Settings;
use App\Models\GroupProgram;
use Illuminate\Http\Request;

class GroupedProgramController extends Controller
{
    public function index()
    {
        if (checkRoleHas(['Admin', 'Grader', 'Facilitator'])) {
            $groups = Group::with(['programs'])->latest()->get();
            $allActivePrograms = Program::mainActiveProgramsWithIsClosed()->get();
            $allPrograms = Program::allMainPrograms()->get();
            $currencies = Currency::select('id', 'name')->where('status',1)->get();
            $currency_symbol = Settings::first()->CURR_ABBREVIATION;

            return view('dashboard.admin.groupedprogram.index', compact('groups', 'currency_symbol', 'allPrograms', 'currencies', 'allActivePrograms'));
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'p_name'            => 'required|string|max:255',
            'p_abbr'            => 'required|string|max:50',
            'programs'          => 'required|array|min:1',
            'programs.*'        => 'exists:programs,id',
            'p_amount'          => 'required|numeric|min:0',
            'e_amount'          => 'nullable|numeric|min:0',
            'p_start'           => 'nullable|date',
            'p_end'             => 'nullable|date|after_or_equal:p_start',
            'status'            => 'required|boolean',
            'early_bird_status' => 'required|boolean',
            'currencies'        => 'sometimes|array|min:1',
            'currencies.*'      => 'exists:currencies,id',
            'currency_values'   => 'sometimes|array',
            'haspartpayment'    => 'sometimes',
        ]);

        if ($request->file('image')) {
            $file = $this->uploadFileToUploads($request->file('image'), 'image', 'trainings', 533, 533);
            $validated['image'] = 'trainingimage/' . $file;
        }

        if(!empty($validated['currencies'])){
            $currencyData = collect($validated['currencies'])->map(function ($id) use ($validated) {
                $value = $validated['currency_values'][$id] ?? null;
    
                return [
                    'id'     => (int) $id,
                    'amount' => $value !== '' ? (float) $value : null,
                ];
            })->toArray();
        }
        
        $group = Group::create([
            'p_name'            => $validated['p_name'],
            'p_abbr'            => $validated['p_abbr'],
            'p_amount'          => $validated['p_amount'],
            'e_amount'          => $validated['e_amount'] ?? null,
            'p_start'           => $validated['p_start']  ?? null,
            'p_end'             => $validated['p_end']    ?? null,
            'status'            => $validated['status'],
            'early_bird_status' => $validated['early_bird_status'],
            'currencies'        => $currencyData ?? null,
            'image'             => $validated['image'] ?? null,
            'haspartpayment'    => $validated['haspartpayment'] ?? null,
        ]);
        
        $group->programs()->attach($validated['programs']);
        
        return back()->with('success', 'Group created successfully.');
    }

    public function edit(Group $groupedprogram)
    {
        return redirect()->route('groupedprogram.index', ['edit' => $groupedprogram->id]);
    }

    public function update(Request $request, Group $groupedprogram)
    {
        $validated = $request->validate([
            'p_name'            => 'required|string|max:255',
            'p_abbr'            => 'required|string|max:50',
            'programs'          => 'required|array|min:1',
            'programs.*'        => 'exists:programs,id',
            'p_amount'          => 'required|numeric|min:0',
            'e_amount'          => 'nullable|numeric|min:0',
            'p_start'           => 'nullable|date',
            'p_end'             => 'nullable|date|after_or_equal:p_start',
            'status'            => 'required|boolean',
            'early_bird_status' => 'required|boolean',
            'currencies'        => 'sometimes|array|min:1',
            'currencies.*'      => 'exists:currencies,id',
            'currency_values'   => 'sometimes|array',
            'haspartpayment'    => 'sometimes',
        ]);
        
        if ($request->file('image')) {
            $file = $this->uploadFileToUploads($request->file('image'), 'image', 'trainings', 533, 533);
            $validated['image'] = 'trainingimage/' . $file;
        }else {
            $validated['image'] = $groupedprogram->image;
        }

        if (!empty($validated['currencies'])) {
            $currencyData = collect($validated['currencies'])->map(function ($id) use ($validated) {
                $value = $validated['currency_values'][$id] ?? null;

                return [
                    'id'     => (int) $id,
                    'amount' => $value !== '' ? (float) $value : null,
                ];
            })->toArray();
        }


        $groupedprogram->update([
            'p_name'            => $validated['p_name'],
            'p_abbr'            => $validated['p_abbr'],
            'p_amount'          => $validated['p_amount'],
            'e_amount'          => $validated['e_amount'] ?? null,
            'p_start'           => $validated['p_start']  ?? null,
            'p_end'             => $validated['p_end']    ?? null,
            'status'            => $validated['status'],
            'early_bird_status' => $validated['early_bird_status'],
            'currencies'        => $currencyData ?? null,
            'image'             => $validated['image'] ?? null,
            'haspartpayment'    => $validated['haspartpayment'] ?? null,

        ]);

        // Sync programs (detach all and attach new ones)
        $groupedprogram->programs()->sync($validated['programs']);

        return back()->with('message', 'Group updated successfully');
    }

    public function destroy(Group $groupedprogram)
    {
        $groupedprogram->programs()->detach();

        $groupedprogram->delete();

        return back()->with('message', 'Group deleted successfully.');
    }
}
