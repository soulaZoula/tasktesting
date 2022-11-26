<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Resources\TasksResource;
use App\Models\Task;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TasksController extends Controller
{
   use HttpResponses;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       return TasksResource::collection(
        Task::where('user_id', Auth::user()->id)->get(),
       );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTaskRequest $request)
    {
        $request->validated($request->all());
        $task = Task::create([
            'user_id' => Auth::user()->id,
            'name' => $request->name,
            'description' => $request->description,
            'priority' => $request->priority,
        ]);

        return new TasksResource($task);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        return $this->isNotAuthorized($task) ? $this->isNotAuthorized($task) : new TasksResource($task);
        //$task = Task::where('id', $id)->first();
       
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
    public function update(Request $request, Task $task)
    {

        if(Auth::user()->id !== $task->user_id){
            return $this->error('','you are not authorized to edit this task', 403);
        }

        $task->update($request->all());

        return new TasksResource($task); 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        
       return $this->isNotAuthorized($task) ? $this->isNotAuthorized($task) : $task->delete();

        // return response(null, 204);
    }

    private function isNotAuthorized($task)
    {
        if(Auth::user()->id !== $task->user_id){
            return $this->error('','you are not authorized to make this request', 403);
        }
    }
}
