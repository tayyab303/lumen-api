<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Utils\UserType;
use App\Models\Property;
use Illuminate\Http\Request;
use App\Utils\HttpStatusCode;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\PropertyResource;
use App\Http\Resources\EmployeesResource;
use App\Http\Resources\PropertiesResource;
use App\Utils\AppConst;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Reserve;

class PropertyController extends Controller
{
    /**
     * private array variable for validation
     * 
     * @return array
     */
    private $_validationRules =[
        'title' => 'required|max:60|min:10',
        'price' => 'required|regex:/^\d+(\.\d{1,3})?$/',
        'price_sqft' => 'required|regex:/^\d+(\.\d{1,3})?$/',
        'latitude' => ['regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
        'longitude' => ['regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
        'city' => 'required',
        'unit_area' => 'required|max:10|min:1',
        'unit_type' => 'required',
        'description' => 'max:500|min:50',
        'address' => 'required|max:150|min:20',
        'zip_code' => 'regex:/^\d{5}([\-]\d{4})?$/',
        'country' => 'required',
        'society' => 'max:60|min:5',
        'image.*' => 'image|mimes:jpeg,png,jpg,gif,svg,bmp|max:4096',
    ];

    /**
     * private array variable for custom messages
     * 
     * @return array
     */
    private $_customMessages =[

        'title.required' => 'Title of the Property is required.',
        'title.min' => 'Title of the Property must be at least 10 characters.',
        'title.max' => 'Title of the Property shouldnot be greater than 60 characters.',

        'price.required' => 'Price of the Property is required',
        'price.regex' => 'Price format is invalid it should be numbers.',

        'price_sqft.required' => 'Price per Square Feet of the Property is required',
        'price_sqft.regex' => 'Price per Square Feet format is invalid it should be numbers.',

        'latitude.regex' => 'Location latitude format is invalid it should be decimal.',
        'longitude.regex' => 'Location longitude format is invalid it should be decimal.',

        'city.required' => 'Please select city for the Property its required.',
        
        'country.required' => 'Please select country for the Property its required.',

        'unit_type.required' => 'Please select Unit type for the Property its required.',
        'unit_area.required' => 'Please Enter Unit Area of Property its required',

        'unit_area.min' => 'Unit Area of the Property must be at least 1 digit.',
        'unit_area.max' => 'Unit Area of the Property shouldnot be greater than 10 digits.',

        'description.min' => 'Description about Property must be at least 50 characters.',
        'description.max' => 'Description about Property shouldnot be greater than 500 characters.',

        'address.required' => 'Please Enter Address of Property its required.',
        'address.min' => 'Address of the Property must be at least 20 characters.',
        'address.max' => 'Address of the Property shouldnot be greater than 150 characters.',

        'zip_code.regex' => 'Zip Code format is invalid it should be number.',

        'society.min' => 'Society name must be at least 10 characters.',
        'society.max' => 'Society name shouldnot be greater than 60 characters.',
    ];
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Property $property)
    {
        //
        $this->_property = $property;
    }

    /**
     * index function to show property list with search.
     * 
     * @param Request $request
     * @param array $search
     * @return json response
     */
    public function index(Request $request)
    {
        $search=[
            'property_type'=>$request->property_type,
            'unit_type'=>$request->unit_type,
            'min_price'=>$request->min_price,
            'max_price'=>$request->max_price,
            'city'=>$request->city,
            'area'=>$request->area,
            'beds'=>$request->beds,
            'country'=>$request->country, // all these parameters for filtering
            'search'=>$request->search, //search parameter is for searching
            'sort'=>['order'=>$request->order,'column'=>$request->column],
            'status'=>$request->status,
            'available'=>$request->available,
            'verify'=>$request->verify,
        ];
        try {
            $result= $this->_property->listProperty($search);
            if(count($result)>0){
                return PropertyResource::collection($result);
            }else{
                return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]], HttpStatusCode::NOT_FOUND);
            }
        }catch(\Exception $e) {
            Log::error('PropertyController -> index: ',$e);
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     *  validate and create property
     * 
     * @param Request $req
     * @return json response
     */
    public function store(Request $req)
    {
        /**
         * validation calling 
         */
        $this->validate($req, $this->_validationRules, $this->_customMessages);
        try {
            $property =$this->_property->createProperty($req);
            return response()->json(['entity' =>$property, 'message'=> HttpStatusCode::$statusTexts[HttpStatusCode::CREATED]],HttpStatusCode::CREATED);
        }catch(\Exception $e) {
            Log::error('PropertyController -> store: ', $e);
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
    * show single  property detail
    * 
    * @param int $id
    * @return json response
    */
    public function show($id)
    {
        $property = $this->_property;
        try {
            if(auth()->user()->type === UserType::COMPANY){
                $property = $this->_property->with('images','categories','reservation')->where('company_id', '=', auth()->user()->company->id)->find($id);
            } else if(auth()->user()->type === UserType::SUPER_EMPLOYEE || auth()->user()->type === UserType::COMPANY_EMPLOYEE) {
                $property = $this->_property->with('images', 'categories')->where('employee_id', '=', auth()->user()->employee->id)->find($id);
            } else {
                $property = $this->_property->with('images','categories')->find($id);
            }
            if($property !== null) {
                return new PropertyResource($this->_property->with('images','categories')->find($id));
            }else {
                return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]], HttpStatusCode::NOT_FOUND);
            }
        }catch(\Exception $e) {
            Log::error('PropertyController -> show: ', $e);
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
    * view single  property detail
    * 
    * @param int $id
    * @return json response
    */
    public function view($id)
    {
        try {
        $property = $this->_property->with('images','categories')->find($id);
        if($property !== null) {
            return new PropertyResource($this->_property->with('images','categories')->find($id));
        }else {
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]], HttpStatusCode::NOT_FOUND);
        }
        }catch(\Exception $e) {
            Log::error('PropertyController -> view: ', $e);
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Edit property
     * 
     *  @param int $id
     * @return json response
     */
    public function edit($id)
    {
        $property = $this->_property->with('images','categories')->find($id);
        if($property == null){
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]], HttpStatusCode::NOT_FOUND);
        }
        try {
            if($property && Auth::user()->can('update', $property))
            {
                return response()->json(['entity' => $property, 'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]], HttpStatusCode::OK);
            }else {
                return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::FORBIDDEN]], HttpStatusCode::FORBIDDEN);
            }
        }catch(\Exception $e) {
            Log::error('PropertyController -> edit: ', $e);
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * update property
     * 
     *  @param Request $req
     *  @param int $id
     * @return json response
     */
    public function update(Request $req, $id)
    {
        $property = $this->_property->find($id);
        if($property == null){
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]], HttpStatusCode::NOT_FOUND);
        }
        /**
         * validation calling 
         */
        $this->validate($req, $this->_validationRules, $this->_customMessages);
        try {
            if($property && Auth::user()->can('update', $property))
            {
                $property= $this->_property->updateProperty($req,$id);
            } else {
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::FORBIDDEN]], HttpStatusCode::FORBIDDEN);
            }
            return response()->json(['entity'=> $property, 'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]], HttpStatusCode::OK);
        }catch(\Exception $e) {
            Log::error('PropertyController -> update: ', $e);
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete property
     * 
     * @param Request $req
     *  @param int $id
     * @return json reponse 
     */
    public function destroy(Request $req,$id)
    {
        $property = $this->_property->find($id);
        if($property == null){
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]], HttpStatusCode::NOT_FOUND);
        }
        try {
            if($property && Auth::user()->can('delete', $property))
            {
                // $property->notify(new PropertyDelete);
                $entity= $property->only(['id']);
                /**
                 * unlinking images related to specified property from public folder
                 * 
                 * @return void
                 */
                $images= Media::where('property_id',$req->id)->get();
                foreach($images as $file){
                    $oldImage = $file->image;
                    if(file_exists($oldImage)){
                        unlink($oldImage);
                    }
                }
                $property->images()->delete();
                /**
                 * detaching details of property from pivot table
                 * @return void
                 */
                $property->categories()->detach();
                $property->delete();
            }
            else {
                return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::FORBIDDEN]], HttpStatusCode::FORBIDDEN);
            }
            return response()->json(['entity'=> $entity, 'message' => 'Deleted! '.HttpStatusCode::$statusTexts[HttpStatusCode::OK]], HttpStatusCode::OK);
        }catch(\Exception $e) {
            Log::error('PropertyController -> destroy: ',$e);
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * employee list for task
     * 
     *  @param Request $request
     * @return json reponse 
     */
    public function employeeList($id)
    {
        $property = $this->_property->find($id);
        if($property == null){
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]], HttpStatusCode::NOT_FOUND);
        }
        $employee = $this->_property->viewEmployee($id);
        try {
            if($property && Auth::user()->can('view', $property)){
            return new EmployeesResource($employee);
        }else {
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::FORBIDDEN]], HttpStatusCode::FORBIDDEN);
        }
        } catch (\Exception $e) {
            Log::error('PropertyController -> employeeList: ', $e);
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**   
     * Assign property to employee
     * 
     * @param Request $request
     * @return var array 
     */
    public function assignProperty(Request $request)
    {
        $property=$this->_property->assignProperty($request);
        return response()->json([ 'entity' => $property, 'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]], HttpStatusCode::OK);
        try {
        } catch (\Exception $e) {
            Log::error('PropertyController -> assign: ', $e);
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
        
    }

    /**
     * function to search property also with filtering
     * @param Request $request
     * @return array $search
     */
    public function search(Request $request)
    {
        $search=[
            'property_type'=>$request->property_type,
            'unit_type'=>$request->unit_type,
            'min_price'=>$request->min_price,
            'max_price'=>$request->max_price,
            'city'=>$request->city,
            'area'=>$request->area,
            'beds'=>$request->beds,
            'country'=>$request->country, // all these parameters for filtering
            'search'=>$request->search, //search parameter is for searching
            'sort'=>['order'=>$request->order,'column'=>$request->column] // sort parameter is for sorting
        ];
        try {
            $result= $this->_property->searchProperty($search);
            if(count($result)>0){
                return PropertyResource::collection($result);
            }else{
                return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]], HttpStatusCode::NOT_FOUND);
            }
        }catch(\Exception $e) {
            Log::error('PropertyController -> search: ',$e);
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * featured property function
     *
     * @return void
     */
    public function featured()
    {
        try {
            $result = $this->_property->latest()->take(AppConst::FEATURED_PROPERTIES)->get();
            return PropertyResource::collection($result);
        } catch (\Exception $e) {
            Log::error('PropertyController -> search: ', $e);
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }
}
