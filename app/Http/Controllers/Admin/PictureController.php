<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Picture;

class PictureController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!empty(array_intersect(adminRoles(), Auth::user()->role()))) {
            return view('dashboard.admin.pictures.create');
        } else {
            return redirect('/pictures');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    { {
            $data = request()->validate([
                'title' => 'required | min:5',
                'file' => 'required|file|image|mimes:jpg,png,jpeg',
            ]);

            //$imagePath = request('booking_form')->store('/uploads', 'public');
            $imagePath = $request->file->storeAs('uploads/slides', $request->file->getClientOriginalName());
            //$this->storeImage($program);
            Picture::create([
                'title' => $data['title'],
                'file' => $imagePath,
            ]);

            return back()->with('message', 'Image succesfully added');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    { {
            $picture->delete();
            return redirect('pictures')->with('message', 'Picture succesfully deleted');
        }
    }
}
