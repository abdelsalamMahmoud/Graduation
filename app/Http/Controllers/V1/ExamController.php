<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExamRequest;
use App\Http\Requests\SubmitExamRequest;
use App\Http\Requests\UpdateExamRequest;
use App\Models\Exam;
use App\Models\Option;
use App\Models\StudentAnswer;
use App\Models\StudentExam;

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

    public function submit_exam(SubmitExamRequest $request)
    {
        try {
            $student_id = auth('api')->user()->id;
            $exam_id = $request->exam_id;
            $answers = $request->answers;

            $correct_count = 0;
            $totalQuestions = count($answers);

            foreach ($answers as $answer) {
                $question_id = $answer['question_id'];
                $option_id = $answer['option_id'];

                // Store the student's answer
                StudentAnswer::create([
                    'student_id' => $student_id,
                    'exam_id' => $exam_id,
                    'question_id' => $question_id,
                    'option_id' => $option_id,
                ]);

                // Check if the answer is correct
                $isCorrect = Option::where('id', $option_id)->where('is_correct', true)->exists();
                if ($isCorrect) {
                    $correct_count++;
                }
            }

            // Calculate score percentage
            $score = ($correct_count / $totalQuestions) * 100;

            // Store exam completion record
            $studentExam = StudentExam::updateOrCreate(
                ['student_id' => $student_id, 'exam_id' => $exam_id],
                ['score' => round($score)]
            );

            $data = [
                'correct_answers' => $correct_count,
                'total_questions' => $totalQuestions,
                'score' => round($score, 2) . '%',
                'student_exam' => $studentExam
            ];

            return $this->apiResponse($data, 'Exam submitted successfully', 200);
        } catch (\Exception $e) {
            return $this->apiResponse(null, 'An error occurred: ' . $e->getMessage(), 500);
        }
    }


}
