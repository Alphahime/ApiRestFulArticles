<?php

namespace App\Http\Controllers\API\v1;

use Exception;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::latest('publish_date')->get();

        if ($articles->isEmpty()) {
            return response()->json(
                [
                   'status' => Response::HTTP_NOT_FOUND,
                   'message' => 'Article empty'
                ], 
                Response::HTTP_NOT_FOUND
            );
        } else {
            return response()->json(
                [
                    'data' => $articles->map(function($article) {
                        return [
                            'title' => $article->title,
                            'content' => $article->content,
                            'publish_date' => $article->publish_date
                        ];
                    }),
                    'message' => 'Liste des articles',
                    'status' => Response::HTTP_OK
                ], 
                Response::HTTP_OK
            );
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'content' => 'required',
            'publish_date' => 'required|date'
        ]);
        
        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
        }

        try {
            $article = Article::create([
                'title' => $request->title,
                'content' => $request->content,
                'publish_date' => $request->publish_date,
            ]);

            return response()->json([
                'status' => Response::HTTP_CREATED,
                'message' => 'Article created successfully',
                'data' => $article
            ], Response::HTTP_CREATED);
        } catch (Exception $e) {
            Log::error('Error storing data: ' . $e->getMessage());

            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Failed to store data'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'content' => 'required',
            'publish_date' => 'required|date'
        ]);
        
        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
        }

        try {
            $article = Article::findOrFail($id);

            $article->update([
                'title' => $request->title,
                'content' => $request->content,
                'publish_date' => $request->publish_date,
            ]);

            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Article updated successfully',
                'data' => $article
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Error updating data: ' . $e->getMessage());

            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Failed to update data'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        try {
            $article = Article::findOrFail($id);
            $article->delete();

            return response()->json([
                'status' => Response::HTTP_NO_CONTENT,
                'message' => 'Article deleted successfully'
            ], Response::HTTP_NO_CONTENT);
        } catch (Exception $e) {
            Log::error('Error deleting data: ' . $e->getMessage());

            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Failed to delete data'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
