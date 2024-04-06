<?php

namespace App\Http\Controllers\API;

use App\Events\CommentEvent;
use App\Events\LikeEvent;
use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LikeCommentController extends Controller
{
    //
    public function PostComment(Request $request){
        $request->validate([
            'post_id' => 'required',
            'content' => 'required|min:1|max:250'
        ]);

        $comment = Comment::create([
            'user_id' => $request->user()->id,
            'post_id' => $request->post_id,
            'content' => $request->content
        ]);

        broadcast(new CommentEvent($comment))->toOthers();

        return response()->json([
            $comment
        ], Response::HTTP_CREATED);
    }

    public function LikeUnlike(Request $request, $postId){
        $user = $request->user();
        $exists = Like::where('user_id', $user->id)->where('post_id', $postId)->first();
        
        if($exists){
            $exists->delete();
        }else{
            $type = 'like';
            $like = Like::create([
                'user_id' => $user->id,
                'post_id' => $postId
            ]);
        }

        $data = [
            'type' => $type,
            $like => $exists ? ['like_id' => $exists->id] : $like
        ]; 

        broadcast(new LikeEvent($data))->toOthers();

        return response()->json($data, 201);

    }
}
