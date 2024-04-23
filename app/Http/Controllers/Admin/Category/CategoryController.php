<?php

namespace App\Http\Controllers\Admin\Category;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    private $db;
    private $category;
    public function __construct(Category $category, DB $db){
        $this->db = $db;
        $this->category = $category;
    }

    public function list(){
        return view('admin.category.list');
    }

    public function index(Request $request){

        $columns = array(
            0 => 'name',
            1 => 'slug',
            2 => 'action',
        );

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $totalData = $totalFiltered = 0;

        if (empty($request->input('search.value'))) {
            $categoryList = $this->category->select(DB::raw('categories.*'));

            $categoryList = $categoryList->get();
            $totalData = $totalFiltered = $categoryList->count();
        } else {
            $search = $request->input('search.value');

            $categoryList = $this->category->select(DB::raw('categories.*'))
                ->where(function($q) use ($search) {
                    $q->where('categories.name', 'LIKE', "%{$search}%")
                        ->orWhere('categories.slug', 'LIKE', "%{$search}%");
                })
                ->offset($start)->limit($limit)->orderBy($order, $dir);

            $totalFiltered =  $this->category->select(DB::raw('categories.*'))
                ->where(function($q) use ($search) {
                    $q->where('categories.name', 'LIKE', "%{$search}%")->orWhere('categories.slug', 'LIKE', "%{$search}%");
                });

            $categoryList = $categoryList->get();
            $totalFiltered = $totalFiltered->count();
        }
        $data = array();
        if (!empty($categoryList)) {
            foreach ($categoryList as $category) {

                $action = '<a title="Edit" href='.route("editCategory", $category->id).'><button type="button" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i>Edit</button></a> '.
                    '<button type="button" data-id="'.$category->id.'" class="btn btn-secondary btn-sm removeCategory"><i class="fas fa-trash-alt"></i></button></a>';

                $categoryData['name'] = $category->name;
                $categoryData['slug'] = $category->slug;
                $categoryData['action'] = $action;
                $data[] = $categoryData;
            }
        }

        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        );

        echo json_encode($json_data);
    }
    public function add(){
        return view('admin.category.add');
    }
    public function store(CategoryRequest $request){
        try {
            $data = [
                'name' => $request->name
            ];

            $category = $this->category->create($data);
                $category->slug = Str::slug($category->name . "-" . $category->id);
                $category->save();

            return redirect(route('categoryList'))->with('message', flashMessage('insert','Category'));
        } catch (Exception $e) {
            return back()->withInput()->withErrors(['errorMessage' => exceptionMessage($e)]);
        }
    }
    public function edit($categoryId){
        try {

            $category = Category::where('id', $categoryId)->first();
            return view('admin.category.edit', compact('category'));

        } catch (Exception $e) {
            echo exceptionMessage($e);
        }
    }

    public function update(CategoryRequest $request, $categoryId){
        try {
            $data = [
                'name' => $request->name
            ];
                $data['slug'] = Str::slug($request->name . "-" . $categoryId);

            $this->category->updateOrCreate(['id' => $categoryId], $data);

            return redirect(route('editCategory', $categoryId))->with('message', flashMessage('update','Category'));
        } catch (Exception $e) {
            echo exceptionMessage($e);
        }
    }

    public function delete($id)
    {
        try {
        $category = Category::findOrFail($id);
        $category->delete();

            return response()->json(['success' => 'true']);
        }
        catch (Exception $e) {
                return back()->withInput()->withErrors(['errorMessage' => exceptionMessage($e)]);
            }
    }
}
