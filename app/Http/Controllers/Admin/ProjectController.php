<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Project;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Requests\StoreProjectRequest;
use Illuminate\Support\Facades\Storage;
use App\Models\Type;

//use Illuminate\Support\Facades\DB;


class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::all();
        return view('admin.project.index ', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $types = Type::all();
        return view('admin.project.create', compact('types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request)
    {
        $form_data = $request->validated();
        $form_data['slug'] = Project::generateSlug($form_data['title']);
        if ($request->hasFile('image')) {
            $path = Storage::put('project_image', $request->image);
            $form_data['image'] = $path;
        }


        $new_project = Project::create($form_data);
        return redirect()->route('admin.project.index')->with("message", "Il progetto $new_project->title e stato creato correttamente");
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {

        return view('admin.project.show', compact('project'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        return view('admin.project.edit', compact('project'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {

        $form_data = $request->validated();
        if ($project->title !== $form_data['title']) {
            $form_data['slug'] = Project::generateSlug($form_data['title']);
            
        }
        if ($request->hasFile('image')) {
            $path = Storage::put('project_image', $request->image);
            $form_data['image'] = $path;
        }
        //     DB::enableQueryLog();
        $project->update($form_data);
        //     $query = DB::getQueryLog();
        //     dd($query);

        return redirect()->route('admin.project.show', $project->slug)->with('message', "The project $project->title has been updated");

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route("admin.project.index")->with('message', "The project $project->title has been deleted");

    }
}
