<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExamRequest;
use App\Http\Requests\UpdateExamRequest;
use App\Models\Exam;

class ExamController extends Controller
{
    use ApiResopnseTrait;

    public function index()
    {
        $teacher_id = auth('api')->user()->id;
        try {
            $exams = Exam::with('questions')->where('teacher_id',$teacher_id)->paginate(10);
            return $this->apiResponse($exams,'these are all exams',200);
        } catch (\Exception $exception) {
            return $this->apiResponse(null,'please try again',404);
        }

    }

    public function store(StoreExamRequest $request)
    {
        try {
            $exam = Exam::create(array_merge(
                $request->except(['_token']),
                [
                    'teacher_id'=>auth('api')->user()->id,
                ]
            ));
            return $this->apiResponse($exam,'Exam Created successfully',200);

        } catch (\Exception $e) {
            return $this->apiResponse(null,'Please Try Again',400);
        }
    }

    public function show($id)
    {
        try {
            $exam = Exam::with('questions')->find($id);
            return $this->apiResponse($exam,'this is the exam',200);
        } catch (\Exception $exception) {
            return $this->apiResponse(null,'please try again',404);
        }
    }

    public function update(UpdateExamRequest $request, $id)
    {
        try {
            $exam = Exam::find($id);

            if($exam->teacher_id != auth('api')->user()->id)
            {
                return $this->apiResponse(null,'you do not have the permission to update this exam',200);
            }

            $data = $request->all();
            $exam->update($data);
            return $this->apiResponse($exam,'Exam updated successfully',200);
        } catch (\Exception $exception) {
            return $this->apiResponse(null,'please try again',404);
        }
    }

    public function destroy($id)
    {
        try {
            $exam = Exam::find($id);

            if($exam->teacher_id != auth('api')->user()->id)
            {
                return $this->apiResponse(null,'you do not have the permission to delete this exam',200);
            }

            $exam->delete();
            return $this->apiResponse(null,'exam deleted successfully',200);
        } catch (\Exception $exception) {
            return $this->apiResponse(null,'please try again',404);
        }

    }
}
