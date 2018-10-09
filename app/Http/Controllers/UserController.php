<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // dd($request->all());
        $pagination = 2;

        // filtering
        $users = User::query();
        // Jika parameter nama di isi, maka lakukan pencarian berdasarkan nama
        if(isset($request->name) AND $request->name != '') {
            // equals
            // $users->where('name', '=', '%'.$request.'%');
            // LIKE
            $users->where('name', 'LIKE', '%'.$request->name.'%');
        }

        $users = $users->paginate($pagination);
        // $users = User::all();

        // Support Pagination
        // $users = User::paginate($pagination);

        // numbering
        $number = 1;

        if( request()->has('page') && request()->get('page') > 1) {
            $number += (request()->get('page') - 1) * $pagination;
        }

        return view('users.index', compact('users', 'number'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'password' => 'confirmed'
        ]);

        // Cara Pertama (Query Builder)
        // User::create($request->only('name', 'email', 'password'));
        
        // Cara Kedua (Eloquent)
        $user = New User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();
        
        return redirect()->route('users.index');
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
        // GET data by id
        $user = User::find($id);

        // return to view
        return view('users.edit', compact('user'));
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
        $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email,'.$id,
            'password' => 'confirmed'
        ]);

        // GET data by id
        $user = User::find($id);

        // Update data
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = $request->password;
        $user->save();

        // return to view
        return redirect()->route('users.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // GET data by id
        User::destroy($id);

        // return to view
        return redirect()->route('users.index');
    }
}