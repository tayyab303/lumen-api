<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Property;
use App\Utils\AppConst;
use App\Utils\UserType;
use Illuminate\Http\Request;
use App\Utils\HttpStatusCode;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\CommentResource;
use App\Http\Resources\CommentsResource;


class CommentController extends Controller
{
    /**
     * private variable
     * 
     *  @var App\Models\Comment $_comment  
     */
    private $_comment;
    /**
     * array variable for validation
     * 
     */
    private $_validationRules=[
        // 'comment' => 'required|min:10|max:200',
        'status' => 'boolean',
    ];
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Comment $comment)
    {
        $this->_comment = $comment;
    }
    
    /**
     * index function to show Category list.
     *
     * @return json response
     */
    public function index($id)
    {
        try {
        $property = Property :: where('id','=', $id)->first();
        if($property !==null){
            $company_id= $property->company_id;
            $employee_id = $property->employee_id;
        if(auth()->user()->type === UserType::SUPER_ADMIN ||
        auth()->user()->type === UserType::COMPANY && auth()->user()->company !== null && auth()->user()->company->id === $company_id  
        ||auth()->user()->type === UserType::COMPANY_EMPLOYEE && auth()->user()->employee !== null && auth()->user()->employee->id === $employee_id
        || auth()->user()->type === UserType::SUPER_EMPLOYEE && auth()->user()->employee !== null && auth()->user()->employee->id === $employee_id){
            return new CommentsResource($this->_comment->where('property_id', '=', $id )->with('employee.user', 'company.user', 'user')->paginate(AppConst::PAGE_SIZE));
        }else {
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::FORBIDDEN]], HttpStatusCode::FORBIDDEN);
        }
        }
        else{
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]], HttpStatusCode::NOT_FOUND);

        }
    }catch(\Exception $e) {
        Log::error('CommentController -> index: ',$e);
        return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
    }
    }

    /**
     *  validate and create comment
     * 
     * @param Request $req
     * @return json response
     */
    public function store(Request $request, $id)
    {
        $this->validate($request, $this->_validationRules); 
        $comment = $this->_comment->createComment($request,$id);
        if($comment === false){
            return response()->json(['message' => "You are not spotted at property location"], HttpStatusCode::NOT_ACCEPTABLE);
        } else {
            return response()->json(['entity' => $comment, 'message' => HttpStatusCode::$statusTexts[HttpStatusCode::CREATED]], HttpStatusCode::CREATED);
        }
        try {
        }catch(\Exception $e) {
            Log::error('commentController -> store: ',$e);
        }
    }

    // /**
    // * show single  Comment
    // * 
    // * @param int $id
    // * @return json response
    // */
    // public function show($id)
    // {
        // $comment = $this->_comment->where('property_id', '=', $id )->first();
        // $comment['comment']= $this->_comment->where('property_id', '=', $id )->with('employee.user', 'company.user', 'user')->get();
        // $comment['property'] = Property :: where('id','=', $id)->first();
        // // $result =  $comment;
        // if($comment['property'] !==null){
        //     $company_id= $comment['property']->company_id;
        //     $employee_id = $comment['property']->employee_id;
        //     // dd($company_id,$employee_id );
        // if(auth()->user()->type === UserType::SUPER_ADMIN ||
        // auth()->user()->type === UserType::COMPANY && auth()->user()->company !== null && auth()->user()->company->id === $company_id  
        // ||auth()->user()->type === UserType::COMPANY_EMPLOYEE && auth()->user()->employee !== null && auth()->user()->employee->id === $employee_id
        // || auth()->user()->type === UserType::SUPER_EMPLOYEE && auth()->user()->employee !== null && auth()->user()->employee->id === $employee_id){
        //     return $comment;
        // }else {
        //     return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::FORBIDDEN]], HttpStatusCode::FORBIDDEN);
        // }
        // }
        // else{
        //     return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]], HttpStatusCode::NOT_FOUND);

        // }
    //                 try {
    //             }catch(\Exception $e) {
    //                 Log::error('CommentController -> show: ', $e);
    //         return response()->json(['message' => 'ERROR'], HttpStatusCode::CONFLICT);
    //     }
    // }

    // /**
    //  * Edit comment
    //  * 
    //  *  @param int $id
    //  * @return json response
    //  */
    // public function edit($id)
    // {
    //     try {
    //         if($this->_comment->find($id) != null){
    //             // return response()->json(['category' => new CommentResource($this->_comment->find($id)), 'message' => HttpStatusCode::$statusTexts[HttpStatusCode::FOUND]], HttpStatusCode::FOUND);
    //         }else{
    //             return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]], HttpStatusCode::NOT_FOUND);
    //         }
    //     }catch(\Exception $e) {
    //         Log::error('CommentController -> edit: ',$e);
    //         return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
    //     }
    // }

    // /**
    //  * update comment
    //  * 
    //  *  @param Request $req
    //  * @return json response
    //  */
    // public function update(Request $req, $id)
    // {
    //     $comment = $this->_comment->find($id);
    //     if($comment == null){
    //         return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]], HttpStatusCode::NOT_FOUND);
    //     }
    //     $this->validate($req, $this->_validationRules);
    //     try {
    //         $comment= $this->_comment->updateComment($req,$id);
    //         return response()->json(['category'=> $comment, 'message' =>  HttpStatusCode::$statusTexts[HttpStatusCode::ACCEPTED]], HttpStatusCode::ACCEPTED);
    //     }catch(\Exception $e) {
    //         Log::error('CommentController -> update: ', $e);
    //         return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
    //     }
    // }

    // /**
    //  * Delete comment
    //  * 
    //  *  @param int $id
    //  * @return json reponse 
    //  */
    // public function destroy($id)
    // {
    //     try {
    //         $comment = $this->_comment->find($id);
    //         $comment->delete();
    //         return response()->json(["message" => HttpStatusCode::$statusTexts[HttpStatusCode::OK]], HttpStatusCode::OK);
    //     }catch(\Exception $e) {
    //         Log::error('CommentController -> destroy: ', $e);
    //         return response()->json(["message" => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
    //     }
    // }
}
