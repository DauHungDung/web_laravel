<?php

namespace App\Http\Controllers;

use App\Http\Requests\Course\DestroyRequest;
use App\Http\Requests\Course\StoreRequest;
use App\Http\Requests\Course\UpdateRequest;
use App\Models\Course;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Yajra\DataTables\DataTables;

class CourseController extends Controller
{
    private Builder $model;

    public function __construct()
    {
        $this->model = (new Course())->query();
        $routeName   = Route::currentRouteName();
        $arr         = explode('.', $routeName);
        $arr         = array_map('ucfirst', $arr);
        $title       = implode(' - ', $arr);

        View::share('title', $title);
    }
//    public function index(Request $request)
//    {
//        $search = $request->get('q');
//        $data = Course::query()
//            ->where('name', 'like', '%'.$search.'%')
//            ->paginate(1);
//        $data->appends(['q' => $search]);
//
//        return view('course.index', [
//           'data' => $data,
//           'search' => $search,
//        ]);
//    }
    public function index()
    {
        return view('course.index');
    }

    public function api()
    {
        return DataTables::of($this->model->withCount('students'))
            ->editColumn('created_at', function ($object) {
                return $object->year_created_at;
            })
            ->addColumn('edit', function ($object) {
                return route('courses.edit', $object);
            })
            ->addColumn('destroy', function ($object) {
                return route('courses.destroy', $object);
            })
            ->make(true);
    }

    public function apiName(Request $request)
    {
        return $this->model
            ->where('name', 'like', '%' . $request->get('q') . '%')
            ->get([
                'id',
                'name',
            ]);
    }

    public function create()
    {
        return view('course.create');
    }


    public function store(StoreRequest $request)
    {
//        $objects = new Course();
//        $objects->fill($request->validated());
//        $objects->save();

        $this->model->create($request->validated());

        return redirect()->route('courses.index');
    }


    public function edit(Course $course)
    {
        return view('course.edit', [
            'course' => $course,
        ]);
    }


    public function update(UpdateRequest $request, $courseId)
    {
        // $this->model->where('id', $courseId)->update(
        //     $request->validated()
        // );

        // $this->model->update(
        //     $request->validated()
        // );

        $object = $this->model->find($courseId);
        $object->fill($request->validated());
        $object->save();

        return redirect()->route('courses.index');
    }


    public function destroy(DestroyRequest $request, $courseId)
    {
//        // $course->delete();
//        Course::destroy($course);
//        // Course::where('id', $course->id)->delete();
//        $arr = [];
//        $arr['status'] = true;
//        $arr['message'] = '';
//
////        return redirect()->route('courses.index');
//        return response($arr, 200);

        $this->model->find($courseId)->delete();
        $this->model->where('id', $courseId)->delete();

        $arr            = [];
        $arr['status']  = true;
        $arr['message'] = '';

        return response($arr, 200);
    }
}
