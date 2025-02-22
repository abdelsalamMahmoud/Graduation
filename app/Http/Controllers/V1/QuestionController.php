<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQuestionRequest;
use App\Http\Requests\UpdateQuestionRequest;
use App\Models\Exam;
use App\Models\Question;

class QuestionController extends Controller
{
    use ApiResopnseTrait;
    public function index($exam_id)
    {
        try {
            $questions = Question::where('exam_id',$exam_id)->paginate(10);
            return $this->apiResponse($questions,'these are all questions',200);
        } catch (\Exception $exception) {
            return $this->apiResponse(null,'please try again',404);
        }
    }

    public function store(StoreQuestionRequest $request ,$exam_id)
    {
        try {
            $question = Question::create(array_merge(
                $request->except(['_token']),
                [
                    'exam_id'=>$exam_id,
                ]
            ));
            return $this->apiResponse($question,'Question Created successfully',200);

        } catch (\Exception $e) {
            return $this->apiResponse(null,'Please Try Again',400);
        }
    }

    public function show($id)
    {
        try {
            $question = Question::find($id);
            return $this->apiResponse($question,'this is the question',200);
        } catch (\Exception $exception) {
            return $this->apiResponse(null,'please try again',404);
        }
    }

    public function update(UpdateQuestionRequest $request, $id)
    {
        try {
            $question = Question::find($id);
            $exam = Exam::find($question->exam_id);

            if($exam->teacher_id != auth('api')->user()->id)
            {
                return $this->apiResponse(null,'you do not have the permission to update this question',200);
            }

            $data = $request->all();
            $question->update($data);
            return $this->apiResponse($question,'Question updated successfully',200);
        } catch (\Exception $exception) {
            return $this->apiResponse(null,'please try again',404);
        }

    }
    public function destroy($id)
    {
        try {
            $question = Question::find($id);
            $exam = Exam::find($question->exam_id);

            if($exam->teacher_id != auth('api')->user()->id)
            {
                return $this->apiResponse(null,'you do not have the permission to delete this question',200);
            }

            $question->delete();
            return $this->apiResponse(null,'Question deleted successfully',200);
        } catch (\Exception $exception) {
            return $this->apiResponse(null,'please try again',404);
        }

    }
}
