<?php

namespace App\Http\Controllers;

use App\Utils\AppConst;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Utils\HttpStatusCode;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CategoriesResource;


class CategoryController extends Controller
{
    /**
     * validation
     * 
     * @return variable array $_validationRules
     */
    private $_validationRules=[
        'name'=> 'required|string|min:4|max:25|unique:categories',
    ];
    private $_customMessages = [
        'name.unique' => 'This name of category already taken.'
    ];
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Category $category)
    {
        //
        $this->_category = $category;

    }
    
    /**
     * index function to show Category list.
     * @return  Resources CategoriesResources
     * @return json response
     */
    public function index()
    {
        try {
            return new CategoriesResource($this->_category->latest()->paginate(AppConst::PAGE_SIZE));
        }catch(\Exception $e) {
            Log::error('CategoryController -> index: ',$e);
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     *  validate and create category
     * 
     * @param Request $req
     * @return json response with category
     */
    public function store(Request $req)
    {
        $this->validate($req, $this->_validationRules, $this->_customMessages); 
        try {
            $category = $this->_category->addCategory($req);
            return response()->json(['entity' => $category, 'message' => HttpStatusCode::$statusTexts[HttpStatusCode::CREATED]], HttpStatusCode::CREATED);
        }catch(\Exception $e) {
            Log::error('CategoryController -> store: ',$e);
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
    * show single  Category detail
    * 
    * @param int $id
    * @return  Resource CategoryResource
    * @return json response 
    */
    public function show($id)
    {
        try {
            if($this->_category->find($id) != null){
                return new CategoryResource($this->_category->find($id));
            }else{
                return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]], HttpStatusCode::NOT_FOUND);
            }
        }catch(\Exception $e) {
            Log::error('CategoryController -> show: ', $e);
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Edit Category
     * 
     * @param int $id
     * @return Resource CategoryResource
     * @return json response with resources
     */
    public function edit($id)
    {
        $category = $this->_category->with('properties')->find($id);
        if($category == null){
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]], HttpStatusCode::NOT_FOUND);
        }
        try {
            if($category != null){
                return response()->json(['entity' => $category, 'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]], HttpStatusCode::OK);
            }else{
                return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]], HttpStatusCode::NOT_FOUND);
            }
        }catch(\Exception $e) {
            Log::error('CategoryController -> edit: ',$e);
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * update category
     * 
     * @param Request $req, $id
     * @return json response 
     */
    public function update(Request $req, $id)
    {
        $category = $this->_category->find($id);
        if($category == null){
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]], HttpStatusCode::NOT_FOUND);
        }
        $this->validate($req, $this->_validationRules, $this->_customMessages);
        try {
            $category= $this->_category->updateCategory($req,$id);
            return response()->json(['entity'=> $category, 'message' => ' Updated! '. HttpStatusCode::$statusTexts[HttpStatusCode::ACCEPTED]], HttpStatusCode::ACCEPTED);
        }catch(\Exception $e) {
            Log::error('CategoryController -> update: ', $e);
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete category
     * 
     *  @param int $id
     * @return json reponse 
     */
    public function destroy($id)
    {
        $category = $this->_category->find($id);
        if($category== null)
        {
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]], HttpStatusCode::NOT_FOUND);
        }
        try {
            // $name = $category->only(['name']);
            $category->properties()->detach();
            $category->delete();
            return response()->json(["entity" => $category, "message" => ' Deleted!'.HttpStatusCode::$statusTexts[HttpStatusCode::OK]], HttpStatusCode::OK);
        }catch(\Exception $e) {
            Log::error('CategoryController -> destroy: ', $e);
            return response()->json(["message" => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }
}
