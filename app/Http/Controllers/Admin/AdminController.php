<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;

use App\Models\SiteInfo as Setting;
use App\Models\Swiper;


use SweetAlert;
use Alert;
use Log;
use Carbon\Carbon;

class AdminController extends Controller
{
    //
    public function index(){
        $setting = Setting::first();
    
        if (!$setting || empty($setting->favicon) || empty($setting->site_name) || empty($setting->logo) || empty($setting->description)) {
            return view('admin.siteSettings', [
                'setting' => $setting
            ]);
        }
    
        return view('admin.home');
    }

    public function swipers(){
        $swipers = Swiper::all();
        $setting = Setting::first();
        return view('admin.swipers', [
            'swipers' => $swipers,
            'setting' => $setting,
        ]);
    }

    //GLOBAL SITE SETTINGS LOGIC
    public function siteSettings(){
        $setting = Setting::first();
        return view('admin.siteSettings', [
            'setting' => $setting,
        ]);
    }

    public function updateSiteInfo(Request $request){
        $validator = Validator::make($request->all(), [
            'logo' => 'nullable|image',
            'favicon' => 'nullable|image',
            'description' => 'nullable|string',
            'site_name' => 'nullable|string',
            'swiper_video_only' => 'nullable|boolean',
        ]);
    
        if ($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }
    
        $siteInfo = new Setting;
        if(!empty($request->site_info_id) && !$siteInfo = Setting::find($request->site_info_id)){
            alert()->error('Oops', 'Invalid Site Information')->persistent('Close');
            return redirect()->back();
        }
    
        if (!empty($request->site_name)) {
            $siteInfo->site_name = $request->site_name;
        }
    
        if (!empty($request->description)) {
            $siteInfo->description = $request->description;
        }

        $siteInfo->swiper_video_only = $request->has('swiper_video_only') ? true : false;
    
        // Save logo
        $logoUrl = null;
        if ($request->hasFile('logo')) {
            $logoUrl = 'uploads/siteInfo/' .'logo'.'.'.$request->file('logo')->getClientOriginalExtension();
            $logo = $request->file('logo')->move('uploads/siteInfo', $logoUrl);
            $siteInfo->logo = $logoUrl;
        }
    
        // Save favicon
        $faviconUrl = null;
        if ($request->hasFile('favicon')) {
            $faviconUrl = 'uploads/siteInfo/' .'favicon'.'.'.$request->file('favicon')->getClientOriginalExtension();
            $favicon = $request->file('favicon')->move('uploads/siteInfo', $faviconUrl);
            $siteInfo->favicon = $faviconUrl;
        }
    
        if($siteInfo->save()){
            alert()->success('Changes Saved', 'Site information changes saved successfully')->persistent('Close');
            return redirect()->back();
        }
    
        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    // public function addSwiper(Request $request){
    //     $validator = Validator::make($request->all(), [
    //         'title' => 'nullable|string|max:255',
    //         'subtitle' => 'nullable|string|max:255',
    //         'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
    //         'video_url' => 'nullable|url',
    //         'slides_text' => 'nullable|array',
    //         'slides_text.*.title' => 'required_with:slides_text|string|max:255',
    //         'slides_text.*.subtitle' => 'required_with:slides_text|string|max:255',
    //     ]);

    //     if ($validator->fails()) {
    //         alert()->error('Validation Error', $validator->messages()->first())->persistent('Close');
    //         return redirect()->back();
    //     }

    //     $swiper = new Swiper();

    //     if ($request->video_url) {
    //         // Video mode
    //         if (empty($request->slides_text)) {
    //             alert()->error('Error', 'You must provide at least one title/subtitle for the video.')->persistent('Close');
    //             return redirect()->back();
    //         }

    //         $swiper->video_url = $request->video_url;
    //         $swiper->is_video = true;
    //         $swiper->slides_text = $request->slides_text;
    //         $swiper->image = null; // clear image if video
    //     } elseif ($request->hasFile('image')) {
    //         // Image mode
    //         $swiper->title = $request->title;
    //         $swiper->subtitle = $request->subtitle;

    //         $imagePath = cloudinary()->uploadFile($request->file('image')->getRealPath())->getSecurePath();
    //         $swiper->image = $imagePath;
    //         $swiper->is_video = false;
    //         $swiper->video_url = null;
    //         $swiper->slides_text = null;
    //     } else {
    //         alert()->error('Error', 'Please provide either an image or a video URL.')->persistent('Close');
    //         return redirect()->back();
    //     }

    //     $swiper->save();
    //     alert()->success('Success', 'Swiper slide added successfully')->persistent('Close');
    //     return redirect()->back();
    // }
    
    // public function editSwiper(Request $request){
    //     $validator = Validator::make($request->all(), [
    //         'swiper_id' => 'required|exists:swipers,id',
    //         'title' => 'nullable|string|max:255',
    //         'subtitle' => 'nullable|string|max:255',
    //         'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
    //         'video_url' => 'nullable|url',
    //         'slides_text' => 'nullable|array',
    //         'slides_text.*.title' => 'required_with:slides_text|string|max:255',
    //         'slides_text.*.subtitle' => 'required_with:slides_text|string|max:255',
    //     ]);

    //     if ($validator->fails()) {
    //         alert()->error('Validation Error', $validator->messages()->first())->persistent('Close');
    //         return redirect()->back();
    //     }

    //     $swiper = Swiper::find($request->swiper_id);

    //     if (!$swiper) {
    //         alert()->error('Not Found', 'The swiper slide you are trying to edit does not exist.')->persistent('Close');
    //         return redirect()->back();
    //     }

    //     if ($request->video_url) {
    //         $swiper->video_url = $request->video_url;
    //         $swiper->is_video = true;
    //         $swiper->slides_text = $request->slides_text ?? $swiper->slides_text;
    //         $swiper->image = null;
    //         $swiper->title = null;
    //         $swiper->subtitle = null;
    //     } elseif ($request->hasFile('image')) {
    //         try {
    //             $imagePath = cloudinary()->uploadFile($request->file('image')->getRealPath())->getSecurePath();
    //             $swiper->image = $imagePath;
    //             $swiper->is_video = false;
    //             $swiper->video_url = null;
    //             $swiper->slides_text = null;
    //             $swiper->title = $request->title;
    //             $swiper->subtitle = $request->subtitle;
    //         } catch (\Exception $e) {
    //             alert()->error('Image Upload Failed', 'Could not upload image: ' . $e->getMessage())->persistent('Close');
    //             return redirect()->back();
    //         }
    //     } else {
    //         // Only updating text for image swiper
    //         if (!$swiper->is_video) {
    //             $swiper->title = $request->title;
    //             $swiper->subtitle = $request->subtitle;
    //         }
    //     }

    //     $swiper->save();
    //     alert()->success('Success', 'Swiper updated successfully')->persistent('Close');
    //     return redirect()->back();
    // }

    public function addSwiper(Request $request){
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'video_url' => 'nullable|url',
            'slides_text' => 'nullable|array',
            'slides_text.*.title' => 'required_with:slides_text|string|max:255',
            'slides_text.*.subtitle' => 'required_with:slides_text|string|max:255',
        ]);

        if ($validator->fails()) {
            alert()->error('Validation Error', $validator->messages()->first())->persistent('Close');
            return redirect()->back();
        }

        $swiper = new Swiper();

        if ($request->video_url) {
            // Video mode
            if (empty($request->slides_text)) {
                alert()->error('Error', 'You must provide at least one title/subtitle for the video.')->persistent('Close');
                return redirect()->back();
            }

            // Convert YouTube watch URL to embed URL
            $url = $request->video_url;
            if (preg_match("/watch\?v=([a-zA-Z0-9_-]+)/", $url, $matches)) {
                $embedUrl = "https://www.youtube.com/embed/" . $matches[1];
                $swiper->video_url = $embedUrl;
            } else {
                $swiper->video_url = $url; // fallback for already embed URLs or other links
            }

            $swiper->is_video = true;
            $swiper->slides_text = $request->slides_text;
            $swiper->image = null; // clear image if video
            $swiper->title = null;
            $swiper->subtitle = null;
        } elseif ($request->hasFile('image')) {
            // Image mode
            $swiper->title = $request->title;
            $swiper->subtitle = $request->subtitle;

            $imagePath = cloudinary()->uploadFile($request->file('image')->getRealPath())->getSecurePath();
            $swiper->image = $imagePath;
            $swiper->is_video = false;
            $swiper->video_url = null;
            $swiper->slides_text = null;
        } else {
            alert()->error('Error', 'Please provide either an image or a video URL.')->persistent('Close');
            return redirect()->back();
        }

        $swiper->save();
        alert()->success('Success', 'Swiper slide added successfully')->persistent('Close');
        return redirect()->back();
    }

    public function editSwiper(Request $request){
        $validator = Validator::make($request->all(), [
            'swiper_id' => 'required|exists:swipers,id',
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'video_url' => 'nullable|url',
            'slides_text' => 'nullable|array',
            'slides_text.*.title' => 'required_with:slides_text|string|max:255',
            'slides_text.*.subtitle' => 'required_with:slides_text|string|max:255',
        ]);

        if ($validator->fails()) {
            alert()->error('Validation Error', $validator->messages()->first())->persistent('Close');
            return redirect()->back();
        }

        $swiper = Swiper::find($request->swiper_id);

        if (!$swiper) {
            alert()->error('Not Found', 'The swiper slide you are trying to edit does not exist.')->persistent('Close');
            return redirect()->back();
        }

        if ($request->video_url) {
            // Convert YouTube watch URL to embed URL
            $url = $request->video_url;
            if (preg_match("/watch\?v=([a-zA-Z0-9_-]+)/", $url, $matches)) {
                $embedUrl = "https://www.youtube.com/embed/" . $matches[1];
                $swiper->video_url = $embedUrl;
            } else {
                $swiper->video_url = $url; // fallback for already embed URLs or other links
            }

            $swiper->is_video = true;
            $swiper->slides_text = $request->slides_text ?? $swiper->slides_text;
            $swiper->image = null;
            $swiper->title = null;
            $swiper->subtitle = null;
        } elseif ($request->hasFile('image')) {
            try {
                $imagePath = cloudinary()->uploadFile($request->file('image')->getRealPath())->getSecurePath();
                $swiper->image = $imagePath;
                $swiper->is_video = false;
                $swiper->video_url = null;
                $swiper->slides_text = null;
                $swiper->title = $request->title;
                $swiper->subtitle = $request->subtitle;
            } catch (\Exception $e) {
                alert()->error('Image Upload Failed', 'Could not upload image: ' . $e->getMessage())->persistent('Close');
                return redirect()->back();
            }
        } else {
            // Only updating text for image swiper
            if (!$swiper->is_video) {
                $swiper->title = $request->title;
                $swiper->subtitle = $request->subtitle;
            }
        }

        $swiper->save();
        alert()->success('Success', 'Swiper updated successfully')->persistent('Close');
        return redirect()->back();
    }

    public function deleteSwiper(Request $request){
        $validator = Validator::make($request->all(), [
            'swiper_id' => 'required|exists:swipers,id',
        ]);

        if ($validator->fails()) {
            alert()->error('Error', $validator->messages()->first())->persistent('Close');
            return redirect()->back();
        }

        $swiper = Swiper::find($request->swiper_id);
        if ($swiper->delete()) {
            alert()->success('Deleted', 'Swiper deleted successfully')->persistent('Close');
        } else {
            alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        }

        return redirect()->back();
    }



}
