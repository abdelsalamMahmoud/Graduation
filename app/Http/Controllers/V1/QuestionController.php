<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQuestionRequest;
use App\Http\Requests\UpdateQuestionRequest;
use App\Models\Question;

class QuestionController extends Controller
{

    public function index()
    {
        //
    }

    public function store(StoreQuestionRequest $request)
    {
        //
    }

    public function show(Question $question)
    {
        //
    }

    public function update(UpdateQuestionRequest $request, Question $question)
    {
        //
    }
    public function destroy(Question $question)
    {
        //
    }
}
