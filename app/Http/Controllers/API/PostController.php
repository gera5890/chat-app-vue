<?php

namespace App\Http\Controllers\API;

use App\Events\PostEvent;
use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PostController extends Controller
{
    
    public function index(Request $request)
    {   
        $user = $request->user();
        $posts = Post::with('user','comments.user', 'likes.user')
                        ->withCount('likes', 'comments')
                        ->where('user_id', $user->id)
                        ->orderBy('created_at', 'desc')
                        ->cursorPaginate();

        return response()->json($posts);
    }

    public function publicPosts()
    {   
        $posts = Post::where('visibility', 'public')
                        ->orderBy('created_at', 'desc')
                        ->cursorPaginate();

        

        return response()->json($posts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        if($request->hasFile('image')){
            $request->validate([
                'image' => 'mimes:png,jpg,jpeg'
            ]);

            $image = $request->file('image');
            $path = $image->store('post_images');
        }else{
            $request->validate([
                'text' => 'required|min:5'
            ]);
        }

        $post = Post::create([
            'user_id' => $request->user()->id,
            'text' => $request->text ?? null,
            'image' => $path ?? null,
            'visibility' => $request->visibility ?? 'public'
        ]);

        broadcast(new PostEvent($post));

        return response()->json([
            'message' => 'Post created',
            'data' => $post
        ], Response::HTTP_CREATED);
    }

    public function show(Post $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        //
        $request->validate([
            'visibility' => 'in:public,private'
        ]);

        if($request->text != $post->text && ($request->text != null || $request->text != '' )){
            $request->validate([
                'text' => 'required|min:5'
            ]);
        }

        $post->update($request->only(['text', 'visibility']));

        return response()->json([
            $post
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {   
        $post->delete();
        return response()->json([
            'message' => 'Post deleted successfully'
        ]);
    }
}
