<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreVideoRequest;
use App\Http\Requests\V1\UpdateVideoRequest;
use App\Models\Course;
use App\Models\Video;

class VideoController extends Controller
{
    use ApiResopnseTrait;

    public function index($course_id)
    {
        try {
            $videos = Video::where('course_id',$course_id)->paginate(10);
            if($videos->isEmpty())
            {
                return $this->apiResponse(null,'these are no videos yet',200);
            }
            return $this->apiResponse($videos,'these are the course videos',200);
        } catch (\Exception $exception) {
            return $this->apiResponse(null,'please try again',404);
        }
    }

    public function store(StoreVideoRequest $request, $course_id)
    {
        try {

            if (!$request->hasFile('video_path')) {
                return $this->apiResponse(null, 'No video file provided', 400);
            }

            $videoFile = $request->file('video_path');

            $uniqueName = uniqid() . '_' . time() . '.' . $videoFile->getClientOriginalExtension();

            $destinationPath = public_path('videos');

            $videoFile->move($destinationPath, $uniqueName);

            $video = Video::create(array_merge(
                $request->except(['_token', 'video']),
                [
                    'course_id' => $course_id,
                    'video_path' => 'videos/' . $uniqueName,
                ]
            ));

            return $this->apiResponse($video, 'Video Uploaded successfully', 200);

        } catch (\Exception $e) {
            return $this->apiResponse(null, 'Please Try Again', 400);
        }
    }

    public function show($id)
    {
        try {
            $video = Video::find($id);
            return $this->apiResponse($video,'this is the video',200);
        } catch (\Exception $exception) {
            return $this->apiResponse(null,'please try again',404);
        }
    }

    public function update(UpdateVideoRequest $request, $id)
    {
        try {
            $video = Video::find($id);
            $course = Course::find($video->course_id);

            if($course->teacher_id != auth('api')->user()->id)
            {
                return $this->apiResponse(null,'you do not have the permission to update this video',403);
            }

            $data = $request->all();
            $video->update($data);
            return $this->apiResponse($video,'video updated successfully',200);
        } catch (\Exception $exception) {
            return $this->apiResponse(null,'please try again',404);
        }

    }

    public function destroy($id)
    {
        try {
            $video = Video::find($id);
            $course = Course::find($video->course_id);

            if($course->teacher_id != auth('api')->user()->id)
            {
                return $this->apiResponse(null,'you do not have the permission to delete this video',403);
            }

            $video->delete();
            return $this->apiResponse(null,'video deleted successfully',200);
        } catch (\Exception $exception) {
            return $this->apiResponse(null,'please try again',404);
        }

    }
}
