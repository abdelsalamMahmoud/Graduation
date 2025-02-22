<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOptionRequest;
use App\Http\Requests\UpdateOptionRequest;
use App\Models\Exam;
use App\Models\Option;
use App\Models\Question;

class OptionController extends Controller
{
    use ApiResopnseTrait;

    public function index($question_id)
    {
        try {
            $options = Option::where('question_id',$question_id)->paginate(4);
            return $this->apiResponse($options,'these are all options',200);
        } catch (\Exception $exception) {
            return $this->apiResponse(null,'please try again',404);
        }
    }

    public function store(StoreOptionRequest $request, $question_id)
    {
        try {
            $options = [];
            foreach ($request->options as $option) {
                $options[] = Option::create([
                    'question_id' => $question_id,
                    'option_text' => $option['option_text'],
                    'is_correct' => $option['is_correct'] ?? false
                ]);
            }
            return $this->apiResponse($options, 'Options Created successfully', 200);
        } catch (\Exception $e) {
            return $this->apiResponse(null, 'Please Try Again', 400);
        }
    }

    public function show($id)
    {
        try {
            $option = Option::find($id);
            return $this->apiResponse($option,'this is the option',200);
        } catch (\Exception $exception) {
            return $this->apiResponse(null,'please try again',404);
        }

    }

    public function update(UpdateOptionRequest $request, $id)
    {
        try {
            $option = Option::find($id);
            $question = Question::find($option->question_id);
            $exam = Exam::find($question->exam_id);

            if($exam->teacher_id != auth('api')->user()->id)
            {
                return $this->apiResponse(null,'you do not have the permission to update this option',200);
            }

            $data = $request->all();
            $option->update($data);
            return $this->apiResponse($option,'Option updated successfully',200);
        } catch (\Exception $exception) {
            return $this->apiResponse(null,'please try again',404);
        }
    }

    public function destroy($id)
    {
        try {
            $option = Option::find($id);
            $question = Question::find($option->question_id);
            $exam = Exam::find($question->exam_id);
            if($exam->teacher_id != auth('api')->user()->id)
            {
                return $this->apiResponse(null,'you do not have the permission to delete this option',200);
            }

            $option->delete();
            return $this->apiResponse(null,'Option deleted successfully',200);
        } catch (\Exception $exception) {
            return $this->apiResponse(null,'please try again',404);
        }
    }

}
